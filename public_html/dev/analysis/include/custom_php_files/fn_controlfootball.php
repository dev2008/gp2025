<?php
if(!defined('custom_page_from_inclusion')) { die(); }

/**
 * fn_controlfootball.php
 * Admin batch runner for League Report imports & related sections
 * - Lists candidate uploads (NFL/NCAA)
 * - Lets admin choose an upload and optionally force rerun specific steps
 * - Shows a bitmask legend of which sections are already completed
 * - Uses per-section files + functions via a registry
 *
 * Requires: fn_sections_bootstrap.php (defines flags & helpers)
 */

require_once __DIR__ . '/fn_sections_bootstrap.php';

$_cp_title      = "League Report Control";
$_cp_action     = isset($_POST['_cp_action']) ? $_POST['_cp_action'] : '';
$_cp_upload_id  = isset($_POST['_cp_upload_id']) ? (int)$_POST['_cp_upload_id'] : 0;
$_cp_files_dir  = isset($upload_directory) ? $upload_directory : null;

// Force checkboxes
$_cp_force_league    =  !empty($_POST['_cp_force_league']);
$_cp_force_team       = !empty($_POST['_cp_force_team']);
$_cp_force_roster     = !empty($_POST['_cp_force_roster']);
$_cp_force_pbp        = !empty($_POST['_cp_force_pbp']);
$_cp_force_h2h        = !empty($_POST['_cp_force_h2h']);
$_cp_force_standings  = !empty($_POST['_cp_force_standings']);
$_cp_force_scout      = !empty($_POST['_cp_force_scout']);

// near the top of fn_controlfootball.php, after reading POST:
if (!empty($_POST['_cp_debug']) && !defined('_CP_DEBUG')) {
    define('_CP_DEBUG', true);
}

// UI helper
function _cp_flag_badge($on) {
    return $on
        ? '<span class="w3-tag w3-green w3-round w3-small">✔</span>'
        : '<span class="w3-tag w3-gray w3-round w3-small">—</span>';
}

echo '<div class="w3-container w3-padding">';
echo '<h3 class="w3-text-black">'.htmlspecialchars($_cp_title).'</h3>';
echo '<div class="w3-small w3-text-grey">';
echo 'upload_directory: <code>'.htmlspecialchars((string)$_cp_files_dir).'</code>';
echo '</div>';

