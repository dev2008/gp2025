<?php
if(!defined('custom_page_from_inclusion')) { die(); }

/**
 * leaguereport.php
 * - Parses <BK.League Report> section and upserts into f_games
 * - Updates g_uploads with league/season/week + processed=700
 * - All queries via $conn (PDO)
 * - All echoed HTML (w3.css-friendly)
 */

function _cp_process_league_report(PDO $conn, $upload_id, $files_dir) {
    echo '<div class="w3-card w3-padding w3-white">';
	#echo "<p>Debug is " . var_export(_CP_DEBUG, true) . "</p>";


    // --- Load selected upload row
    try {
        $st = $conn->prepare("SELECT * FROM `g_uploads` WHERE `upload_id` = :id LIMIT 1");
        $st->execute([':id' => $upload_id]);
        $_row = $st->fetch(PDO::FETCH_ASSOC);
        if (!$_row) {
            echo '<div class="w3-panel w3-red">Upload not found.</div></div>';
            return;
        }
    } catch (Exception $e) {
        echo '<div class="w3-panel w3-red"><b>DB Error:</b> '.htmlspecialchars($e->getMessage()).'</div></div>';
        return;
    }

    $_cp_filename = $_row['filename'];
    $_cp_fullpath = rtrim($files_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $_cp_filename;
    if (!is_readable($_cp_fullpath)) {
        echo '<div class="w3-panel w3-red"><b>File not readable:</b> '.htmlspecialchars($_cp_fullpath).'</div></div>';
        return;
    }

    $_cp_raw = file_get_contents($_cp_fullpath);
    if ($_cp_raw === false || $_cp_raw === '') {
        echo '<div class="w3-panel w3-red"><b>Error reading file.</b></div></div>';
        return;
    }

    // --- Extract league/season/week from filename: e.g. NFLZZ-PE_s2033_w14_vs_Cardinals.txt
    $_cp_league = null;
    $_cp_season = null;
    $_cp_week   = null;
    if (preg_match('/^(?P<league>[A-Z]+)-[A-Z]+_s(?P<season>\d{4})_w(?P<week>\d{1,2})_/i', $_cp_filename, $m)) {
        $_cp_league = strtoupper($m['league']);
        $_cp_season = (int)$m['season'];
        $_cp_week   = (int)$m['week'];
    }

    // --- Carve out the League Report section
    // begins: <P.70><BK.League Report><L>
    // ends:   <E><P>  (before <B><BK.Standings><L>)
    $startTag = '<P.70><BK.League Report><L>';
    $endTagA  = '<E><P>';
    $section  = null;

    $startPos = strpos($_cp_raw, $startTag);
    if ($startPos !== false) {
        $startPos += strlen($startTag);
        $endPos = strpos($_cp_raw, $endTagA, $startPos);
        if ($endPos === false) {
            $endPos = strlen($_cp_raw);
        }
        $section = substr($_cp_raw, $startPos, $endPos - $startPos);
    }

    if ($section === null) {
        echo '<div class="w3-panel w3-red"><b>League Report section not found.</b></div></div>';
        return;
    }

    // Normalize line breaks for safety
    $section = str_replace(["\r\n", "\r"], "\n", $section);

    // Split by <Z> markers into games
    $chunks = preg_split('/<Z>/', $section);
    $games  = [];
    foreach ($chunks as $c) {
        $t = trim($c);
        if ($t === '') continue;
        // Each game should have 6 lines; tolerate extra <C> / <L.xx.x> tokens inline
        // Normalize by removing trailing <C> tokens and splitting at "<L."
        $lines = preg_split('/\n+/', $t);
        // Filter out entirely empty lines
        $lines = array_values(array_filter(array_map('trim', $lines), fn($x) => $x !== ''));
        if (count($lines) >= 6) {
            // Take first 6 meaningful lines (ignore any extras)
            $games[] = array_slice($lines, 0, 6);
        }
    }

    if (empty($games)) {
        echo '<div class="w3-panel w3-pale-yellow w3-border">No games found in the League Report.</div></div>';
        return;
    }

    // Optional: map of franchise names to codes (extend as needed)
    $franchiseMap = [
        // 'Buffalo Bills' => '2001',
        // 'New England Patriots' => '2002',
    ];

    // Prepare UPSERT for f_games
    $sql = "
        INSERT INTO `f_games` (
          `id_game`,`league`,`season`,`week`,`team`,`franchise`,`coach`,`qb`,`safe`,
          `q1`,`q2`,`q3`,`q4`,`ot`,`score`,
          `fga`,`fgg`,`epa`,`epg`,`cva`,`cvg`,`punts`,
          `thirdcon`,`thirddowns`,`fourthcon`,`fourthdowns`,`firstd`,
          `passcmp`,`passatt`,`passyds`,`passlng`,`passlngtd`,`passtd`,`passpct`,`interception`,`hrd`,`skd`,
          `rush`,`rushyds`,`rushlng`,`rushlngtd`,`rushtd`,`fum`,`qbatt`,`qbyds`,
          `kr`,`kryds`,`krtd`,`pr`,`pryds`,`prtd`,
          `form1`,`form2`,`run1`,`run2`,`pass1`,`pass2`,`def1`,`def2`,
          `homeaway`,`gametype`,
          `opp_team`,`opp_franchise`,`opp_coach`,`opp_qb`,`opp_safe`,
          `opp_q1`,`opp_q2`,`opp_q3`,`opp_q4`,`opp_ot`,`opp_score`,
          `opp_fga`,`opp_fgg`,`opp_epa`,`opp_epg`,`opp_cva`,`opp_cvg`,`opp_punts`,
          `opp_thirdcon`,`opp_thirddowns`,`opp_fourthcon`,`opp_fourthdowns`,`opp_firstd`,
          `opp_passcmp`,`opp_passatt`,`opp_passyds`,`opp_passlng`,`opp_passlngtd`,`opp_passtd`,`opp_passpct`,`opp_interception`,`opp_hrd`,`opp_skd`,
          `opp_rush`,`opp_rushyds`,`opp_rushlng`,`opp_rushlngtd`,`opp_rushtd`,`opp_fum`,`opp_qbatt`,`opp_qbyds`,
          `opp_kr`,`opp_kryds`,`opp_krtd`,`opp_pr`,`opp_pryds`,`opp_prtd`,
          `opp_form1`,`opp_form2`,`opp_run1`,`opp_run2`,`opp_pass1`,`opp_pass2`,`opp_def1`,`opp_def2`,
          `win`,`lose`,`tie`,
          `modification_by`,`modification_from`
        ) VALUES (
          :id_game,:league,:season,:week,:team,:franchise,:coach,:qb,:safe,
          :q1,:q2,:q3,:q4,:ot,:score,
          :fga,:fgg,:epa,:epg,:cva,:cvg,:punts,
          :thirdcon,:thirddowns,:fourthcon,:fourthdowns,:firstd,
          :passcmp,:passatt,:passyds,:passlng,:passlngtd,:passtd,:passpct,:interception,:hrd,:skd,
          :rush,:rushyds,:rushlng,:rushlngtd,:rushtd,:fum,:qbatt,:qbyds,
          :kr,:kryds,:krtd,:pr,:pryds,:prtd,
          :form1,:form2,:run1,:run2,:pass1,:pass2,:def1,:def2,
          :homeaway,:gametype,
          :opp_team,:opp_franchise,:opp_coach,:opp_qb,:opp_safe,
          :opp_q1,:opp_q2,:opp_q3,:opp_q4,:opp_ot,:opp_score,
          :opp_fga,:opp_fgg,:opp_epa,:opp_epg,:opp_cva,:opp_cvg,:opp_punts,
          :opp_thirdcon,:opp_thirddowns,:opp_fourthcon,:opp_fourthdowns,:opp_firstd,
          :opp_passcmp,:opp_passatt,:opp_passyds,:opp_passlng,:opp_passlngtd,:opp_passtd,:opp_passpct,:opp_interception,:opp_hrd,:opp_skd,
          :opp_rush,:opp_rushyds,:opp_rushlng,:opp_rushlngtd,:opp_rushtd,:opp_fum,:opp_qbatt_opp,:opp_qbyds_opp,
          :opp_kr,:opp_kryds,:opp_krtd,:opp_pr,:opp_pryds,:opp_prtd,
          :opp_form1,:opp_form2,:opp_run1,:opp_run2,:opp_pass1,:opp_pass2,:opp_def1,:opp_def2,
		  :win,:lose,:tie,
		  :modification_by,:modification_from

        )
        ON DUPLICATE KEY UPDATE
          `coach`=VALUES(`coach`), `qb`=VALUES(`qb`), `safe`=VALUES(`safe`),
          `q1`=VALUES(`q1`), `q2`=VALUES(`q2`), `q3`=VALUES(`q3`), `q4`=VALUES(`q4`), `ot`=VALUES(`ot`), `score`=VALUES(`score`),
          `fga`=VALUES(`fga`), `fgg`=VALUES(`fgg`), `epa`=VALUES(`epa`), `epg`=VALUES(`epg`), `cva`=VALUES(`cva`), `cvg`=VALUES(`cvg`), `punts`=VALUES(`punts`),
          `thirdcon`=VALUES(`thirdcon`), `thirddowns`=VALUES(`thirddowns`), `fourthcon`=VALUES(`fourthcon`), `fourthdowns`=VALUES(`fourthdowns`), `firstd`=VALUES(`firstd`),
          `passcmp`=VALUES(`passcmp`), `passatt`=VALUES(`passatt`), `passyds`=VALUES(`passyds`), `passlng`=VALUES(`passlng`), `passlngtd`=VALUES(`passlngtd`),
          `passtd`=VALUES(`passtd`), `passpct`=VALUES(`passpct`), `interception`=VALUES(`interception`), `hrd`=VALUES(`hrd`), `skd`=VALUES(`skd`),
          `rush`=VALUES(`rush`), `rushyds`=VALUES(`rushyds`), `rushlng`=VALUES(`rushlng`), `rushlngtd`=VALUES(`rushlngtd`), `rushtd`=VALUES(`rushtd`),
          `fum`=VALUES(`fum`), `qbatt`=VALUES(`qbatt`), `qbyds`=VALUES(`qbyds`),
          `kr`=VALUES(`kr`), `kryds`=VALUES(`kryds`), `krtd`=VALUES(`krtd`), `pr`=VALUES(`pr`), `pryds`=VALUES(`pryds`), `prtd`=VALUES(`prtd`),
          `form1`=VALUES(`form1`), `form2`=VALUES(`form2`), `run1`=VALUES(`run1`), `run2`=VALUES(`run2`),
          `pass1`=VALUES(`pass1`), `pass2`=VALUES(`pass2`), `def1`=VALUES(`def1`), `def2`=VALUES(`def2`),
          `opp_team`=VALUES(`opp_team`), `opp_franchise`=VALUES(`opp_franchise`), `opp_coach`=VALUES(`opp_coach`), `opp_qb`=VALUES(`opp_qb`), `opp_safe`=VALUES(`opp_safe`),
          `opp_q1`=VALUES(`opp_q1`), `opp_q2`=VALUES(`opp_q2`), `opp_q3`=VALUES(`opp_q3`), `opp_q4`=VALUES(`opp_q4`), `opp_ot`=VALUES(`opp_ot`), `opp_score`=VALUES(`opp_score`),
          `opp_fga`=VALUES(`opp_fga`), `opp_fgg`=VALUES(`opp_fgg`), `opp_epa`=VALUES(`opp_epa`), `opp_epg`=VALUES(`opp_epg`), `opp_cva`=VALUES(`opp_cva`), `opp_cvg`=VALUES(`opp_cvg`), `opp_punts`=VALUES(`opp_punts`),
          `opp_thirdcon`=VALUES(`opp_thirdcon`), `opp_thirddowns`=VALUES(`opp_thirddowns`), `opp_fourthcon`=VALUES(`opp_fourthcon`), `opp_fourthdowns`=VALUES(`opp_fourthdowns`), `opp_firstd`=VALUES(`opp_firstd`),
          `opp_passcmp`=VALUES(`opp_passcmp`), `opp_passatt`=VALUES(`opp_passatt`), `opp_passyds`=VALUES(`opp_passyds`), `opp_passlng`=VALUES(`opp_passlng`), `opp_passlngtd`=VALUES(`opp_passlngtd`),
          `opp_passtd`=VALUES(`opp_passtd`), `opp_passpct`=VALUES(`opp_passpct`), `opp_interception`=VALUES(`opp_interception`), `opp_hrd`=VALUES(`opp_hrd`), `opp_skd`=VALUES(`opp_skd`),
          `opp_rush`=VALUES(`opp_rush`), `opp_rushyds`=VALUES(`opp_rushyds`), `opp_rushlng`=VALUES(`opp_rushlng`), `opp_rushlngtd`=VALUES(`opp_rushlngtd`), `opp_rushtd`=VALUES(`opp_rushtd`),
          `opp_fum`=VALUES(`opp_fum`), `opp_qbatt`=VALUES(`opp_qbatt`), `opp_qbyds`=VALUES(`opp_qbyds`),
          `opp_kr`=VALUES(`opp_kr`), `opp_kryds`=VALUES(`opp_kryds`), `opp_krtd`=VALUES(`opp_krtd`), `opp_pr`=VALUES(`opp_pr`), `opp_pryds`=VALUES(`opp_pryds`), `opp_prtd`=VALUES(`opp_prtd`),
          `opp_form1`=VALUES(`opp_form1`), `opp_form2`=VALUES(`opp_form2`), `opp_run1`=VALUES(`opp_run1`), `opp_run2`=VALUES(`opp_run2`), `opp_pass1`=VALUES(`opp_pass1`), `opp_pass2`=VALUES(`opp_pass2`), `opp_def1`=VALUES(`opp_def1`), `opp_def2`=VALUES(`opp_def2`),
          `win`=VALUES(`win`), `lose`=VALUES(`lose`), `tie`=VALUES(`tie`),
          `modification_time`=CURRENT_TIMESTAMP, `modification_by`=VALUES(`modification_by`), `modification_from`=VALUES(`modification_from`)
    ";
    // right before $up = $conn->prepare($sql);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);   // <-- add this

    $up = $conn->prepare($sql);

    $inserted = 0;
    $updated  = 0;
    $warnings = [];

    $conn->beginTransaction();
    try {
        foreach ($games as $gidx => $lines) {
            // Each $lines[x] holds: "<...Road...><T><...Home...><L.xx.x>"
            $parsedRoad = _cp_parse_game_lines_side($lines, 'road');
            $parsedHome = _cp_parse_game_lines_side($lines, 'home');

            if (!$parsedRoad || !$parsedHome) {
                $warnings[] = "Game ".($gidx+1).": could not parse both sides.";
                continue;
            }

            // Mirror into two row payloads
            $rowRoad = _cp_build_row($_cp_league, $_cp_season, $_cp_week, $parsedRoad, $parsedHome, 0, $franchiseMap);
            $rowHome = _cp_build_row($_cp_league, $_cp_season, $_cp_week, $parsedHome, $parsedRoad, 1, $franchiseMap);

			// ---- Friendly output labels ----
			$roadTeam = $rowRoad['team'] ?? ($parsedRoad['team'] ?? 'Road');
			$homeTeam = $rowHome['team'] ?? ($parsedHome['team'] ?? 'Home');
			$roadId   = $rowRoad['id_game'] ?? '';
			$homeId   = $rowHome['id_game'] ?? '';
			
			_cp_log_processing($roadTeam, $homeTeam, $roadId, $homeId);

	// Upsert Road
	$paramsRoad = _cp_bind_params($rowRoad, $sql);
	[$missingR, $extraR] = _cp_param_diff($sql, $paramsRoad);
	if ($missingR) {
		echo '<div class="w3-panel w3-red"><b>Missing params (road):</b> '
			. htmlspecialchars(implode(', ', $missingR)) . '</div>';
		return; // stop here to fix placeholder names
	}
	list($missingR, $extraR, $needR, $haveR) = _cp_param_diff($sql, $paramsRoad);


if (!empty($missingR)) {
    echo '<div class="w3-panel w3-red"><b>Missing params (road):</b> '
        . htmlspecialchars(implode(', ', $missingR)) . '</div>';
    return;
}

	$up->execute($paramsRoad);
	$inserted += (int)$up->rowCount();

	// Upsert Home
	$paramsHome = _cp_bind_params($rowHome, $sql);
	[$missingH, $extraH] = _cp_param_diff($sql, $paramsHome);
	if ($missingH) {
		echo '<div class="w3-panel w3-red"><b>Missing params (home):</b> '
			. htmlspecialchars(implode(', ', $missingH)) . '</div>';
		return;
	}
	list($missingH, $extraH, $needH, $haveH) = _cp_param_diff($sql, $paramsHome);

	if (!empty($missingH)) {
		echo '<div class="w3-panel w3-red"><b>Missing params (home):</b> '
			. htmlspecialchars(implode(', ', $missingH)) . '</div>';
		return;
	}

	$up->execute($paramsHome);
	$inserted += (int)$up->rowCount();
	

if (_CP_DEBUG) {
    echo "Tokens (road): need ".count($needR).", have ".count($haveR)."<br>";
    echo "Tokens (home): need ".count($needH).", have ".count($haveH)."<br>";
}



        }

		// Update g_uploads league/season/week + set LEAGUE bit
		[$__lg, $__sn, $__wk] = _cp_parse_lsw_from_filename($_cp_filename); // same helper style as sections
		if ($__lg !== null && $__sn !== null && $__wk !== null) {
			_cp_mark_processed_flag($conn, $upload_id, _CP_FLAG_LEAGUE, $__lg, $__sn, $__wk);
		} else {
			_cp_mark_processed_flag($conn, $upload_id, _CP_FLAG_LEAGUE);
			$warnings[] = "League/Season/Week not parsed from filename; only processed bit updated.";
		}
				$conn->commit();
				// After commit:
				require_once __DIR__ . '/fn_sections_bootstrap.php';
				[$__lg, $__sn, $__wk] = _cp_parse_lsw_from_filename($_cp_filename);
				_cp_mark_processed_flag($conn, $upload_id, _CP_FLAG_LEAGUE, $__lg, $__sn, $__wk);

			} catch (Exception $e) {
				$conn->rollBack();
				echo '<div class="w3-panel w3-red"><b>Processing error:</b> '.htmlspecialchars($e->getMessage()).'</div></div>';
				return;
			}
			// ---- Build League Report stats ----
			// actual rows = 2 rows per game successfully processed
			$actual_rows = count($games) * 2;

			// expected rows based on league prefix (from filename parse or filename itself)
			$league_hint = $_cp_league ?: (preg_match('/^([A-Za-z]+)/', $_cp_filename, $mm) ? strtoupper($mm[1]) : null);
			$expected_rows = null;
			if ($league_hint !== null) {
				if (stripos($league_hint, 'NFL') === 0) {
					$expected_rows = 24;
				} elseif (stripos($league_hint, 'NCAA') === 0) {
					$expected_rows = 12;
				}
			}
			$ok = ($expected_rows === null) ? true : ($actual_rows === $expected_rows);

			// (normal UI already echoes "Done.", keep that; just also return stats)
			return ['stats' => [
				'actual_rows'   => $actual_rows,
				'expected_rows' => $expected_rows,
				'ok'            => $ok,
			]];


			// Report
			echo '<div class="w3-panel w3-green"><b>Done.</b> Games processed: '.count($games).'.</div>';
			if ($warnings) {
				echo '<div class="w3-panel w3-pale-yellow w3-border"><b>Warnings:</b><ul class="w3-ul">';
				foreach ($warnings as $w) echo '<li>'.htmlspecialchars($w).'</li>';
				echo '</ul></div>';
			}
			echo '</div>';
		}

/** Helpers *************************************************************/

function _cp_clean_half($s) {
    // Remove trailing <L.xx.x> and <C> etc.
    $s = preg_replace('/<L\.[^>]+>/', '', $s);
    $s = str_replace('<C>', '', $s);
    return trim($s);
}

/**
 * Parse the 6 lines for one side (road/home).
 * @param array $lines 6 strings
 * @param string $side 'road' or 'home'
 * @return array|null
 */
function _cp_parse_game_lines_side(array $lines, string $side) {
    $take = function($line) use ($side) {
        // Split once at <T>, first half = road, second half = home
        $parts = explode('<T>', $line, 2);
        if (count($parts) < 2) return null;
        $half = ($side === 'road') ? $parts[0] : $parts[1];
        return _cp_clean_half($half);
    };

    $L1 = $take($lines[0]); // Teams & Score
    $L2 = $take($lines[1]); // Misc
    $L3 = $take($lines[2]); // Passing
    $L4 = $take($lines[3]); // Rushing
    $L5 = $take($lines[4]); // Special Teams
    $L6 = $take($lines[5]); // Calls

    if ($L1===null || $L2===null || $L3===null || $L4===null || $L5===null || $L6===null) return null;

    $out = [
        'team' => null, 'coach' => null, 'qb'=> null, 'safe'=> null,
        'q1'=>0,'q2'=>0,'q3'=>0,'q4'=>0,'ot'=>0,'score'=>0,
        'fga'=>0,'fgg'=>0,'epa'=>0,'epg'=>0,'cva'=>0,'cvg'=>0,'punts'=>0,
        'thirdcon'=>0,'thirddowns'=>0,'fourthcon'=>0,'fourthdowns'=>0,'firstd'=>0,
        'passcmp'=>0,'passatt'=>0,'passyds'=>0,'passlng'=>null,'passlngtd'=>null,'passtd'=>0,'passpct'=>null,'interception'=>0,'hrd'=>0,'skd'=>0,
        'rush'=>0,'rushyds'=>0,'rushlng'=>null,'rushlngtd'=>null,'rushtd'=>0,'fum'=>0,'qbatt'=>0,'qbyds'=>0,
        'kr'=>0,'kryds'=>0,'krtd'=>0,'pr'=>0,'pryds'=>0,'prtd'=>0,
        'form1'=>null,'form2'=>null,'run1'=>null,'run2'=>null,'pass1'=>null,'pass2'=>null,'def1'=>null,'def2'=>null
    ];

    // Line 1: "Buffalo Bills (Alan Baylis)  10 3 14 19 (46)" or with "S" flag before numbers, and optional OT
    // Also sometimes "Team (Coach) S  7 3 0 7 (17)"
    if (preg_match('/^(?P<team>.+?)\s*\((?P<coach>[^)]+)\)\s*(?P<flag>[A-Z])?\s*(?P<scores>(?:\d+\s+){3,5})\((?P<total>\d+)\)\s*$/', $L1, $m1)) {
        $out['team']  = trim($m1['team']);
        $out['coach'] = trim($m1['coach']);
        $flag = isset($m1['flag']) ? trim($m1['flag']) : '';
        if ($flag !== '') $out['safe'] = $flag; // goes to 'safe' column per your sample

        // split scores
        $nums = preg_split('/\s+/', trim($m1['scores']));
        // first four = q1..q4, optional fifth = ot
        $out['q1'] = (int)$nums[0];
        $out['q2'] = (int)$nums[1];
        $out['q3'] = (int)$nums[2];
        $out['q4'] = (int)$nums[3];
        if (isset($nums[4])) $out['ot'] = (int)$nums[4];
        $out['score'] = (int)$m1['total'];
    } else {
        // Fallback: try a looser pattern
        if (preg_match('/^(?P<team>.+?)\s*\((?P<coach>[^)]+)\)/', $L1, $m0)) {
            $out['team']  = trim($m0['team']);
            $out['coach'] = trim($m0['coach']);
        }
    }

    // Line 2: "FG 4/3, EP 5/5, CP 0/0, Punt 2, 3rd 5/12, 4th 0/0, 1st 27"
    $out = array_merge($out, _cp_parse_misc_line($L2));

    // Line 3: "Pass 22 for 34, 436 yds, Lg t66, 4 TD, 65%, In 0, Hrd 2, Skd 1"
    $out = array_merge($out, _cp_parse_pass_line($L3));

    // Line 4: "Rush 26 for 129 yds, Lg 13, 1 TD, avg 5.0, Fm 0, QB 2 for 17 yds"
    $out = array_merge($out, _cp_parse_rush_line($L4));

    // Line 5: "KR 2 for 11 yds, PR 5 for 83 yds, FumR 0 for 0 yds"
    $out = array_merge($out, _cp_parse_st_line($L5));

    // Line 6: "Calls Fm S J, Run DW QR, Pass DP DL, Def RD ND"
    $out = array_merge($out, _cp_parse_calls_line($L6));

    return $out;
}

function _cp_parse_misc_line($s) {
    $o = [];
    // FG x/y
    if (preg_match('/FG\s+(\d+)\s*\/\s*(\d+)/i', $s, $m)) { $o['fga']=(int)$m[1]; $o['fgg']=(int)$m[2]; }
    if (preg_match('/EP\s+(\d+)\s*\/\s*(\d+)/i', $s, $m)) { $o['epa']=(int)$m[1]; $o['epg']=(int)$m[2]; }
    if (preg_match('/CP\s+(\d+)\s*\/\s*(\d+)/i', $s, $m)) { $o['cva']=(int)$m[1]; $o['cvg']=(int)$m[2]; }
    if (preg_match('/Punt\s+(\d+)/i', $s, $m)) { $o['punts']=(int)$m[1]; }
    if (preg_match('/3rd\s+(\d+)\s*\/\s*(\d+)/i', $s, $m)) { $o['thirdcon']=(int)$m[1]; $o['thirddowns']=(int)$m[2]; }
    if (preg_match('/4th\s+(\d+)\s*\/\s*(\d+)/i', $s, $m)) { $o['fourthcon']=(int)$m[1]; $o['fourthdowns']=(int)$m[2]; }
    if (preg_match('/1st\s+(\d+)/i', $s, $m)) { $o['firstd']=(int)$m[1]; }
    return $o;
}

function _cp_parse_pass_line($s) {
    $o = [];
    if (preg_match('/Pass\s+(\d+)\s+for\s+(\d+)/i', $s, $m)) { $o['passcmp']=(int)$m[1]; $o['passatt']=(int)$m[2]; }
    if (preg_match('/,\s*(\d+)\s*yds/i', $s, $m)) { $o['passyds']=(int)$m[1]; }
    // Lg t66 OR Lg 66 (optionally with 't')
    if (preg_match('/Lg\s+t?(\d+)/i', $s, $m)) {
        $o['passlng']=(int)$m[1];
        $o['passlngtd'] = (stripos($s, 'Lg t') !== false) ? 'Y' : null;
    }
    if (preg_match('/,\s*(\d+)\s*TD/i', $s, $m)) { $o['passtd']=(int)$m[1]; }
    if (preg_match('/,\s*(\d+)\s*%/i', $s, $m)) { $o['passpct']=(int)$m[1]; }
    if (preg_match('/In\s+(\d+)/i', $s, $m)) { $o['interception']=(int)$m[1]; }
    if (preg_match('/Hrd\s+(\d+)/i', $s, $m)) { $o['hrd']=(int)$m[1]; }
    if (preg_match('/Skd\s+(\d+)/i', $s, $m)) { $o['skd']=(int)$m[1]; }
    return $o;
}

function _cp_parse_rush_line($s) {
    $o = [];
    if (preg_match('/Rush\s+(\d+)\s+for\s+(\d+)\s*yds/i', $s, $m)) { $o['rush']=(int)$m[1]; $o['rushyds']=(int)$m[2]; }
    if (preg_match('/Lg\s+t?(\d+)/i', $s, $m)) {
        $o['rushlng']=(int)$m[1];
        $o['rushlngtd'] = (stripos($s, 'Lg t') !== false) ? 'Y' : null;
    }
    if (preg_match('/,\s*(\d+)\s*TD/i', $s, $m)) { $o['rushtd']=(int)$m[1]; }
    if (preg_match('/Fm\s+(\d+)/i', $s, $m)) { $o['fum']=(int)$m[1]; }
    if (preg_match('/QB\s+(\d+)\s+for\s+(-?\d+)\s*yds/i', $s, $m)) { $o['qbatt']=(int)$m[1]; $o['qbyds']=(int)$m[2]; }
    return $o;
}

function _cp_parse_st_line($s) {
    $o = [];
    if (preg_match('/KR\s+(\d+)\s+for\s+(\d+)\s*yds(?:,\s*(\d+)\s*TD)?/i', $s, $m)) {
        $o['kr']=(int)$m[1]; $o['kryds']=(int)$m[2]; $o['krtd']= isset($m[3]) ? (int)$m[3] : 0;
    }
    if (preg_match('/PR\s+(\d+)\s+for\s+(\d+)\s*yds(?:,\s*(\d+)\s*TD)?/i', $s, $m)) {
        $o['pr']=(int)$m[1]; $o['pryds']=(int)$m[2]; $o['prtd']= isset($m[3]) ? (int)$m[3] : 0;
    }
    // FumR present but no destination fields in schema -> ignore
    return $o;
}

function _cp_parse_calls_line($s) {
    $o = [];
    // "Calls Fm S J, Run DW QR, Pass DP DL, Def RD ND"
    // Capture up to two tokens each; allow 0/1/2 as per your note
    if (preg_match('/Fm\s+([A-Z]+)(?:\s+([A-Z]+))?/i', $s, $m)) { $o['form1']=strtoupper($m[1]); $o['form2']= isset($m[2])?strtoupper($m[2]):null; }
    if (preg_match('/Run\s+([A-Z]+)(?:\s+([A-Z]+))?/i', $s, $m)) { $o['run1']=strtoupper($m[1]);  $o['run2']= isset($m[2])?strtoupper($m[2]):null; }
    if (preg_match('/Pass\s+([A-Z]+)(?:\s+([A-Z]+))?/i', $s, $m)) { $o['pass1']=strtoupper($m[1]); $o['pass2']= isset($m[2])?strtoupper($m[2]):null; }
    if (preg_match('/Def\s+([A-Z]+)(?:\s+([A-Z]+))?/i', $s, $m)) { $o['def1']=strtoupper($m[1]);  $o['def2']= isset($m[2])?strtoupper($m[2]):null; }
    return $o;
}

/**
 * Build a single row (either road or home).
 */
function _cp_build_row($league, $season, $week, array $me, array $opp, $homeaway, array $franchiseMap) {
    $id_game = ($league ?? '') . ($season ?? '') . ($week ?? '') . preg_replace('/\s+/', '', $me['team'] ?? '');
    $franchise = $franchiseMap[$me['team']] ?? null;
    $opp_franchise = $franchiseMap[$opp['team']] ?? null;

    // outcome
    $win=0; $lose=0; $tie=0;
    if (isset($me['score'], $opp['score'])) {
        if ($me['score'] > $opp['score']) $win=1;
        elseif ($me['score'] < $opp['score']) $lose=1;
        else $tie=1;
    }

    return [
        'id_game'=>$id_game,
        'league'=>$league,
        'season'=>$season,
        'week'=>$week,
        'team'=>$me['team'] ?? null,
        'franchise'=>$franchise,
        'coach'=>$me['coach'] ?? null,
        'qb'=>$me['qb'] ?? null,
        'safe'=>$me['safe'] ?? null,

        'q1'=>$me['q1']??0,'q2'=>$me['q2']??0,'q3'=>$me['q3']??0,'q4'=>$me['q4']??0,'ot'=>$me['ot']??0,'score'=>$me['score']??0,

        'fga'=>$me['fga']??0,'fgg'=>$me['fgg']??0,'epa'=>$me['epa']??0,'epg'=>$me['epg']??0,'cva'=>$me['cva']??0,'cvg'=>$me['cvg']??0,'punts'=>$me['punts']??0,
        'thirdcon'=>$me['thirdcon']??0,'thirddowns'=>$me['thirddowns']??0,'fourthcon'=>$me['fourthcon']??0,'fourthdowns'=>$me['fourthdowns']??0,'firstd'=>$me['firstd']??0,

        'passcmp'=>$me['passcmp']??0,'passatt'=>$me['passatt']??0,'passyds'=>$me['passyds']??0,
        'passlng'=>$me['passlng'] ?? null, 'passlngtd'=>$me['passlngtd'] ?? null, 'passtd'=>$me['passtd']??0,'passpct'=>$me['passpct']??null,
        'interception'=>$me['interception']??0,'hrd'=>$me['hrd']??0,'skd'=>$me['skd']??0,

        'rush'=>$me['rush']??0,'rushyds'=>$me['rushyds']??0,
        'rushlng'=>$me['rushlng'] ?? null, 'rushlngtd'=>$me['rushlngtd'] ?? null,'rushtd'=>$me['rushtd']??0,
        'fum'=>$me['fum']??0,'qbatt'=>$me['qbatt']??0,'qbyds'=>$me['qbyds']??0,

        'kr'=>$me['kr']??0,'kryds'=>$me['kryds']??0,'krtd'=>$me['krtd']??0,'pr'=>$me['pr']??0,'pryds'=>$me['pryds']??0,'prtd'=>$me['prtd']??0,

        'form1'=>$me['form1']??null,'form2'=>$me['form2']??null,'run1'=>$me['run1']??null,'run2'=>$me['run2']??null,'pass1'=>$me['pass1']??null,'pass2'=>$me['pass2']??null,'def1'=>$me['def1']??null,'def2'=>$me['def2']??null,

        'homeaway'=>$homeaway,
        'gametype'=>null, // not provided; set here if you later derive from filename

        'opp_team'=>$opp['team']??null,'opp_franchise'=>$opp_franchise,'opp_coach'=>$opp['coach']??null,'opp_qb'=>$opp['qb']??null,'opp_safe'=>$opp['safe']??null,

        'opp_q1'=>$opp['q1']??0,'opp_q2'=>$opp['q2']??0,'opp_q3'=>$opp['q3']??0,'opp_q4'=>$opp['q4']??0,'opp_ot'=>$opp['ot']??0,'opp_score'=>$opp['score']??0,

        'opp_fga'=>$opp['fga']??0,'opp_fgg'=>$opp['fgg']??0,'opp_epa'=>$opp['epa']??0,'opp_epg'=>$opp['epg']??0,'opp_cva'=>$opp['cva']??0,'opp_cvg'=>$opp['cvg']??0,'opp_punts'=>$opp['punts']??0,
        'opp_thirdcon'=>$opp['thirdcon']??0,'opp_thirddowns'=>$opp['thirddowns']??0,'opp_fourthcon'=>$opp['fourthcon']??0,'opp_fourthdowns'=>$opp['fourthdowns']??0,'opp_firstd'=>$opp['firstd']??0,

        'opp_passcmp'=>$opp['passcmp']??0,'opp_passatt'=>$opp['passatt']??0,'opp_passyds'=>$opp['passyds']??0,
        'opp_passlng'=>$opp['passlng'] ?? null, 'opp_passlngtd'=>$opp['passlngtd'] ?? null,'opp_passtd'=>$opp['passtd']??0,'opp_passpct'=>$opp['passpct']??null,
        'opp_interception'=>$opp['interception']??0,'opp_hrd'=>$opp['hrd']??0,'opp_skd'=>$opp['skd']??0,

        'opp_rush'=>$opp['rush']??0,'opp_rushyds'=>$opp['rushyds']??0,
        'opp_rushlng'=>$opp['rushlng'] ?? null, 'opp_rushlngtd'=>$opp['rushlngtd'] ?? null,'opp_rushtd'=>$opp['rushtd']??0,
        'opp_fum'=>$opp['fum']??0,'opp_qbatt'=>$opp['qbatt']??0,'opp_qbyds'=>$opp['qbyds']??0,

        'opp_kr'=>$opp['kr']??0,'opp_kryds'=>$opp['kryds']??0,'opp_krtd'=>$opp['krtd']??0,'opp_pr'=>$opp['pr']??0,'opp_pryds'=>$opp['pryds']??0,'opp_prtd'=>$opp['prtd']??0,

        'opp_form1'=>$opp['form1']??null,'opp_form2'=>$opp['form2']??null,'opp_run1'=>$opp['run1']??null,'opp_run2'=>$opp['run2']??null,'opp_pass1'=>$opp['pass1']??null,'opp_pass2'=>$opp['pass2']??null,'opp_def1'=>$opp['def1']??null,'opp_def2'=>$opp['def2']??null,

        'win'=>$win,'lose'=>$lose,'tie'=>$tie,

        'modification_by'=>'asm',
        'modification_from'=>'asm'
    ];
}


if (!function_exists('_cp_bind_params')) {
    function _cp_bind_params(array $row, string $sql = null) : array {
        // Remaps used by SQL
        if (array_key_exists('opp_qbatt', $row)) $row['opp_qbatt_opp'] = $row['opp_qbatt'];
        if (array_key_exists('opp_qbyds', $row)) $row['opp_qbyds_opp'] = $row['opp_qbyds'];

        // Normalize empty strings -> NULL
        foreach ($row as $k => $v) {
            if ($v === '') $row[$k] = null;
        }

        // If SQL provided, keep ONLY placeholders present in the SQL, in SQL order
        if ($sql !== null) {
            preg_match_all('/:\w+/', $sql, $m);
            $needTokens = array_values(array_unique($m[0])); // e.g., [':league', ':team', ...]
            $final = [];
            foreach ($needTokens as $tok) {
                $key = substr($tok, 1); // drop ':'
                $final[$key] = array_key_exists($key, $row) ? $row[$key] : null;
            }
            return $final; // exact match: count($final) == count($needTokens)
        }

        return $row; // fallback if no SQL passed
    }
}


if (!function_exists('_cp_param_diff')) {
    function _cp_param_diff(string $sql, array $params): array {
        preg_match_all('/:\w+/', $sql, $m);
        $need = array_values(array_unique($m[0])); // tokens in SQL like :league
        $have = [];
        foreach (array_keys($params) as $k) {
            $have[] = ':' . $k;
        }
        $missing = array_values(array_diff($need, $have));
        $extra   = array_values(array_diff($have, $need));
        return [$missing, $extra, $need, $have];
    }
}

