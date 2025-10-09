<?php
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'f_functions.php';

/**
 * fn_playbyplay.php — PBP import with look-ahead yardage and P/F/KO insertion
 *
 * - Parse from “1st Quarter” to “<E><ST.66><L>”
 * - Insert plays matching the full pattern (including P/F), and KO lines
 * - KO rows: form=X, ocall=KO, dcall=KR, yards=0
 * - P/F rows: yards=0
 * - Yardage rule (current → next):
 *     For each NORMAL play (any formation including P/F present in feed),
 *     look ahead to next matched play; if next is KO skip yard calc; if next shows
 *     same offense (or doesn’t show poss) use (current_field - next_field) as yards.
 *   This ensures the yards belong to the CURRENT row, and works when the next play
 *   is a P or F formation.
 * - Penalty ⇒ a_penalty=1 and a_yards=0
 * - Editor to fix rows with a_yards=999, then call nz_pbp()
 */

if (!defined('_CP_PBP_SOFT_DEBUG')) define('_CP_PBP_SOFT_DEBUG', false);

/** Entry used by control page */
function _cp_process_play_by_play(PDO $conn, int $upload_id, string $files_dir) {
    echo '<div class="w3-container"><div class="w3-small w3-text-grey">Play by Play</div>';

    // --- Handle POST from 999 editor
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

    // --- Header (league/season/week/level)
    $hdrLeague = null; $hdrSeason = null; $hdrWeek = null; $hdrLevel = 'Advanced';
    if (preg_match('/GAMEPLAN\s*\((?P<level>[^)]+)\)\s+League\s+(?P<league>[A-Z0-9]+)\s+Season\s+(?P<season>\d{4})\s+Week\s+(?P<week>\d{1,2})/i', $raw, $hm)) {
        $hdrLeague = strtoupper($hm['league']);
        $hdrSeason = (int)$hm['season'];
        $hdrWeek   = (int)$hm['week'];
        $hdrLevel  = trim($hm['level']);
    } else {
        if (preg_match('/^(?P<league>[A-Z0-9]+)-[A-Z]+_s(?P<season>\d{4})_w(?P<week>\d{1,2})_/i', $filename, $fm)) {
            $hdrLeague = strtoupper($fm['league']);
            $hdrSeason = (int)$fm['season'];
            $hdrWeek   = (int)$fm['week'];
        }
    }
    if (preg_match('/^NCAA6/i', (string)$hdrLeague)) $hdrLevel = 'Basic';
    $a_type = (stripos((string)$hdrLeague, 'NCAA') === 0) ? 'College' : 'Pro';

    // --- Slice “1st Quarter”..end marker
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
        .'(?:(\d{1,2}):(\d{2}))\s+'                    // 1,2 mm:ss
        .'([A-Z]{2})?\s*'                              // 3 poss (optional)
        .'(\d{1,2})\s+'                                 // 4 field
        .'(1st|2nd|3rd|4th)\s+and\s+(\d{1,2}|goal)\s+'  // 5 down, 6 dist
        .'(' . $formationSet . ')\s+'                  // 7 form
        .'([A-Z]{2})\s+'                               // 8 ocall
        .'([A-Z]{2})\s+'                               // 9 dcall
        .'(.+?)\s*$'                                   // 10 text
        .'/i';

    $re_ko = '/^\s*'
        .'(?:(\d{1,2}):(\d{2}))\s+'                    // 1,2 mm:ss
        .'([A-Z]{2})?\s*'                              // 3 poss (optional)
        .'KO\s+KR\b'                                   // kickoff marker
        .'(.*)$'                                       // 4 tail text (optional)
        .'/i';

    // --- Track teams via offense
    $teamSeen = [];     // up to two initials
    $prev_off = '';     // last known offense

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

        // --- Kickoff line: insert with yards=0
        if (preg_match($re_ko, $line, $k)) {
            $minutes = (int)$k[1];
            $seconds = (int)$k[2];
            $possRaw = isset($k[3]) ? strtoupper($k[3]) : '';
            $text = trim(preg_replace('/<[^>]*>/', '', (string)($k[4] ?? '')));
            $a_poss = $possRaw;
            if ($a_poss !== '') { $a_off = $a_poss; $prev_off = $a_off; } else { $a_off = $prev_off; }
            if ($a_off && !in_array($a_off, $teamSeen, true)) $teamSeen[] = $a_off;
            $a_def = '';
            if (count($teamSeen) === 2) {
                $a_def = ($teamSeen[0] === $a_off) ? $teamSeen[1] : $teamSeen[0];
            }

            $params = [
                'a_type'   => (stripos((string)$hdrLeague, 'NCAA') === 0) ? 'College' : 'Pro',
                'a_level'  => $hdrLevel ?: 'Advanced',
                'a_league' => $hdrLeague ?: '',
                'a_season' => $hdrSeason ?: 0,
                'a_week'   => $hdrWeek ?: 0,

                'a_minutes'=> $minutes,
                'a_seconds'=> $seconds,
                'a_poss'   => $a_poss,
                'a_off'    => $a_off ?: '',
                'a_def'    => $a_def ?: '',

                'a_field'  => 0,
                'a_down'   => 0,
                'a_distance'=> 0,
                'a_form'   => 'X',     // not to collide with real formation
                'a_ocall'  => 'KO',
                'a_dcall'  => 'KR',

                'a_yards'  => 0,

                'a_team1'  => $teamSeen[0] ?? '',
                'a_team2'  => $teamSeen[1] ?? '',
                'a_text'   => mb_substr($text, 0, 1024, 'UTF-8'),

                'a_td'     => 0,
                'a_first'  => 0,
                'a_fumble' => 0,
                'a_intercept'=> 0,
                'a_penalty'=> 0,
                'a_sack'   => 0,
                'a_hurry'  => 0,
                'a_blitzpickup'   => 0,
                'a_blitznopickup' => 0,
                'a_safety' => 0,

                'a_security'  => isset($_SESSION['logged_user_infos_ar']['username_user'])
                                 ? mb_substr((string)$_SESSION['logged_user_infos_ar']['username_user'],0,255,'UTF-8')
                                 : null,
                'a_incomplete'=> 0,
                'a_peno'      => 0,
                'a_pend'      => 0,
                'a_bado'      => null,
                'a_twodowns'  => null,
                'a_situationno'=> null,
                'a_negative'  => 0,
                'a_playtype'  => null,
                'n_offcoach'  => null,
                'n_defcoach'  => null,
            ];
            $ins->execute(_cp_pbp_bind($params));
            $rowsInserted++;
            continue;
        }

        // --- Normal play
        if (!preg_match($re_play, $line, $m)) continue;

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

        // Play text (strip markup)
        $text = trim(preg_replace('/<[^>]*>/', '', $m[10]));
        // One-line wrap continuation
        if ($i+1 < $N) {
            $next = trim($lines[$i+1]);
            if ($next !== '' && !preg_match($re_play, $next) && !preg_match($re_ko, $next) && $next[0] !== '<') {
                $text2 = trim(preg_replace('/<[^>]*>/', '', $next));
                if ($text2 !== '') $text .= ' ' . $text2;
            }
        }

        // Possession/off/def
        $a_poss = $possRaw;  // keep as-is
        if ($a_poss !== '') { $a_off = $a_poss; $prev_off = $a_off; } else { $a_off = $prev_off; }
        if ($a_off && !in_array($a_off, $teamSeen, true)) $teamSeen[] = $a_off;
        $a_def = '';
        if (count($teamSeen) === 2) {
            $a_def = ($teamSeen[0] === $a_off) ? $teamSeen[1] : $teamSeen[0];
        }

        // ===== Yardage (current → next) =====
        // If this is P or F, we will insert with yards=0. Still, we allow a previous play to
        // compute its yards using our field (because we don't skip P/F).
        $yards       = 999;
        if ($form === 'P' || $form === 'F') {
            $yards = 0;
        } else {
            $nextField   = null;
            $nextPossRaw = null;

            for ($j = $i + 1; $j < $N; $j++) {
                $ln = trim($lines[$j]);
                if ($ln === '') continue;
                if (strpos($ln, '<E><ST.66><L>') !== false) break;
                if ($ln[0] === '<') continue;               // tags/summaries
                if (preg_match($re_ko, $ln)) {              // KO is a boundary; don't derive across
                    break;
                }
                if (preg_match($re_play, $ln, $m2)) {
                    $nextField   = (int)$m2[4];
                    $nextPossRaw = isset($m2[3]) ? strtoupper($m2[3]) : '';
                    break; // NOTE: we accept P/F as next play and use its field
                }
                // plain text: ignore for yard calc
            }

            $canUseNext = false;
            if ($nextField !== null) {
                if ($nextPossRaw === '' || $nextPossRaw === $a_off || $a_off === '') {
                    $canUseNext = true;
                }
            }
            if ($canUseNext) {
                $yards = (int)$field - (int)$nextField; // positive when advancing
            }
        }
        // ====================================

        // Flags & derived
        $flags = _cp_pbp_simple_flags($text);
        if ($flags['penalty'] === 1) {
            $yards = 0;
        }
        $a_first = ($distance > 0 && $yards >= $distance) ? 1 : 0;
        $a_td    = ((int)$field === (int)$yards) ? 1 : 0;

        // Insert
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
    }

	echo "<p>Finished import</p>";
	nz_999(0);
	echo "<p>Finished function</p>";

    echo '<div class="w3-small">Inserted rows: '.(int)$rowsInserted.'</div>';

    // Mark processed bit for PBP
    if (defined('_CP_FLAG_PBP')) {
        try {
            $u = $conn->prepare("UPDATE g_uploads SET processed = COALESCE(processed,0) | :flag WHERE upload_id=:id LIMIT 1");
            $u->execute([':flag'=>_CP_FLAG_PBP, ':id'=>$upload_id]);
        } catch (Throwable $e) {
            echo '<div class="w3-panel w3-amber">Processed flag not updated: '.htmlspecialchars($e->getMessage()).'</div>';
        }
    }

    // Show 999 editor
    if ($hdrLeague && $hdrSeason && $hdrWeek) {
        _cp_pbp_render_editable_999($conn, $hdrLeague, $hdrSeason, $hdrWeek);
    } else {
        echo '<div class="w3-panel w3-amber">Missing league/season/week; cannot show 999 editor.</div>';
    }

    echo '</div>';
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

/** Editor for a_yards=999 */
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

/** Save edits from 999 grid */
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

/** Try calling nz_pbp() after manual corrections */
function _cp_pbp_call_nz(PDO $conn, int $upload_id): void {
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
        // soft-fail
    }
}