// Process branch
if ($_cp_action === 'process' && $_cp_upload_id > 0) {

    // Section registry: flag, file, function, label, force var
    $sections = [
		['flag'=>_CP_FLAG_LEAGUE, 	  'file'=>'fn_leaguereport.php',   'func'=>'_cp_process_league_report',   'label'=>'League Report',   'force'=>$_cp_force_league],
        ['flag'=>_CP_FLAG_TEAM,       'file'=>'fn_teamreport.php',     'func'=>'_cp_process_team_report',     'label'=>'Team Report',     'force'=>$_cp_force_team],
        ['flag'=>_CP_FLAG_ROSTER,     'file'=>'fn_roster.php',         'func'=>'_cp_process_roster',          'label'=>'Roster',          'force'=>$_cp_force_roster],
        ['flag'=>_CP_FLAG_PBP,        'file'=>'fn_playbyplay.php',     'func'=>'_cp_process_play_by_play',    'label'=>'Play by Play',    'force'=>$_cp_force_pbp],
        ['flag'=>_CP_FLAG_H2H,        'file'=>'fn_headtohead.php',     'func'=>'_cp_process_head_to_head',    'label'=>'Head to Head',    'force'=>$_cp_force_h2h],
        ['flag'=>_CP_FLAG_STANDINGS,  'file'=>'fn_standings.php',      'func'=>'_cp_process_standings',       'label'=>'Standings',       'force'=>$_cp_force_standings],
        ['flag'=>_CP_FLAG_SCOUT,      'file'=>'fn_scouting.php',       'func'=>'_cp_process_scouting_report', 'label'=>'Scouting Report', 'force'=>$_cp_force_scout],
    ];

    try {
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch current processed mask
        $st = $conn->prepare("SELECT processed FROM g_uploads WHERE upload_id=:id LIMIT 1");
        $st->execute([':id'=>$_cp_upload_id]);
        $processed = (int)$st->fetchColumn();
        
        $_cp_summary = [
		'run'     => [],   // labels that actually ran
		'skipped' => [],   // labels skipped due to bit already set
		'forced'  => [],   // labels that were forced (bit cleared then ran)
		'errors'  => [],   // ['label' => 'message']
		'per_sec' => [],   // 'Label' => seconds
		'total_s' => 0.0,
		];
		$_cp_t0 = microtime(true);

        // Legend
        echo '<div class="w3-section w3-small">';
        echo '<div><b>Section status (upload_id '.(int)$_cp_upload_id.')</b></div>';
        echo '<div>League Report '    . _cp_flag_badge(_cp_has_flag($processed, _CP_FLAG_LEAGUE))    . '</div>';
        echo '<div>Team Report '      . _cp_flag_badge(_cp_has_flag($processed, _CP_FLAG_TEAM))      . '</div>';
        echo '<div>Roster '           . _cp_flag_badge(_cp_has_flag($processed, _CP_FLAG_ROSTER))    . '</div>';
        echo '<div>Play by Play '     . _cp_flag_badge(_cp_has_flag($processed, _CP_FLAG_PBP))       . '</div>';
        echo '<div>Head to Head '     . _cp_flag_badge(_cp_has_flag($processed, _CP_FLAG_H2H))       . '</div>';
        echo '<div>Standings '        . _cp_flag_badge(_cp_has_flag($processed, _CP_FLAG_STANDINGS)) . '</div>';
        echo '<div>Scouting Report '  . _cp_flag_badge(_cp_has_flag($processed, _CP_FLAG_SCOUT))     . '</div>';
        echo '</div>';

        // Optionally clear bits for forced re-run (except League)
		$clearStmt = $conn->prepare("UPDATE g_uploads SET processed = COALESCE(processed,0) & ~:flag WHERE upload_id=:id LIMIT 1");
		foreach ($sections as $s) {
		if (!empty($s['force'])) {
				$clearStmt->execute([':flag'=>$s['flag'], ':id'=>$_cp_upload_id]);
				$_cp_summary['forced'][] = $s['label'];
			}
		}
        // refresh mask if any forced
		if ($_cp_force_league || $_cp_force_team || $_cp_force_roster || $_cp_force_pbp || $_cp_force_h2h || $_cp_force_standings || $_cp_force_scout) {
			$st->execute([':id'=>$_cp_upload_id]);
			$processed = (int)$st->fetchColumn();
		}


        // Run steps (skip if bit already set and not forced)
		foreach ($sections as $s) {
			$already = _cp_has_flag($processed, $s['flag']);
			$label   = $s['label'];

			if ($already && empty($s['force'])) {
				echo '<div class="w3-panel w3-pale-green w3-border">Skip — '.$label.' (already done)</div>';
				$_cp_summary['skipped'][] = $label;
				continue;
			}

			$forcedNote = !empty($s['force']) ? ' (forced)' : '';
			echo '<div class="w3-panel w3-pale-blue w3-border">Step — '
				. htmlspecialchars($label) . $forcedNote . '…</div>';


			$path = __DIR__ . '/' . $s['file'];
			if (!is_file($path)) {
				$msg = 'Missing processor file: '.$s['file'];
				echo '<div class="w3-panel w3-amber w3-border">'.htmlspecialchars($msg).'</div>';
				$_cp_summary['errors'][$label] = $msg;
				continue;
			}
			require_once $path;

			if (!function_exists($s['func'])) {
				$msg = 'Missing function '.$s['func'].' in '.$s['file'];
				echo '<div class="w3-panel w3-amber w3-border">'.htmlspecialchars($msg).'</div>';
				$_cp_summary['errors'][$label] = $msg;
				continue;
			}

			$sec_t0 = microtime(true);
			try {
				// processors may return an array with optional stats, but it's fine if they return void
				$ret = call_user_func($s['func'], $conn, $_cp_upload_id, $_cp_files_dir);
				$_cp_summary['run'][] = $label;

				// optional: if a processor returns stats, store them
				if (is_array($ret) && isset($ret['stats'])) {
					$_cp_summary['per_sec'][$label.'_stats'] = $ret['stats'];
				}

				$st->execute([':id'=>$_cp_upload_id]);
				$processed = (int)$st->fetchColumn();

			} catch (Throwable $e) {
				$msg = $e->getMessage();
				echo '<div class="w3-panel w3-red"><b>Error:</b> '.htmlspecialchars($msg).'</div>';
				$_cp_summary['errors'][$label] = $msg;
			} finally {
				$_cp_summary['per_sec'][$label] = round(microtime(true) - $sec_t0, 3);
			}
		}
		$_cp_summary['total_s'] = round(microtime(true) - $_cp_t0, 3);


    } catch (Exception $e) {
        echo '<div class="w3-panel w3-red"><b>DB Exception:</b> '.htmlspecialchars($e->getMessage()).'</div>';
    }

		echo '</div>';
		if (!defined('_CP_DEBUG') || _CP_DEBUG === false) {
		echo '<div class="w3-panel w3-white w3-card w3-margin-top">';
		echo '<h4 class="w3-margin-top">Summary</h4>';

		$ran      = implode(', ', $_cp_summary['run']) ?: '—';
		$skipped  = implode(', ', $_cp_summary['skipped']) ?: '—';
		$forced   = implode(', ', $_cp_summary['forced']) ?: '—';
		$hasErr   = !empty($_cp_summary['errors']);

		echo '<div class="w3-row-padding w3-small">';
		echo '  <div class="w3-third"><b>Ran:</b> '.htmlspecialchars($ran).'</div>';
		echo '  <div class="w3-third"><b>Skipped:</b> '.htmlspecialchars($skipped).'</div>';
		echo '  <div class="w3-third"><b>Forced:</b> '.htmlspecialchars($forced).'</div>';
		echo '</div>';

		// timings
		echo '<div class="w3-section w3-small">';
		echo '<b>Timings</b> (s): ';
		$pairs = [];
		foreach ($_cp_summary['per_sec'] as $k => $v) {
			if (substr($k, -6) === '_stats') continue;
			$pairs[] = htmlspecialchars($k).': '.$v;
		}
		echo $pairs ? implode(' &middot; ', $pairs) : '—';
		echo '<div class="w3-text-grey">Total: '.$_cp_summary['total_s'].' s</div>';
		echo '</div>';

		if ($hasErr) {
			echo '<div class="w3-panel w3-pale-red w3-border"><b>Errors</b><ul class="w3-ul w3-small">';
			foreach ($_cp_summary['errors'] as $lbl => $msg) {
				echo '<li><b>'.htmlspecialchars($lbl).':</b> '.htmlspecialchars($msg).'</li>';
			}
			echo '</ul></div>';
		}
		
		// League Report row sanity check (only when stats were returned)
		if (!empty($_cp_summary['per_sec']['League Report_stats'])) {
			$lr = $_cp_summary['per_sec']['League Report_stats'];
			$act = (int)($lr['actual_rows'] ?? 0);
			$exp = $lr['expected_rows']; // may be null if unknown prefix
			$ok  = !empty($lr['ok']);
			echo '<div class="w3-section w3-small"><b>League Report rows</b>: ';
			if ($exp === null) {
				// Unknown prefix; just show actual
				echo '<span class="w3-tag w3-light-gray w3-round">actual '.$act.'</span>';
			} else {
				// Compare against expected; colorize
				$cls = $ok ? 'w3-green' : 'w3-red';
				echo '<span class="w3-tag '.$cls.' w3-round">'.$act.' / expected '.$exp.'</span>';
				if (!$ok) {
					echo ' <span class="w3-text-red">Mismatch detected</span>';
				}
			}
			echo '</div>';
		}


		echo '</div>'; // end summary card
	}

    return;
}

