<?php
if(!defined('custom_page_from_inclusion')) { die(); }
require_once __DIR__ . '/fn_sections_bootstrap.php';

/**
 * Play by Play import: parses quartered play log into n_playbyplay.
 *
 * Quarter headers look like: <ST.118><C><B>1st Quarter<L.54.1>
 * Header line example: <P.70><B>GAMEPLAN (Advanced)   League NFLZZ   Season 2033   Week 12   2/10/25<L.54.1>
 */
function _cp_process_play_by_play(PDO $conn, int $upload_id, string $files_dir) : ?array
{
    echo '<div class="w3-card w3-padding w3-white w3-margin-top"><div class="w3-text-indigo"><b>Play by Play</b></div>';

    // --- Load upload row + file contents (helper from bootstrap) ---
    [$urow, $filename, $raw] = _cp_load_upload_and_file($conn, $upload_id, $files_dir);
    if ($raw === null) { echo '</div>'; return null; }

    // --- Parse header for level/league/season/week ---
    $level   = 'Advanced';
    $league  = null;
    $season  = null;
    $week    = null;

    if (preg_match('/<P\.70><B>GAMEPLAN\s+\((?P<level>[^)]+)\)\s+League\s+(?P<league>\S+)\s+Season\s+(?P<season>\d{4})\s+Week\s+(?P<week>\d+)/', $raw, $mh)) {
        $level  = trim($mh['level']);
        $league = strtoupper(trim($mh['league']));
        $season = (int)$mh['season'];
        $week   = (int)$mh['week'];
    } else {
        // fallback to filename parse
        [$league, $season, $week] = _cp_parse_lsw_from_filename($filename);
    }
    if (strtoupper((string)$league) === 'NCAA6') {
        $level = 'Basic';
    }

    // a_type from league prefix
    $type = (is_string($league) && stripos($league, 'NCAA') === 0) ? 'College' : 'Pro';

    // --- Find all quarter blocks (1st/2nd/3rd/4th/Overtime) ---
    $qPattern = '/<ST\.118><C><B>(1st Quarter|2nd Quarter|3rd Quarter|4th Quarter|Overtime)<L\.54\.1>/';
    preg_match_all($qPattern, $raw, $qm, PREG_OFFSET_CAPTURE);

    if (empty($qm[0])) {
        echo '<div class="w3-panel w3-amber w3-border">Play by Play section not found.</div></div>';
        return null;
    }

    $quarters = [];
    for ($i = 0; $i < count($qm[0]); $i++) {
        $label = $qm[1][$i][0] ?? 'Quarter';
        $start = $qm[0][$i][1] + strlen($qm[0][$i][0]);
        $end   = ($i + 1 < count($qm[0])) ? $qm[0][$i+1][1] : strpos($raw, '<E><P>', $start);
        if ($end === false) $end = strlen($raw);
        $quarters[] = [$label, $start, $end];
    }

    // --- Prepare INSERT (omit a_id so DB assigns AUTO_INCREMENT) ---
    $sql = "INSERT INTO n_playbyplay (
                a_type,a_level,a_league,a_season,a_week,
                a_minutes,a_seconds,a_poss,a_off,a_def,a_field,a_down,a_distance,a_form,a_ocall,a_dcall,
                a_yards,a_team1,a_team2,a_text,a_td,a_first,a_fumble,a_intercept,a_penalty,a_sack,a_hurry,
                a_blitzpickup,a_blitznopickup,a_safety,a_security,a_incomplete,a_peno,a_pend,a_bado,a_twodowns,
                a_situationno,a_negative,a_playtype,n_offcoach,n_defcoach
            ) VALUES (
                :a_type,:a_level,:a_league,:a_season,:a_week,
                :a_minutes,:a_seconds,:a_poss,:a_off,:a_def,:a_field,:a_down,:a_distance,:a_form,:a_ocall,:a_dcall,
                :a_yards,:a_team1,:a_team2,:a_text,:a_td,:a_first,:a_fumble,:a_intercept,:a_penalty,:a_sack,:a_hurry,
                :a_blitzpickup,:a_blitznopickup,:a_safety,:a_security,:a_incomplete,:a_peno,:a_pend,:a_bado,:a_twodowns,
                :a_situationno,:a_negative,:a_playtype,:n_offcoach,:n_defcoach
            )";
    $ins = $conn->prepare($sql);

    // --- Regexes for line parsing ---
    // Normal play: "mm:ss POSS FIELD 1st and 10  F  OC  DC  text..."
    $re_normal = '/^\s*'
        .'(?:(\d{1,2}):(\d{2}))?\s*'                // 1,2 = minutes,seconds (optional)
        .'([A-Z]{2})?\s*'                           // 3 = poss (optional)
        .'(\d{1,2})\s+'                              // 4 = field
        .'(1st|2nd|3rd|4th)\s+and\s+(\d{1,2})\s+'    // 5 = down-word, 6 = distance
        .'([A-Z])\s+'                                // 7 = form
        .'([A-Z]{2})\s+'                             // 8 = ocall
        .'([A-Z]{2})\s+'                             // 9 = dcall
        .'(.+?)\s*$'                                 // 10 = text
        .'/i';

    // Kickoff/return: "mm:ss POSS KO KR text..."
    $re_ko = '/^\s*'
        .'(?:(\d{1,2}):(\d{2}))?\s*'     // 1,2 = minutes,seconds (optional)
        .'([A-Z]{2})?\s*'                // 3 = poss (optional)
        .'KO\s+KR\s+'
        .'(.+?)\s*$'                     // 4 = text
        .'/i';

    $conn->beginTransaction();
    try {
        // Clear existing rows for this week, if key available
        if ($league && $season && $week) {
            $del = $conn->prepare("DELETE FROM n_playbyplay WHERE a_league=:l AND a_season=:s AND a_week=:w");
            $del->execute([':l'=>$league, ':s'=>$season, ':w'=>$week]);
        }

        $username = $_SESSION['logged_user_infos_ar']['username_user'] ?? null;
        $inserted = 0;
        $pbp_mismatches = []; // collects yardage cross-check mismatches
        $teamSet  = []; // distinct 2-letter team codes encountered

        // Carry-forward state
        $carryPoss    = null;
        $carryMinutes = null;
        $carrySeconds = null;

        // Buffer to cross-check previous line's yards vs next line's field (same poss)
        $prevLine = null; // ['poss'=>.., 'field'=>.., 'textYds'=>.., 'params'=>&array]

        foreach ($quarters as [$qlabel, $qstart, $qend]) {
            $chunk = substr($raw, $qstart, $qend - $qstart);
            $chunk = str_replace(["\r\n","\r"], "\n", $chunk);
            $lines = array_values(array_filter(array_map('trim', explode("\n", $chunk)), fn($x)=>$x!==''));

            foreach ($lines as $line) {
                // Strip markup like <L>, <C>, <Z> etc.
                $clean = preg_replace('/<[^>]+>/', '', $line);
                if ($clean === '') continue;

                $isNormal = preg_match($re_normal, $clean, $mN);
                $isKO     = !$isNormal && preg_match($re_ko, $clean, $mK);

                // Continuation line (no structure) -> append to previous play text
                if (!$isNormal && !$isKO) {
                    if ($prevLine !== null) {
                        $prevLine['params'][':a_text'] .= ' ' . trim($clean);
                        _cp_pbp_apply_flags_from_text($prevLine['params']);
                    }
                    continue;
                }

                // Resolve time / poss (carry-forward blanks)
                if ($isNormal) {
                    $minutes = ($mN[1] !== '') ? (int)$mN[1] : $carryMinutes;
                    $seconds = ($mN[2] !== '') ? (int)$mN[2] : $carrySeconds;
                    $poss    = ($mN[3] !== '') ? strtoupper($mN[3]) : $carryPoss;

                    if ($minutes === null || $seconds === null || $poss === null) {
                        // Not enough context yet
                        $carryMinutes = $minutes;
                        $carrySeconds = $seconds;
                        $carryPoss    = $poss;
                        $prevLine     = null;
                        continue;
                    }

                    $field    = (int)$mN[4];
                    $downWord = strtolower($mN[5]);
                    $downMap  = ['1st'=>1,'2nd'=>2,'3rd'=>3,'4th'=>4];
                    $down     = $downMap[$downWord] ?? 0;
                    $distance = (int)$mN[6];
                    $form     = strtoupper($mN[7]);
                    $ocall    = strtoupper($mN[8]);
                    $dcall    = strtoupper($mN[9]);
                    $text     = trim($mN[10]);

                } else { // KO/KR line
                    $minutes = ($mK[1] !== '') ? (int)$mK[1] : $carryMinutes;
                    $seconds = ($mK[2] !== '') ? (int)$mK[2] : $carrySeconds;
                    $poss    = ($mK[3] !== '') ? strtoupper($mK[3]) : $carryPoss;

                    if ($minutes === null || $seconds === null || $poss === null) {
                        $carryMinutes = $minutes;
                        $carrySeconds = $seconds;
                        $carryPoss    = $poss;
                        $prevLine     = null;
                        continue;
                    }

                    // Fill minimal required fields for NOT NULL columns
                    $field = 0; $down = 0; $distance = 0;
                    $form = 'X'; $ocall = 'KO'; $dcall = 'KR';
                    $text = trim($mK[4]);
                    $text .= ' [KO/KR]';
                }

                // Track teams we see
                if ($poss && !in_array($poss, $teamSet, true)) $teamSet[] = $poss;
                $off = $poss;
                $def = (count($teamSet) >= 2)
                    ? ($teamSet[0] === $off ? $teamSet[1] : $teamSet[0])
                    : $off;

                // Compute yards from text
                $yardsFromText = _cp_pbp_compute_yards($text);

                // Build params for this line
                $params = [
                    ':a_type'     => $type,
                    ':a_level'    => $level,
                    ':a_league'   => (string)$league,
                    ':a_season'   => (int)$season,
                    ':a_week'     => (int)$week,
                    ':a_minutes'  => (int)$minutes,
                    ':a_seconds'  => (int)$seconds,
                    ':a_poss'     => $off,
                    ':a_off'      => $off,
                    ':a_def'      => $def,
                    ':a_field'    => (int)$field,
                    ':a_down'     => (int)$down,
                    ':a_distance' => (int)$distance,
                    ':a_form'     => $form,
                    ':a_ocall'    => $ocall,
                    ':a_dcall'    => $dcall,
                    ':a_yards'    => (int)$yardsFromText, // may be overridden to 999 by cross-check
                    ':a_team1'    => $teamSet[0] ?? $off,
                    ':a_team2'    => $teamSet[1] ?? $def,
                    ':a_text'     => $text,
                    ':a_td'       => null,
                    ':a_first'    => ($yardsFromText >= $distance && $down > 0) ? 1 : 0,
                    ':a_fumble'   => 0,
                    ':a_intercept'=> 0,
                    ':a_penalty'  => 0,
                    ':a_sack'     => 0,
                    ':a_hurry'    => 0,
                    ':a_blitzpickup'    => 0,
                    ':a_blitznopickup'  => 0,
                    ':a_safety'   => 0,
                    ':a_security' => (string)$username,
                    ':a_incomplete' => 0,
                    ':a_peno'     => 0,
                    ':a_pend'     => 0,
                    ':a_bado'     => null,
                    ':a_twodowns' => null,
                    ':a_situationno' => null,
                    ':a_negative' => ($yardsFromText < 0) ? 1 : 0,
                    ':a_playtype' => null,
                    ':n_offcoach' => null,
                    ':n_defcoach' => null,
                ];
                _cp_pbp_apply_flags_from_text($params);

				// Cross-check with previous buffered line (same poss, both have field)
				if ($prevLine !== null) {
					$prevHasField = ($prevLine['field'] > 0);
					$thisHasField = ($field > 0);

					// Skip cross-check when the previous play is a penalty (we store yards=0)
					$skipPrev = !empty($prevLine['params'][':a_penalty'])
							 || !empty($prevLine['params'][':a_peno'])
							 || !empty($prevLine['params'][':a_pend']);

					if (!$skipPrev && $prevHasField && $thisHasField && $prevLine['poss'] === $off) {
						// Offense gains => field number DECREASES
						$deltaYards = $prevLine['field'] - $field;

						if ($deltaYards !== $prevLine['textYds']) {
							$prevLine['params'][':a_yards']    = 999;
							$prevLine['params'][':a_negative'] = ($prevLine['params'][':a_yards'] < 0) ? 1 : 0;

							// log mismatch
							$pbp_mismatches[] = [
								'quarter'      => $prevLine['quarter'],
								'time'         => sprintf('%02d:%02d', $prevLine['min'], $prevLine['sec']),
								'poss'         => $prevLine['poss'],
								'prev_field'   => $prevLine['field'],
								'next_field'   => $field,
								'delta'        => ($field - $prevLine['field']), // raw (old) delta (for reference)
								'delta_yards'  => $deltaYards,                   // corrected (prev - next)
								'text_yards'   => $prevLine['textYds'],
								'prev_text'    => $prevLine['text'],
								'next_text'    => $text,
							];
						}
					}

					$ins->execute($prevLine['params']);
					$inserted += (int)$ins->rowCount();
				}




				$prevLine = [
					'quarter' => $qlabel,
					'poss'    => $off,
					'field'   => $field,
					'min'     => (int)$minutes,
					'sec'     => (int)$seconds,
					'textYds' => (int)$yardsFromText,
					'text'    => $text,
					'params'  => $params
				];




                // Carry-forward
                $carryMinutes = $minutes;
                $carrySeconds = $seconds;
                $carryPoss    = $off;
            }
        }

        // Flush last buffered line
        if ($prevLine !== null) {
            $ins->execute($prevLine['params']);
            $inserted += (int)$ins->rowCount();
        }

        $conn->commit();

		if (defined('_CP_DEBUG') && _CP_DEBUG && !empty($pbp_mismatches)) {
			echo '<div class="w3-panel w3-amber w3-border">';
			echo '<b>Play-by-Play yardage mismatches</b> ('.count($pbp_mismatches).')</div>';

			echo '<div class="w3-responsive">';
			echo '<table class="w3-table w3-bordered w3-small">';
			echo '<tr class="w3-light-grey">'
				.'<th>Quarter</th>'
				.'<th>Time</th>'
				.'<th>Poss</th>'
				.'<th>Prev Field</th>'
				.'<th>Next Field</th>'
				.'<th>Δ (raw)</th>'         // next - prev (for reference)
				.'<th>Yds by Field</th>'    // prev - next (the one we compare)
				.'<th>Text Yds</th>'
				.'<th>Prev Text</th>'
				.'<th>Next Text</th>'
				.'</tr>';

			$maxRows = 200;
			$i = 0;
			foreach ($pbp_mismatches as $mm) {
				if ($i++ >= $maxRows) break;

				$quarter      = htmlspecialchars($mm['quarter'] ?? '');
				$time         = htmlspecialchars($mm['time'] ?? '');
				$poss         = htmlspecialchars($mm['poss'] ?? '');
				$prev_field   = (int)($mm['prev_field'] ?? 0);
				$next_field   = (int)($mm['next_field'] ?? 0);
				$delta_raw    = (int)($mm['delta'] ?? ($next_field - $prev_field));
				$yds_by_field = (int)($mm['delta_yards'] ?? ($prev_field - $next_field));
				$text_yds     = (int)($mm['text_yards'] ?? 0);
				$prev_text    = htmlspecialchars($mm['prev_text'] ?? '');
				$next_text    = htmlspecialchars($mm['next_text'] ?? '');

				echo '<tr>'
					.'<td>'.$quarter.'</td>'
					.'<td>'.$time.'</td>'
					.'<td>'.$poss.'</td>'
					.'<td>'.$prev_field.'</td>'
					.'<td>'.$next_field.'</td>'
					.'<td>'.$delta_raw.'</td>'
					.'<td>'.$yds_by_field.'</td>'
					.'<td>'.$text_yds.'</td>'
					.'<td>'.$prev_text.'</td>'
					.'<td>'.$next_text.'</td>'
					.'</tr>';
			}
			echo '</table>';
			if (count($pbp_mismatches) > $maxRows) {
				echo '<div class="w3-small w3-text-grey">…and '.(count($pbp_mismatches)-$maxRows).' more</div>';
			}
			echo '</div>';
		}


        // Mark processed (this is a real implementation, not a stub)
        _cp_mark_processed_flag($conn, $upload_id, _CP_FLAG_PBP, $league, $season, $week);

        if (defined('_CP_DEBUG') && _CP_DEBUG) {
            echo '<div class="w3-small">Inserted rows: '.(int)$inserted.'</div>';
        }
        echo '<div class="w3-panel w3-pale-green w3-border">Play by Play processed (rows: '.$inserted.').</div></div>';

        return ['stats' => ['inserted' => $inserted]];

    } catch (Throwable $e) {
        $conn->rollBack();
        echo '<div class="w3-panel w3-red"><b>Error:</b> '.htmlspecialchars($e->getMessage()).'</div></div>';
        return null;
    }
}

