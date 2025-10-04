<?php
if(!defined('custom_page_from_inclusion')) { die(); }

// Strongly prefer explicit path to avoid include_path ambiguity
$__cp_lr_path = __DIR__ . '/fn_leaguereport.php';

$_cp_title      = "League Report Control";
$_cp_action     = isset($_POST['_cp_action']) ? $_POST['_cp_action'] : '';
$_cp_upload_id  = isset($_POST['_cp_upload_id']) ? (int)$_POST['_cp_upload_id'] : 0;

// DaDaBIK exposes $upload_directory — show it so we know it's set
$_cp_files_dir  = isset($upload_directory) ? $upload_directory : null;

echo '<div class="w3-container w3-padding">';
echo '<h3 class="w3-text-black">'.htmlspecialchars($_cp_title).'</h3>';

// always show environment hints
echo '<div class="w3-small w3-text-grey">';
echo 'upload_directory: <code>'.htmlspecialchars((string)$_cp_files_dir).'</code><br>';
echo 'leaguereport path: <code>'.htmlspecialchars($__cp_lr_path).'</code>';
echo '</div>';

if ($_cp_action === 'process' && $_cp_upload_id > 0) {
    if (!is_file($__cp_lr_path)) {
        echo '<div class="w3-panel w3-red"><b>Error:</b> Cannot find fn_leaguereport.php at '
            .htmlspecialchars($__cp_lr_path).'</div></div>';
        return;
    }
    require_once $__cp_lr_path;

    if (!function_exists('_cp_process_league_report')) {
        echo '<div class="w3-panel w3-red"><b>Error:</b> _cp_process_league_report() not found after include.</div></div>';
        return;
    }

    echo '<div class="w3-panel w3-pale-blue w3-border">Processing upload_id '
        .(int)$_cp_upload_id.' …</div>';

require_once __DIR__ . '/fn_sections_bootstrap.php';

$sections = [
    ['flag'=>_CP_FLAG_LEAGUE,     'file'=>'fn_leaguereport.php',   'func'=>'_cp_process_league_report', 'label'=>'League Report'],
    ['flag'=>_CP_FLAG_TEAM,       'file'=>'fn_teamreport.php',     'func'=>'_cp_process_team_report',   'label'=>'Team Report'],
    ['flag'=>_CP_FLAG_ROSTER,     'file'=>'fn_roster.php',         'func'=>'_cp_process_roster',        'label'=>'Roster'],
    ['flag'=>_CP_FLAG_PBP,        'file'=>'fn_playbyplay.php',     'func'=>'_cp_process_play_by_play',  'label'=>'Play by Play'],
    ['flag'=>_CP_FLAG_H2H,        'file'=>'fn_headtohead.php',     'func'=>'_cp_process_head_to_head',  'label'=>'Head to Head'],
    ['flag'=>_CP_FLAG_STANDINGS,  'file'=>'fn_standings.php',      'func'=>'_cp_process_standings',     'label'=>'Standings'],
    ['flag'=>_CP_FLAG_SCOUT,      'file'=>'fn_scouting.php',       'func'=>'_cp_process_scouting_report','label'=>'Scouting Report'],
];

// get current mask
$st = $conn->prepare("SELECT processed FROM g_uploads WHERE upload_id=:id LIMIT 1");
$st->execute([':id'=>$_cp_upload_id]);
$processed = (int)$st->fetchColumn();

foreach ($sections as $s) {
    if (_cp_has_flag($processed, $s['flag'])) {
        echo '<div class="w3-panel w3-pale-green w3-border">Skip — '.$s['label'].' (already done)</div>';
        continue;
    }
    echo '<div class="w3-panel w3-pale-blue w3-border">Step — '.$s['label'].'…</div>';

    // include processor file
    $path = __DIR__ . '/' . $s['file'];
    if (!is_file($path)) {
        echo '<div class="w3-panel w3-amber w3-border">Missing processor file: '.htmlspecialchars($s['file']).'</div>';
        continue;
    }
    require_once $path;

    if (!function_exists($s['func'])) {
        echo '<div class="w3-panel w3-amber w3-border">Missing function '.$s['func'].' in '.$s['file'].'</div>';
        continue;
    }

    // run the step
    call_user_func($s['func'], $conn, $_cp_upload_id, $_cp_files_dir);

    // refresh mask (in case the step set its flag)
    $st->execute([':id'=>$_cp_upload_id]);
    $processed = (int)$st->fetchColumn();
}
    echo '</div>';
    return;
}

try {
    // ensure PDO throws exceptions (so catch shows a panel)
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
        SELECT `upload_id`, `filename`, `league`, `season`, `week`, `mytimestamp`, `processed`
        FROM `g_uploads`
        WHERE (`filename` LIKE '%NFL%' OR `filename` LIKE '%NCAA%') AND  `processed` <>999
        ORDER BY `mytimestamp` DESC, `upload_id` DESC
        LIMIT 200
    ";
    $st = $conn->prepare($sql);
    $ok = $st->execute();

    if (!$ok) {
        echo '<div class="w3-panel w3-red"><b>DB Error:</b> execute() returned false.</div></div>';
        return;
    }

    $rows = $st->fetchAll(PDO::FETCH_ASSOC);
    $count = is_array($rows) ? count($rows) : 0;

    echo '<div class="w3-panel w3-pale-green w3-border">Query returned <b>'.$count.'</b> row(s).</div>';

    if ($count === 0) {
        echo '<div class="w3-panel w3-pale-yellow w3-border">No matching uploads found for LIKE %NFL% or %NCAA%.</div>';
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
    echo '<div class="w3-margin-top">';
    echo '<button class="w3-button w3-blue">Process League Report</button>';
    echo '</div>';
    echo '</form>';

} catch (Exception $e) {
    echo '<div class="w3-panel w3-red"><b>DB Exception:</b> '.htmlspecialchars($e->getMessage()).'</div>';
}

echo '</div>';