// ------- Selection UI -------

try {
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
        SELECT `upload_id`, `filename`, `league`, `season`, `week`, `mytimestamp`, `processed`
        FROM `g_uploads`
        WHERE `filename` LIKE '%NFL%' OR `filename` LIKE '%NCAA%'
        ORDER BY `mytimestamp` DESC, `upload_id` DESC
        LIMIT 300
    ";
    $st = $conn->prepare($sql);
    $st->execute();
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) {
        echo '<div class="w3-panel w3-pale-yellow w3-border">No matching uploads found.</div>';
        echo '</div>';
        return;
    }

    echo '<form method="post" class="w3-margin-top">';
    echo '<input type="hidden" name="_cp_action" value="process">';

    echo '<div class="w3-responsive">';
    echo '<table class="w3-table w3-bordered w3-small">';
    echo '<tr class="w3-light-grey"><th></th><th>Upload ID</th><th>Filename</th><th>League</th><th>Season</th><th>Week</th><th>Timestamp</th><th>Processed</th></tr>';
    foreach ($rows as $r) {
        echo '<tr>';
        echo '<td><input class="w3-radio" type="radio" name="_cp_upload_id" value="'.(int)$r['upload_id'].'"></td>';
        echo '<td>'.(int)$r['upload_id'].'</td>';
        echo '<td>'.htmlspecialchars($r['filename']).'</td>';
        echo '<td>'.htmlspecialchars((string)$r['league']).'</td>';
        echo '<td>'.htmlspecialchars((string)$r['season']).'</td>';
        echo '<td>'.htmlspecialchars((string)$r['week']).'</td>';
        echo '<td>'.htmlspecialchars($r['mytimestamp']).'</td>';
        echo '<td>'.htmlspecialchars((string)$r['processed']).'</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</div>';

    // Force rerun options
    echo '<div class="w3-section">';
    echo '<div><b>Force rerun (optional)</b></div>';
    echo '<label><input class="w3-check" type="checkbox" name="_cp_force_league"> League Report</label><br>';
    echo '<label><input class="w3-check" type="checkbox" name="_cp_force_team"> Team Report</label><br>';
    echo '<label><input class="w3-check" type="checkbox" name="_cp_force_roster"> Roster</label><br>';
    echo '<label><input class="w3-check" type="checkbox" name="_cp_force_pbp"> Play by Play</label><br>';
    echo '<label><input class="w3-check" type="checkbox" name="_cp_force_h2h"> Head to Head</label><br>';
    echo '<label><input class="w3-check" type="checkbox" name="_cp_force_standings"> Standings</label><br>';
    echo '<label><input class="w3-check" type="checkbox" name="_cp_force_scout"> Scouting Report</label>';
    echo '</div>';

    echo '<div class="w3-margin-top">';
    echo '<button class="w3-button w3-blue">Process Selected Upload</button>';
    echo '</div>';

    echo '</form>';

} catch (Exception $e) {
    echo '<div class="w3-panel w3-red"><b>DB Exception:</b> '.htmlspecialchars($e->getMessage()).'</div>';
}

echo '</div>';