/** -------------------- helpers specific to PBP parsing -------------------- */

/**
 * Sum yards from text phrases:
 *  - "gain of N yards", "for N yards", "for no gain"
 *  - "loss of N yards", "minus N yards"
 *  - "run back N yards"
 * Returns net yards (gains minus losses).
 */
function _cp_pbp_compute_yards(string $text) : int {
    $t = strtolower($text);
    $yards = 0;

    // Treat "no gain" explicitly
    if (preg_match('/\bno gain\b/', $t)) {
        // keep 0 unless other explicit values appear
    }

	// Losses
	if (preg_match_all('/\bloss of\s+(\d+)\s+yards?/i', $t, $mm)) {
		foreach ($mm[1] as $n) $yards -= (int)$n;
	}
	if (preg_match_all('/\bminus\s+(\d+)\s+yards?/i', $t, $mm2)) {
		foreach ($mm2[1] as $n) $yards -= (int)$n;
	}

	// Gains
	if (preg_match_all('/\bgain of\s+(\d+)\s+yards?/i', $t, $mm3)) {
		foreach ($mm3[1] as $n) $yards += (int)$n;
	}
	if (preg_match_all('/\bfor\s+a\s+gain of\s+(\d+)\s+yards?/i', $t, $mm3b)) {
		foreach ($mm3b[1] as $n) $yards += (int)$n;
	}
	// Keep this after the more specific patterns
	if (preg_match_all('/\bfor\s+(\d+)\s+yards?/i', $t, $mm4)) {
		foreach ($mm4[1] as $n) $yards += (int)$n;
	}
	// Kick/punt return phrasing
	if (preg_match_all('/\brun back\s+(\d+)\s+yards?/i', $t, $mm5)) {
		foreach ($mm5[1] as $n) $yards += (int)$n;
	}


    return (int)$yards;
}

