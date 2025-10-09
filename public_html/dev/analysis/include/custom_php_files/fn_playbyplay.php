<?php
if(!defined('custom_page_from_inclusion')) { die(); }

/**
 * fn_playbyplay.php
 * Parse Play-by-Play and load into n_playbyplay.
 *
 * Key features:
 * - Strict full-play regex (time, poss, field, down & distance, formation, ocall, dcall, text)
 * - KO/KR handling
 * - Quarter slicing + overall end marker stop
 * - “Kneel” (flop/downs ball) skipped
 * - One-play look-ahead: cross-check pending play’s yards vs next snap field for SAME possession (mismatch -> 999)
 * - a_poss kept EXACTLY as in file (can be blank). a_off/a_def/team1/team2 still inferred.
 * - Formations P or F with no “fake” => yards forced to 0
 * - “personal foul” => penalty flag, yards forced to 0
 * - Clips short codes to schema sizes to avoid 22001 (dcall/form/ocall/teams/security/text)
 * - Verbose mode prints per-line rule decisions; non-verbose inserts rows and sets processed flag bit
 */

// Verbose logger toggle (true = per-line trace; false = insert rows)
if (!defined('_CP_PBP_VERBOSE')) {
    define('_CP_PBP_VERBOSE', false);
}

function _cp_process_play_by_play(PDO $conn, int $upload_id, string $files_dir) {
    echo '<div class="w3-container"><div class="w3-small w3-text-grey">Play by Play</div>';

    // --- Load upload row & file
    $st = $conn->prepare("SELECT * FROM `g_uploads` WHERE `upload_id`=:id LIMIT 1");
    $st->execute([':id'=>$upload_id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if (!$row) { echo '<div class="w3-panel w3-red">Upload not found.</div></div>'; return null; }

    $filename = $row['filename'];
    $fullpath = rtrim($files_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
    if (!is_readable($fullpath)) { echo '<div class="w3-panel w3-red">File not readable: '.htmlspecialchars($fullpath).'</div></div>'; return null; }

    $raw = file_get_contents($fullpath);
    if ($raw === false || $raw === '') { echo '<div class="w3-panel w3-red">File empty.</div></div>'; return null; }

    // --- Header parse (league/season/week/level)
    $hdrLeague = null; $hdrSeason = null; $hdrWeek = null; $hdrLevel = 'Advanced';
    if (preg_match('/GAMEPLAN\s*\((?P<level>[^)]+)\)\s+League\s+(?P<league>[A-Z0-9]+)\s+Season\s+(?P<season>\d{4})\s+Week\s+(?P<week>\d{1,2})/i', $raw, $hm)) {
        $hdrLeague = strtoupper($hm['league']);
        $hdrSeason = (int)$hm['season'];
        $hdrWeek   = (int)$hm['week'];
        $hdrLevel  = trim($hm['level']);
    } else {
        // fallback from filename NFLZZ-PE_s2033_w14_...
        if (preg_match('/^(?P<league>[A-Z0-9]+)-[A-Z]+_s(?P<season>\d{4})_w(?P<week>\d{1,2})_/i', $filename, $fm)) {
            $hdrLeague = strtoupper($fm['league']);
            $hdrSeason = (int)$fm['season'];
            $hdrWeek   = (int)$fm['week'];
        }
    }
    if (preg_match('/^NCAA6/i', (string)$hdrLeague)) {
        $hdrLevel = 'Basic';
    }
    $a_type = (stripos((string)$hdrLeague, 'NCAA') === 0) ? 'College' : 'Pro';

    // --- Overall end marker (game over)
    $overallEnd = strpos($raw, '<E><ST.66><L>');
    if ($overallEnd === false) $overallEnd = strlen($raw);

    // --- Quarter slicing
    $qp = '/<B>\s*(1st Quarter|2nd Quarter|3rd Quarter|4th Quarter|Overtime)\s*<[^>]*>/i';
    preg_match_all($qp, $raw, $qm, PREG_OFFSET_CAPTURE);
    if (empty($qm[0])) { echo '<div class="w3-panel w3-amber w3-border">Play by Play section not found.</div></div>'; return null; }

    $quarters = [];
    for ($i=0; $i<count($qm[0]); $i++) {
        $fullMatch = $qm[0][$i][0];
        $label     = $qm[1][$i][0] ?? 'Quarter';
        $startPos  = $qm[0][$i][1] + strlen($fullMatch);
        $nextPos   = ($i+1<count($qm[0])) ? $qm[0][$i+1][1] : $overallEnd;
        $endPos    = min($nextPos, $overallEnd);
        $quarters[] = [$label, $startPos, $endPos];
    }

    if (defined('_CP_DEBUG') && _CP_DEBUG) {
        echo '<div class="w3-small w3-text-grey">Quarters found: <b>'.count($quarters).'</b><br>';
        foreach ($quarters as $q) {
            echo htmlspecialchars($q[0]).' ['.$q[1].'–'.$q[2].'] len='.($q[2]-$q[1]).'<br>';
        }
        echo '</div>';
    }

    // --- Regexes
    $formationSet = '(?:A|B|C|D|E|F|G|H|I|J|K|L|O|P|S|T|U|W|X|Z)';

    // Full play line with outcome text
    $re_play_full = '/^\s*'
      .'(?:(\d{1,2}):(\d{2}))\s+'                             // 1,2 time
      .'([A-Z]{2})?\s*'                                       // 3 poss (opt; KEEP AS IS)
      .'(\d{1,2})\s+'                                         // 4 field
      .'(1st|2nd|3rd|4th)\s+and\s+(\d{1,2}|goal)\s+'          // 5 down, 6 dist|goal
      .'(' . $formationSet . ')\s+'                           // 7 form
      .'([A-Z]{2})\s+'                                        // 8 ocall
      .'([A-Z]{2})\s+'                                        // 9 dcall
      .'(.+?)\s*$'                                            // 10 text
      .'/i';

    // Header-only (no text) — context only
    $re_play_header = '/^\s*'
      .'(?:(\d{1,2}):(\d{2}))\s+'
      .'([A-Z]{2})?\s*'
      .'(\d{1,2})\s+'
      .'(1st|2nd|3rd|4th)\s+and\s+(\d{1,2}|goal)\s+'
      .'(' . $formationSet . ')\s+'
      .'([A-Z]{2})\s+'
      .'([A-Z]{2})\s*$'
      .'/i';

    // Kickoff / return
    $re_ko = '/^\s*'
      .'(?:(\d{1,2}):(\d{2}))\s+'
      .'([A-Z]{1,2})?\s*'
      .'KO\s+KR\s+'
      .'(.+?)\s*$'
      .'/i';

    // End-of-game kneel
    $re_kneel = '/\bflop\b.*\bdowns the ball\b/i';

    // Quarter summaries (team stats lines)
    $re_summary = '/^<(Z|C)>.*?:.*(rushing|passing)/i';

    $stripInline = ['<L>', '<C>'];

    // --- Prepare INSERT
    $sqlIns = "
        INSERT INTO `n_pbpgpt` (
          `a_type`,`a_level`,`a_league`,`a_season`,`a_week`,
          `a_minutes`,`a_seconds`,`a_poss`,`a_off`,`a_def`,
          `a_field`,`a_down`,`a_distance`,`a_form`,`a_ocall`,`a_dcall`,
          `a_yards`,
          `a_team1`,`a_team2`,
          `a_text`,
          `a_td`,`a_first`,`a_fumble`,`a_intercept`,`a_penalty`,`a_sack`,
          `a_hurry`,`a_blitzpickup`,`a_blitznopickup`,`a_safety`,
          `a_security`,`a_incomplete`,`a_peno`,`a_pend`,`a_bado`,`a_twodowns`,
          `a_situationno`,`a_negative`,`a_playtype`,`n_offcoach`,`n_defcoach`
        ) VALUES (
          :a_type,:a_level,:a_league,:a_season,:a_week,
          :a_minutes,:a_seconds,:a_poss,:a_off,:a_def,
          :a_field,:a_down,:a_distance,:a_form,:a_ocall,:a_dcall,
          :a_yards,
          :a_team1,:a_team2,
          :a_text,
          :a_td,:a_first,:a_fumble,:a_intercept,:a_penalty,:a_sack,
          :a_hurry,:a_blitzpickup,:a_blitznopickup,:a_safety,
          :a_security,:a_incomplete,:a_peno,:a_pend,:a_bado,:a_twodowns,
          :a_situationno,:a_negative,:a_playtype,:n_offcoach,:n_defcoach
        )
    ";
    $ins = $conn->prepare($sqlIns);

    $rowsInserted = 0;
    $doInsert = !_CP_PBP_VERBOSE;

    // Discover team codes from possession tags (for off/def/team1/2 inference)
    $seenTeams = [];

    // Debug counters
    $dbg_counts = [
        'matched_full'  => 0,
        'matched_ko'    => 0,
        'header_only'   => 0,
        'continuations' => 0,
        'skipped_blank' => 0,
        'skipped_kneel' => 0,
        'skipped_summary'=>0,
        'unmatched'     => 0,
    ];

    // Verbose pre
    if (_CP_PBP_VERBOSE) {
        echo '<pre style="font-size:11px;line-height:1.25em;background:#f7f7f7;padding:8px;border:1px solid #ccc;">';
    }

    // One-play buffer for yard cross-check
    $pending = null;

    foreach ($quarters as $qInfo) {
        [$qLabel, $qStart, $qEnd] = $qInfo;
        $slice = substr($raw, $qStart, $qEnd - $qStart);
        $slice = str_replace(["\r\n", "\r"], "\n", $slice);
        $lines = preg_split('/\n+/', $slice);

        $lineno = 0;
        foreach ($lines as $line) {
            $lineno++;
            $original = $line;
            $clean = trim($line);

            if ($clean === '') {
                $dbg_counts['skipped_blank']++;
                if (_CP_PBP_VERBOSE) echo sprintf("[%s %03d] skipped_blank\n", $qLabel, $lineno);
                continue;
            }

            if (strpos($clean, '<E><ST.66><L>') !== false) {
                // Game over inside quarter; stop this quarter
                if (_CP_PBP_VERBOSE) echo sprintf("[%s %03d] stop_marker\n", $qLabel, $lineno);
                break;
            }

            // Strip inline tokens in the text area
            $cleanText = str_replace($stripInline, '', $clean);

            // Skip quarter team summary lines
            if ($cleanText !== '' && $cleanText[0] === '<' && preg_match($re_summary, $cleanText)) {
                $dbg_counts['skipped_summary']++;
                if (_CP_PBP_VERBOSE) echo sprintf("[%s %03d] skipped_summary → %s\n", $qLabel, $lineno, $cleanText);
                continue;
            }

            // Skip kneel
            if (preg_match($re_kneel, $cleanText)) {
                $dbg_counts['skipped_kneel']++;
                if (_CP_PBP_VERBOSE) echo sprintf("[%s %03d] skipped_kneel → %s\n", $qLabel, $lineno, $cleanText);
                // Flush pending if any (no next snap to compare reliably)
                if ($pending && $doInsert) {
                    $cleanIns = _cp_pbp_clip_fields($pending);
                    $ins->execute(_cp_ppb_bind($cleanIns));
                    $rowsInserted++;
                    $pending = null;
                }
                continue;
            }

            // FULL play with text
            if (preg_match($re_play_full, $cleanText, $m)) {
                $dbg_counts['matched_full']++;
                if (_CP_PBP_VERBOSE) echo sprintf("[%s %03d] matched_full → %s\n", $qLabel, $lineno, $cleanText);

                $minutes = (int)$m[1];
                $seconds = (int)$m[2];
                $possRaw = ($m[3] !== '') ? strtoupper($m[3]) : '';  // KEEP a_poss as seen in file (can be blank)
                $field   = (int)$m[4];

                $downWord = $m[5]; // 1st/2nd/3rd/4th
                $downMap  = ['1st'=>1,'2nd'=>2,'3rd'=>3,'4th'=>4,'1ST'=>1,'2ND'=>2,'3RD'=>3,'4TH'=>4];
                $down     = $downMap[$downWord] ?? 0;

                $distRaw  = strtolower($m[6]);
                $distance = ($distRaw === 'goal') ? 10 : (int)$distRaw;

                $form  = strtoupper($m[7]);
                $ocall = strtoupper($m[8]);
                $dcall = strtoupper($m[9]);

                $text  = trim($m[10]);
                $text  = preg_replace('/<[^>]*>/', '', $text);
                $text  = trim($text);

                // Track teams from possession for off/def & team1/2 inference
                if ($possRaw && preg_match('/^[A-Z]{2}$/', $possRaw) && !in_array($possRaw, $seenTeams, true)) {
                    $seenTeams[] = $possRaw;
                }
                $a_off = $possRaw ?: ($seenTeams[0] ?? '');
                $a_def = '';
                if (count($seenTeams) === 2) {
                    $a_def = ($seenTeams[0] === $a_off) ? $seenTeams[1] : $seenTeams[0];
                }

                // yards & flags
                $yards = _cp_pbp_compute_yards($text, $form); // <- uses form P/F rule too
                $fx    = _cp_pbp_flags_from_text($text);

                // Force penalties to 0 yards (incl. personal foul)
                if ($fx['penalty'] === 1) {
                    $yards = 0;
                }

                // First down calc
                $a_first = ($distance > 0 && $yards >= $distance) ? 1 : 0;

                // If there is a pending play, finalize it using this play's starting field for delta (same possession only)
                if ($pending && $doInsert) {
                    // Compare only if both have a_off and they match
                    if (($pending['a_off'] ?? '') !== '' && $pending['a_off'] === $a_off) {
                        // Orientation assumption: moving toward LOWER numbers
                        $yd_by_field = (int)$pending['a_field'] - (int)$field;
                        if ($pending['a_yards'] !== null && (int)$pending['a_yards'] !== (int)$yd_by_field) {
                            $pending['a_yards'] = 999;
                        }
                    }
                    $cleanIns = _cp_pbp_clip_fields($pending);
                    $ins->execute(_cp_ppb_bind($cleanIns));
                    $rowsInserted++;
                    $pending = null;
                }

                // Build new pending (current play)
                $pending = [
                    'a_type'   => $a_type,
                    'a_level'  => $hdrLevel,
                    'a_league' => $hdrLeague ?: '',
                    'a_season' => $hdrSeason ?: 0,
                    'a_week'   => $hdrWeek ?: 0,

                    'a_minutes'=> $minutes,
                    'a_seconds'=> $seconds,
                    'a_poss'   => $possRaw,     // keep as-is (can be blank)
                    'a_off'    => $a_off,
                    'a_def'    => $a_def,

                    'a_field'  => $field,
                    'a_down'   => $down,
                    'a_distance'=> $distance,
                    'a_form'   => $form,
                    'a_ocall'  => $ocall,
                    'a_dcall'  => $dcall,

                    'a_yards'  => (int)$yards,

                    'a_team1'  => $seenTeams[0] ?? '',
                    'a_team2'  => $seenTeams[1] ?? '',

                    'a_text'   => $text,

                    'a_td'     => $fx['td'],
                    'a_first'  => $a_first,
                    'a_fumble' => $fx['fumble'],
                    'a_intercept'=> $fx['intercept'],
                    'a_penalty'=> $fx['penalty'],
                    'a_sack'   => $fx['sack'],
                    'a_hurry'  => $fx['hurry'],
                    'a_blitzpickup'   => $fx['blitzpickup'],
                    'a_blitznopickup' => $fx['blitznopickup'],
                    'a_safety' => $fx['safety'],
                    'a_security' => $_SESSION['logged_user_infos_ar']['username_user'] ?? null,
                    'a_incomplete' => $fx['incomplete'],
                    'a_peno'   => $fx['peno'],
                    'a_pend'   => $fx['pend'],
                    'a_bado'   => null,
                    'a_twodowns'=> null,
                    'a_situationno'=> null,
                    'a_negative' => ($yards < 0) ? 1 : 0,
                    'a_playtype'=> null,
                    'n_offcoach'=> null,
                    'n_defcoach'=> null,
                ];

                continue;
            }

            // Header-only lines (context) — ignore
            if (preg_match($re_play_header, $cleanText)) {
                $dbg_counts['header_only']++;
                if (_CP_PBP_VERBOSE) echo sprintf("[%s %03d] header_only  → %s\n", $qLabel, $lineno, $cleanText);
                continue;
            }

            // KO/KR
            if (preg_match($re_ko, $cleanText, $mk)) {
                $dbg_counts['matched_ko']++;
                if (_CP_PBP_VERBOSE) echo sprintf("[%s %03d] matched_ko   → %s\n", $qLabel, $lineno, $cleanText);

                $minutes = (int)$mk[1];
                $seconds = (int)$mk[2];
                $possRaw = ($mk[3] !== '') ? strtoupper($mk[3]) : ''; // keep as-is for a_poss

                $text = trim($mk[4]);
                $text = preg_replace('/<[^>]*>/', '', $text);
                $text = trim($text);

                // seen teams for off/def
                if ($possRaw && preg_match('/^[A-Z]{2}$/', $possRaw) && !in_array($possRaw, $seenTeams, true)) {
                    $seenTeams[] = $possRaw;
                }
                $a_off = $possRaw ?: ($seenTeams[0] ?? '');
                $a_def = '';
                if (count($seenTeams) === 2) {
                    $a_def = ($seenTeams[0] === $a_off) ? $seenTeams[1] : $seenTeams[0];
                }

                $yards = _cp_pbp_compute_yards($text, 'K'); // not P/F; KO rule unchanged
                $fx    = _cp_pbp_flags_from_text($text);

                // Penalties on KO -> 0 yards
                if ($fx['penalty'] === 1) {
                    $yards = 0;
                }

                // KO/KR are inserted immediately
                if ($doInsert) {
                    $params = [
                        'a_type'=>$a_type,'a_level'=>$hdrLevel,'a_league'=>$hdrLeague?:'','a_season'=>$hdrSeason?:0,'a_week'=>$hdrWeek?:0,
                        'a_minutes'=>$minutes,'a_seconds'=>$seconds,'a_poss'=>$possRaw,'a_off'=>$a_off,'a_def'=>$a_def,
                        'a_field'=>0,'a_down'=>0,'a_distance'=>0,'a_form'=>'K','a_ocall'=>'KO','a_dcall'=>'KR',
                        'a_yards'=>(int)$yards,
                        'a_team1'=>$seenTeams[0] ?? '','a_team2'=>$seenTeams[1] ?? '',
                        'a_text'=>$text,
                        'a_td'=>$fx['td'],'a_first'=>0,'a_fumble'=>$fx['fumble'],'a_intercept'=>$fx['intercept'],'a_penalty'=>$fx['penalty'],'a_sack'=>$fx['sack'],
                        'a_hurry'=>$fx['hurry'],'a_blitzpickup'=>$fx['blitzpickup'],'a_blitznopickup'=>$fx['blitznopickup'],'a_safety'=>$fx['safety'],
                        'a_security'=>$_SESSION['logged_user_infos_ar']['username_user'] ?? null,'a_incomplete'=>$fx['incomplete'],
                        'a_peno'=>$fx['peno'],'a_pend'=>$fx['pend'],'a_bado'=>null,'a_twodowns'=>null,'a_situationno'=>null,
                        'a_negative'=>($yards<0)?1:0,'a_playtype'=>null,'n_offcoach'=>null,'n_defcoach'=>null
                    ];
                    $cleanIns = _cp_pbp_clip_fields($params);
                    $ins->execute(_cp_ppb_bind($cleanIns));
                    $rowsInserted++;
                }

                continue;
            }

            // Continuation (indented narrative line)
            if (preg_match('/^\s+[^\d<]/', $original)) {
                $dbg_counts['continuations']++;
                if (_CP_PBP_VERBOSE) echo sprintf("[%s %03d] continuation → %s\n", $qLabel, $lineno, $cleanText);
                continue;
            }

            // Unmatched
            $dbg_counts['unmatched']++;
            if (_CP_PBP_VERBOSE) echo sprintf("[%s %03d] unmatched    → %s\n", $qLabel, $lineno, $cleanText);
        }

        // Quarter end: flush pending (no next snap to cross-check)
        if ($pending && $doInsert) {
            $cleanIns = _cp_pbp_clip_fields($pending);
            $ins->execute(_cp_ppb_bind($cleanIns));
            $rowsInserted++;
            $pending = null;
        }
    }

    // After final quarter: flush leftover pending (safety)
    if ($pending && $doInsert) {
        $cleanIns = _cp_pbp_clip_fields($pending);
        $ins->execute(_cp_ppb_bind($cleanIns));
        $rowsInserted++;
        $pending = null;
    }

    // Mark processed bit (don’t change processed when verbose/dry-run)
    if ($doInsert && defined('_CP_FLAG_PBP')) {
        try {
            $u = $conn->prepare("UPDATE `g_uploads` SET `processed` = COALESCE(`processed`,0) | :flag WHERE `upload_id`=:id LIMIT 1");
            $u->execute([':flag'=>_CP_FLAG_PBP, ':id'=>$upload_id]);
        } catch (Throwable $e) {
            echo '<div class="w3-panel w3-amber w3-border">Processed flag not updated: '.htmlspecialchars($e->getMessage()).'</div>';
        }
    }

    // Summary line
    if (_CP_PBP_VERBOSE) {
        echo '</pre>';
        echo '<div class="w3-small">Verbose Play-by-Play parsing complete (no inserts in verbose mode).</div>';
        echo '<div class="w3-small"><b>Total lines:</b> '.count($lines).' · <b>matched_full:</b> '.$dbg_counts['matched_full']."</div>";
    } else {
        echo '<div class="w3-small">Inserted rows: '.(int)$rowsInserted.'</div>';
        echo '<div class="w3-small">Play by Play processed (rows: '.(int)$rowsInserted.').</div>';
    }

    // Debug counters
    if (defined('_CP_DEBUG') && _CP_DEBUG) {
        echo '<div class="w3-small">PBP parse counters<br>'
            .'matched_full: '.$dbg_counts['matched_full'].' · '
            .'matched_ko: '.$dbg_counts['matched_ko'].' · '
            .'header_only: '.$dbg_counts['header_only'].' · '
            .'continuations: '.$dbg_counts['continuations'].' · '
            .'skipped_blank: '.$dbg_counts['skipped_blank'].' · '
            .'skipped_kneel: '.$dbg_counts['skipped_kneel'].' · '
            .'skipped_summary: '.$dbg_counts['skipped_summary'].' · '
            .'unmatched: '.$dbg_counts['unmatched'].' · '
            .'</div>';
    }

    echo '</div>';

    return ['stats'=>['rows'=>$rowsInserted]];
}

/* ================= Helpers ================= */

function _cp_ppb_bind(array $p): array {
    $out = [];
    foreach ($p as $k=>$v) $out[':'.$k] = $v;
    return $out;
}

/**
 * Yard parser.
 * - Counts “gain of N yards”, “for N yards”, “run back N yards”, punts “of N yards”, FG “of N yards”
 * - Subtracts “loss of N yards”, “minus N yards”
 * - SPECIAL: if formation is P or F and text does NOT contain “fake”, force 0 yards
 * - “checked off” is treated as an ordinary completed play; the gains are captured by the generic patterns
 */
function _cp_pbp_compute_yards(string $text, string $form = '') : int {
    $t = strtolower($text);
    $yards = 0;

    // P/F formations without "fake" -> force 0
    if (($form === 'P' || $form === 'F') && strpos($t, 'fake') === false) {
        return 0;
    }

    // Negative
    if (preg_match_all('/\bloss of\s+(\d+)\s+yards?/i', $t, $mm)) {
        foreach ($mm[1] as $n) $yards -= (int)$n;
    }
    if (preg_match_all('/\bminus\s+(\d+)\s+yards?/i', $t, $mm2)) {
        foreach ($mm2[1] as $n) $yards -= (int)$n;
    }

    // Positive (specific first; “checked off ... gain of N yards” matches here)
    if (preg_match_all('/\bfor\s+a\s+gain of\s+(\d+)\s+yards?/i', $t, $mm3b)) {
        foreach ($mm3b[1] as $n) $yards += (int)$n;
    }
    if (preg_match_all('/\bgain of\s+(\d+)\s+yards?/i', $t, $mm3)) {
        foreach ($mm3[1] as $n) $yards += (int)$n;
    }
    if (preg_match_all('/\bfor\s+(\d+)\s+yards?/i', $t, $mm4)) {
        foreach ($mm4[1] as $n) $yards += (int)$n;
    }
    if (preg_match_all('/\brun back\s+(\d+)\s+yards?/i', $t, $mm5)) {
        foreach ($mm5[1] as $n) $yards += (int)$n;
    }
    // Noun-led "of N yards"
    if (preg_match_all('/\bpunt\s+of\s+(\d+)\s+yards?/i', $t, $mm6)) {
        foreach ($mm6[1] as $n) $yards += (int)$n;
    }
    if (preg_match_all('/\bfield goal (?:attempt\s+)?of\s+(\d+)\s+yards?/i', $t, $mm7)) {
        foreach ($mm7[1] as $n) $yards += (int)$n;
    }
    if (preg_match_all('/\bover\s+(\d+)\s+yards?/i', $t, $mm8)) {
        foreach ($mm8[1] as $n) $yards += (int)$n;
    }

    return (int)$yards;
}

/**
 * Extract binary flags from narrative.
 * - Includes generic “penalty” AND explicit “personal foul” as penalty=1
 */
function _cp_pbp_flags_from_text(string $text) : array {
    $t = strtolower($text);
    $has = function($pat) use ($t) { return preg_match($pat, $t) ? 1 : 0; };

    $penalty = $has('/\bpenalty\b/i') || $has('/\bpersonal foul\b/i');

    return [
        'td'            => $has('/\b(is )?touchdown\b/'),
        'fumble'        => $has('/\bfumble\b/i'),
        'intercept'     => $has('/\bintercept(ed|ion)\b/i'),
        'penalty'       => $penalty ? 1 : 0,
        'sack'          => $has('/\bsack(ed)?\b/i'),
        'hurry'         => $has('/\bhurr(y|ied)\b/i'),
        'blitzpickup'   => $has('/\bblitz picked\b/i'),
        'blitznopickup' => $has('/\bblitz not picked\b/i'),
        'safety'        => $has('/\bsafety\b/i'),
        'incomplete'    => $has('/\bincomplete\b/i'),
        'peno'          => $has('/\bpenalty against offence\b/i'),
        'pend'          => $has('/\bpenalty against defence\b/i'),
    ];
}

/**
 * Clip/sanitize short fields to their schema sizes to avoid 22001.
 */
function _cp_pbp_clip_fields(array $p): array {
    // keep letters only for these codes, uppercase
    $letters = function($s){ return strtoupper(preg_replace('/[^A-Z]/i', '', (string)$s)); };

    // lengths by schema
    if (isset($p['a_form']))   $p['a_form']   = substr($letters($p['a_form']), 0, 3);   // varchar(3)
    if (isset($p['a_ocall']))  $p['a_ocall']  = substr($letters($p['a_ocall']), 0, 3);  // varchar(3)
    if (isset($p['a_dcall']))  $p['a_dcall']  = substr($letters($p['a_dcall']), 0, 3);  // varchar(3)
    if (isset($p['a_poss']))   $p['a_poss']   = substr($letters($p['a_poss']), 0, 2);   // varchar(2)
    if (isset($p['a_off']))    $p['a_off']    = substr($letters($p['a_off']), 0, 2);    // varchar(2)
    if (isset($p['a_def']))    $p['a_def']    = substr($letters($p['a_def']), 0, 2);    // varchar(2)
    if (isset($p['a_team1']))  $p['a_team1']  = substr($letters($p['a_team1']), 0, 2);  // varchar(2)
    if (isset($p['a_team2']))  $p['a_team2']  = substr($letters($p['a_team2']), 0, 2);  // varchar(2)

    // a_security is VARCHAR(3)
    if (isset($p['a_security']) && $p['a_security'] !== null) {
        $p['a_security'] = substr((string)$p['a_security'], 0, 3);
    }

    // narrative text limit
    if (isset($p['a_text'])) {
        $t = trim(preg_replace('/<[^>]*>/', '', (string)$p['a_text']));
        $p['a_text'] = mb_substr($t, 0, 1024, 'UTF-8');
    }

    // numeric clamps (safety)
    foreach (['a_minutes','a_seconds','a_field','a_down','a_distance','a_yards'] as $nk) {
        if (isset($p[$nk]) && $p[$nk] !== null && $p[$nk] !== '') {
            $p[$nk] = (int)$p[$nk];
        }
    }

    return $p;
}
