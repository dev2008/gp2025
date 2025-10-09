<?php
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'f_functions.php';

/**
 * fn_playbyplay.php — RESET VERSION (stable build)
 *
 * - Parse from “1st Quarter” to “<E><ST.66><L>”
 * - Insert only lines that match the full pattern and have formation not P/F
 * - Skip KO/KR lines entirely
 * - Yardage rule: if a_poss is blank => (prev_field - this_field), else 999
 * - Penalty => a_penalty=1 and a_yards=0
 * - Present editable grid for a_yards=999; on save update and call nz_pbp()
 */

if (!defined('_CP_PBP_SOFT_DEBUG')) define('_CP_PBP_SOFT_DEBUG', false);

/** Entry point used by the control page */
function _cp_process_play_by_play(PDO $conn, int $upload_id, string $files_dir) {
    echo '<div class="w3-container"><div class="w3-small w3-text-grey">Play by Play</div>';

    // --- Handle POST back from the a_yards=999 editor
    if (!empty($_POST['_cp_pbp_save']) && isset($_POST['_cp_edit']) && is_array($_POST['_cp_edit'])) {
        _cp_pbp_handle_save($conn, $_POST['_cp_edit']);
        _cp_pbp_call_nz($conn, $upload_id);
        echo '<div class="w3-panel w3-green">Edits saved and nz_pbp() executed (if available).</div>';
        echo '</div>';
        return;
    }

    // --- Load upload row
    $st = $conn->prepare("SELECT * FROM `g_uploads` WHERE `upload_id`=:id LIMIT 1");
    $st->execute([':id'=>$upload_id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if (!$row) { echo '<div class="w3-panel w3-red">Upload not found.</div></div>'; return null; }

    $filename = $row['filename'];
    $fullpath = rtrim($files_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
    if (!is_readable($fullpath)) { echo '<div class="w3-panel w3-red">File not readable: '.htmlspecialchars($fullpath).'</div></div>'; return null; }
    $raw = file_get_contents($fullpath);
    if ($raw === false || $raw === '') { echo '<div class="w3-panel w3-red">File empty.</div></div>'; return null; }

    // --- Header extraction (league / season / week / level)
    $hdrLeague = null; $hdrSeason = null; $hdrWeek = null; $hdrLevel = 'Advanced';
    if (preg_match('/GAMEPLAN\s*\((?P<level>[^)]+)\)\s+League\s+(?P<league>[A-Z0-9]+)\s+Season\s+(?P<season>\d{4})\s+Week\s+(?P<week>\d{1,2})/i', $raw, $hm)) {
        $hdrLeague = strtoupper($hm['league']);
        $hdrSeason = (int)$hm['season'];
        $hdrWeek   = (int)$hm['week'];
        $hdrLevel  = trim($hm['level']);
    } else {
        // Fallback to filename pattern
        if (preg_match('/^(?P<league>[A-Z0-9]+)-[A-Z]+_s(?P<season>\d{4})_w(?P<week>\d{1,2})_/i', $filename, $fm)) {
            $hdrLeague = strtoupper($fm['league']);
            $hdrSeason = (int)$fm['season'];
            $hdrWeek   = (int)$fm['week'];
        }
    }
    if (preg_match('/^NCAA6/i', (string)$hdrLeague)) $hdrLevel = 'Basic';
    $a_type = (stripos((string)$hdrLeague, 'NCAA') === 0) ? 'College' : 'Pro';

    // --- Slice region from “1st Quarter” to end marker
    $startPos = _cp_pbp_pos_first_quarter($raw);
    if ($startPos === null) { echo '<div class="w3-panel w3-amber">Could not find "1st Quarter".</div></div>'; return null; }
    $endPos = strpos($raw, '<E><ST.66><L>', $startPos);
    if ($endPos === false) $endPos = strlen($raw);
    $block = substr($raw, $startPos, $endPos - $startPos);
    $block = str_replace(["\r\n", "\r"], "\n", $block);
    $lines = preg_split('/\n+/', $block);

    // --- Patterns
    $formationSet = '(?:A|B|C|D|E|F|G|H|I|J|K|L|O|P|S|T|U|W|X|Z)';
    $re_play = '/^\s*'
        .'(?:(\d{1,2}):(\d{2}))\s+'                // 1,2 minutes:seconds
        .'([A-Z]{2})?\s*'                          // 3 poss (optional)
        .'(\d{1,2})\s+'                             // 4 field
        .'(1st|2nd|3rd|4th)\s+and\s+(\d{1,2}|goal)\s+' // 5 down, 6 distance
        .'(' . $formationSet . ')\s+'              // 7 form
        .'([A-Z]{2})\s+'                           // 8 ocall
        .'([A-Z]{2})\s+'                           // 9 dcall
        .'(.+?)\s*$'                               // 10 text
        .'/i';

    $re_ko   = '/^\s*(\d{1,2}):(\d{2})\s+[A-Z]{0,2}\s*KO\s+KR\b/i';

    // --- Track two teams from poss field
    $teamSeen = [];
    $prev_off = '';
    $prev_field = null;

    // --- Insert statement
    $sqlIns = "
        INSERT INTO `n_playbyplay` (
          a_type,a_level,a_league,a_season,a_week,
          a_minutes,a_seconds,a_poss,a_off,a_def,
          a_field,a_down,a_distance,a_form,a_ocall,a_dcall,
          a_yards,
          a_team1,a_team2,a_text,
          a_td,a_first,a_fumble,a_intercept,a_penalty,a_sack,
          a_hurry,a_blitzpickup,a_blitznopickup,a_safety,
          a_security,a_incomplete,a_peno,a_pend,a_bado,a_twodowns,
          a_situationno,a_negative,a_playtype,n_offcoach,n_defcoach
        ) VALUES (
          :a_type,:a_level,:a_league,:a_season,:a_week,
          :a_minutes,:a_seconds,:a_poss,:a_off,:a_def,
          :a_field,:a_down,:a_distance,:a_form,:a_ocall,:a_dcall,
          :a_yards,
          :a_team1,:a_team2,:a_text,
          :a_td,:a_first,:a_fumble,:a_intercept,:a_penalty,:a_sack,
          :a_hurry,:a_blitzpickup,:a_blitznopickup,:a_safety,
          :a_security,:a_incomplete,:a_peno,:a_pend,:a_bado,:a_twodowns,
          :a_situationno,:a_negative,:a_playtype,:n_offcoach,:n_defcoach
        )
    ";
    $ins = $conn->prepare($sqlIns);

    $rowsInserted = 0;
    $N = count($lines);

    for ($i=0; $i<$N; $i++) {
        $line = trim($lines[$i]);
        if ($line === '') continue;
        if (strpos($line, '<E><ST.66><L>') !== false) break; // end of game
        if (preg_match($re_ko, $line)) continue;             // skip kickoffs entirely
        if (!preg_match($re_play, $line, $m)) continue;      // only accept full matches

        // Extract fields
        $minutes = (int)$m[1];
        $seconds = (int)$m[2];
        $possRaw = isset($m[3]) ? strtoupper($m[3]) : '';
        $field   = (int)$m[4];

        $downWord= $m[5];
        $downMap = ['1st'=>1,'2nd'=>2,'3rd'=>3,'4th'=>4];
        $down    = $downMap[$downWord] ?? 0;

        $distRaw = strtolower($m[6]);
        $distance = ($distRaw === 'goal') ? 10 : (int)$distRaw;

        $form = strtoupper($m[7]);
        $ocall = strtoupper($m[8]);
        $dcall = strtoupper($m[9]);

        // Skip formations P and F (punt & field-goal pages) unless you decide later to include “fake”
        if ($form === 'P' || $form === 'F') {
            $prev_field = $field;
            if ($possRaw !== '') $prev_off = $possRaw;
            if ($possRaw && !in_array($possRaw, $teamSeen, true)) $teamSeen[] = $possRaw;
            continue;
        }

        // Play text (strip markup)
        $text = trim(preg_replace('/<[^>]*>/', '', $m[10]));

        // Append next line if it’s plain text (not a new play/KO or tag), to capture wrap lines
        if ($i+1 < $N) {
            $next = trim($lines[$i+1]);
            if ($next !== '' && !preg_match($re_play, $next) && !preg_match($re_ko, $next) && $next[0] !== '<') {
                $text2 = trim(preg_replace('/<[^>]*>/', '', $next));
                if ($text2 !== '') $text .= ' ' . $text2;
            }
        }

        // Team sides
        $a_poss = $possRaw;                 // leave a_poss as-is (often blank)
        if ($a_poss !== '') {               // Off follows poss if present
            $a_off = $a_poss;
            $prev_off = $a_off;
        } else {
            $a_off = $prev_off;
        }
        if ($a_off && !in_array($a_off, $teamSeen, true)) $teamSeen[] = $a_off;

        $a_def = '';
        if (count($teamSeen) === 2) {
            $a_def = ($teamSeen[0] === $a_off) ? $teamSeen[1] : $teamSeen[0];
        }

        // Yardage rule:
        // - If a_poss is blank => yards = prev_field - this_field
        // - Otherwise 999 (to be hand-edited later)
        $yards = 999;
        if ($a_poss === '' && $prev_field !== null) {
            $yards = (int)$prev_field - (int)$field;
        }

        // Flags & derived
        $flags = _cp_pbp_simple_flags($text);
        if ($flags['penalty'] === 1) {
            $yards = 0;
        }
        $a_first = ($distance > 0 && $yards >= $distance) ? 1 : 0;
        $a_td    = ((int)$field === (int)$yards) ? 1 : 0;

        // Bind & insert (cast ints to satisfy strict mode)
        $params = [
            'a_type'   => $a_type,
            'a_level'  => $hdrLevel ?: 'Advanced',
            'a_league' => $hdrLeague ?: '',
            'a_season' => $hdrSeason ?: 0,
            'a_week'   => $hdrWeek ?: 0,

            'a_minutes'=> (int)$minutes,
            'a_seconds'=> (int)$seconds,
            'a_poss'   => $a_poss,
            'a_off'    => $a_off ?: '',
            'a_def'    => $a_def ?: '',

            'a_field'  => (int)$field,
            'a_down'   => (int)$down,
            'a_distance'=> (int)$distance,
            'a_form'   => substr(preg_replace('/[^A-Z]/i','',$form),0,3),
            'a_ocall'  => substr(preg_replace('/[^A-Z]/i','',$ocall),0,3),
            'a_dcall'  => substr(preg_replace('/[^A-Z]/i','',$dcall),0,3),

            'a_yards'  => (int)$yards,

            'a_team1'  => $teamSeen[0] ?? '',
            'a_team2'  => $teamSeen[1] ?? '',

            'a_text'   => mb_substr($text, 0, 1024, 'UTF-8'),

            'a_td'     => (int)$a_td,
            'a_first'  => (int)$a_first,
            'a_fumble' => (int)$flags['fumble'],
            'a_intercept'=> (int)$flags['intercept'],
            'a_penalty'=> (int)$flags['penalty'],
            'a_sack'   => (int)$flags['sack'],
            'a_hurry'  => (int)$flags['hurry'],
            'a_blitzpickup'   => (int)$flags['blitzpickup'],
            'a_blitznopickup' => (int)$flags['blitznopickup'],
            'a_safety' => (int)$flags['safety'],

            'a_security'  => isset($_SESSION['logged_user_infos_ar']['username_user'])
                             ? mb_substr((string)$_SESSION['logged_user_infos_ar']['username_user'],0,255,'UTF-8')
                             : null,
            'a_incomplete'=> (int)$flags['incomplete'],
            'a_peno'      => (int)$flags['peno'],
            'a_pend'      => (int)$flags['pend'],
            'a_bado'      => null,
            'a_twodowns'  => null,
            'a_situationno'=> null,
            'a_negative'  => ($yards < 0) ? 1 : 0,
            'a_playtype'  => null,
            'n_offcoach'  => null,
            'n_defcoach'  => null,
        ];

        $ins->execute(_cp_pbp_bind($params));
        $rowsInserted++;

        // Update context for next line
        $prev_field = $field;
        if ($possRaw !== '') $prev_off = $possRaw;
    }

	echo "<p>Finished import</p>";
	nz_999(0);
	echo "<p>Finished function</p>";

    echo '<div class="w3-small">Inserted rows: '.(int)$rowsInserted.'</div>';

    // Mark processed bit for PBP (bitmask flag)
    if (defined('_CP_FLAG_PBP')) {
        try {
            $u = $conn->prepare("UPDATE g_uploads SET processed = COALESCE(processed,0) | :flag WHERE upload_id=:id LIMIT 1");
            $u->execute([':flag'=>_CP_FLAG_PBP, ':id'=>$upload_id]);
        } catch (Throwable $e) {
            echo '<div class="w3-panel w3-amber">Processed flag not updated: '.htmlspecialchars($e->getMessage()).'</div>';
        }
    }

    // Render the 999 editor
    if ($hdrLeague && $hdrSeason && $hdrWeek) {
        _cp_pbp_render_editable_999($conn, $hdrLeague, $hdrSeason, $hdrWeek);
    } else {
        echo '<div class="w3-panel w3-amber">Missing league/season/week; cannot show 999 editor.</div>';
    }

    echo '</div>'; // container
    return ['stats'=>['rows'=>$rowsInserted]];
}

/* ======================= Helpers ======================= */

function _cp_pbp_pos_first_quarter(string $raw): ?int {
    if (preg_match('/<B>\s*1st Quarter<[^>]*>/', $raw, $m, PREG_OFFSET_CAPTURE)) {
        return $m[0][1] + strlen($m[0][0]);
    }
    return null;
}

function _cp_pbp_bind(array $p): array {
    $out = [];
    foreach ($p as $k=>$v) { $out[':'.$k] = $v; }
    return $out;
}

/** crude flags from text */
function _cp_pbp_simple_flags(string $text): array {
    $t = strtolower($text);
    $has = function($pattern) use ($t) { return preg_match($pattern, $t) ? 1 : 0; };
    return [
        'td'            => $has('/\btouchdown\b/'),
        'fumble'        => $has('/\bfumble\b/'),
        'intercept'     => $has('/\bintercept(ed|ion)\b/'),
        'penalty'       => $has('/\bpenalty\b/'),
        'sack'          => $has('/\bsack(ed)?\b/'),
        'hurry'         => $has('/\bhurr(y|ied)\b/'),
        'blitzpickup'   => $has('/\bblitz picked\b/'),
        'blitznopickup' => $has('/\bblitz not picked\b/'),
        'safety'        => $has('/\bsafety\b/'),
        'incomplete'    => $has('/\bincomplete\b/') || $has('/\bthrown away\b/'),
        'peno'          => $has('/penalty against offence/'),
        'pend'          => $has('/penalty against defence/'),
    ];
}

/** Render grid for a_yards = 999 and allow edit */
function _cp_pbp_render_editable_999(PDO $conn, string $league, int $season, int $week): void {
    $q = $conn->prepare("SELECT a_id,a_minutes,a_seconds,a_poss,a_off,a_def,a_field,a_down,
        a_distance,a_form,a_ocall,a_dcall,a_yards,a_text
        FROM n_playbyplay
        WHERE a_league=:l AND a_season=:s AND a_week=:w AND a_yards=999
        ORDER BY a_id");
    $q->execute([':l'=>$league, ':s'=>$season, ':w'=>$week]);
    $rows = $q->fetchAll(PDO::FETCH_ASSOC);

    echo '<h4 class="w3-margin-top">Plays requiring yard fixes (a_yards = 999)</h4>';
    if (!$rows) { echo '<div class="w3-panel w3-pale-green">No rows to fix.</div>'; return; }

    echo '<form method="post" class="w3-margin-top">';
    echo '<input type="hidden" name="_cp_pbp_save" value="1">';
    echo '<div class="w3-responsive"><table class="w3-table w3-bordered w3-small">';
    echo '<tr class="w3-light-grey"><th>ID</th><th>Min</th><th>Sec</th><th>Poss</th><th>Off</th><th>Def</th>'
        .'<th>Field</th><th>Down</th><th>Dist</th><th>Form</th><th>OCall</th><th>DCall</th><th>Yards</th>'
        .'<th style="width:40%">Text</th></tr>';

    foreach ($rows as $r) {
        $id = (int)$r['a_id'];
        echo '<tr>';
        echo '<td>'.$id.'</td>';

        $fields = ['a_minutes','a_seconds','a_poss','a_off','a_def','a_field','a_down',
                   'a_distance','a_form','a_ocall','a_dcall','a_yards'];
        foreach ($fields as $f) {
            $val = $r[$f];
            $isNum = is_numeric($val);
            $type = $isNum ? 'number' : 'text';
            $extra = $isNum ? ' step="1"' : '';
            echo '<td><input class="w3-input w3-border" type="'.$type.'" name="_cp_edit['.$id.']['.$f.']" value="'.htmlspecialchars((string)$val).'"'.$extra.'></td>';
        }

        echo '<td><textarea class="w3-input w3-border" rows="2" name="_cp_edit['.$id.'][a_text]">'.htmlspecialchars((string)$r['a_text']).'</textarea></td>';
        echo '</tr>';
    }

    echo '</table></div>';
    echo '<div class="w3-section"><button class="w3-button w3-blue">Save yard fixes &amp; post-process</button></div>';
    echo '</form>';
}

/** Handle posted edits from the 999 grid */
function _cp_pbp_handle_save(PDO $conn, array $edit): void {
    $sql = "UPDATE n_playbyplay SET
                a_minutes=:a_minutes,
                a_seconds=:a_seconds,
                a_poss=:a_poss,
                a_off=:a_off,
                a_def=:a_def,
                a_field=:a_field,
                a_down=:a_down,
                a_distance=:a_distance,
                a_form=:a_form,
                a_ocall=:a_ocall,
                a_dcall=:a_dcall,
                a_yards=:a_yards,
                a_text=:a_text,
                a_negative = CASE WHEN :a_yards < 0 THEN 1 ELSE 0 END
            WHERE a_id=:a_id
            LIMIT 1";
    $up = $conn->prepare($sql);

    foreach ($edit as $id => $row) {
        $a_id = (int)$id;
        $params = [
            ':a_id'      => $a_id,
            ':a_minutes' => (int)($row['a_minutes'] ?? 0),
            ':a_seconds' => (int)($row['a_seconds'] ?? 0),
            ':a_poss'    => (string)($row['a_poss'] ?? ''),
            ':a_off'     => (string)($row['a_off'] ?? ''),
            ':a_def'     => (string)($row['a_def'] ?? ''),
            ':a_field'   => (int)($row['a_field'] ?? 0),
            ':a_down'    => (int)($row['a_down'] ?? 0),
            ':a_distance'=> (int)($row['a_distance'] ?? 0),
            ':a_form'    => substr(preg_replace('/[^A-Z]/i','',(string)($row['a_form'] ?? '')),0,3),
            ':a_ocall'   => substr(preg_replace('/[^A-Z]/i','',(string)($row['a_ocall'] ?? '')),0,3),
            ':a_dcall'   => substr(preg_replace('/[^A-Z]/i','',(string)($row['a_dcall'] ?? '')),0,3),
            ':a_yards'   => (int)($row['a_yards'] ?? 0),
            ':a_text'    => mb_substr((string)($row['a_text'] ?? ''), 0, 1024, 'UTF-8'),
        ];
        $up->execute($params);
    }
}

/** Call nz_pbp if available */
function _cp_pbp_call_nz(PDO $conn, int $upload_id): void {
    // Try to recover league/season/week from any row we just edited/inserted for this upload.
    // Simpler: read them from the last non-null in n_playbyplay for latest week of this league from upload header.
    // We’ll re-derive from g_uploads filename if needed.
    try {
        $st = $conn->prepare("SELECT filename FROM g_uploads WHERE upload_id=:id LIMIT 1");
        $st->execute([':id'=>$upload_id]);
        $fn = (string)$st->fetchColumn();
        $league = null; $season = null; $week = null;
        if ($fn && preg_match('/^(?P<league>[A-Z0-9]+)-[A-Z]+_s(?P<season>\d{4})_w(?P<week>\d{1,2})_/i', $fn, $m)) {
            $league = strtoupper($m['league']); $season = (int)$m['season']; $week = (int)$m['week'];
        }

        @require_once __DIR__ . '/g_functions.php';
        if (function_exists('nz_pbp') && $league && $season && $week) {
            nz_pbp($league, $season, $week);
        }
    } catch (Throwable $e) {
        // Soft-fail silently; this step is optional
    }
}