/**
 * Apply flags derived from text to the params array (passed by reference).
 * Fills: a_td, a_fumble, a_intercept, a_penalty, a_sack, a_hurry,
 *        a_blitzpickup, a_blitznopickup, a_safety, a_incomplete, a_peno, a_pend.
 */
function _cp_pbp_apply_flags_from_text(array &$p) : void {
    $t = strtolower($p[':a_text']);

    $p[':a_td']            = (preg_match('/\btouchdown\b|\btd\b/', $t)) ? 1 : 0;
    $p[':a_fumble']        = (preg_match('/\bfumble\b/', $t)) ? 1 : 0;
    $p[':a_intercept']     = (preg_match('/\bintercept(ed|ion)\b/', $t)) ? 1 : 0;
    $p[':a_penalty']       = (preg_match('/\bpenalty\b/', $t)) ? 1 : 0;
    $p[':a_sack']          = (preg_match('/\bsack(ed)?\b/', $t)) ? 1 : 0;
    $p[':a_hurry']         = (preg_match('/\bhurr(y|ied)\b/', $t)) ? 1 : 0;
    $p[':a_blitzpickup']   = (preg_match('/\bblitz picked\b/', $t)) ? 1 : 0;
    $p[':a_blitznopickup'] = (preg_match('/\bblitz not picked\b/', $t)) ? 1 : 0;
    $p[':a_safety']        = (preg_match('/\bsafety\b/', $t)) ? 1 : 0;
    $p[':a_incomplete']    = (preg_match('/\bincomplete\b/', $t)) ? 1 : 0;

    // Specific offence/defence penalty phrasing
    $p[':a_peno']          = (preg_match('/\bpenalty against offence\b/', $t)) ? 1 : 0;
    $p[':a_pend']          = (preg_match('/\bpenalty against defence\b/', $t)) ? 1 : 0;
}
