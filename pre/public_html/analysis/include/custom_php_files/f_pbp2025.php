<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require_once 'f_functions.php';
require_once 'g_functions.php';

/* ---------------------------
   W3 helpers (no <head>/<body>)
---------------------------- */
$render_header = function(string $title, string $subtitle = '') {
  echo '<div class="w3-container w3-padding">';
  echo '  <h2 class="w3-margin-0">'.$title.'</h2>';
  if ($subtitle !== '') {
    echo '  <p class="w3-text-grey w3-small">'.$subtitle.'</p>';
  }
  echo '</div>';
};

$render_card = function(string $title, string $content_html, string $colorClass = 'w3-white') {
  echo '<div class="w3-card w3-round-xxlarge w3-margin-top '.$colorClass.'">';
  echo '  <div class="w3-container w3-padding">';
  if ($title !== '') echo '    <h3 class="w3-margin-top">'.$title.'</h3>';
  echo $content_html;
  echo '  </div>';
  echo '</div>';
};

$render_list = function(string $title, array $items, string $paleClass = 'w3-pale-blue') use ($render_card) {
  ob_start();
  echo '<ul class="w3-ul w3-border w3-hoverable w3-small">';
  foreach ($items as $it) echo '<li>'.htmlspecialchars($it, ENT_QUOTES).'</li>';
  echo '</ul>';
  $render_card($title, ob_get_clean(), $paleClass);
};

$render_kv = function(array $rows) {
  // rows: [[label, value, optionalBadgeClass]]
  echo '<div class="w3-row-padding">';
  foreach ($rows as $r) {
    $label = $r[0]; $value = $r[1]; $badge = $r[2] ?? 'w3-blue';
    echo '<div class="w3-col s12 m6 l4 w3-margin-bottom">';
    echo '  <div class="w3-container w3-white w3-round-xxlarge w3-padding w3-border">';
    echo '    <div class="w3-small w3-text-grey">'.htmlspecialchars($label, ENT_QUOTES).'</div>';
    echo '    <div class="w3-xlarge"><span class="w3-badge '.$badge.' w3-large" style="vertical-align:middle">'.htmlspecialchars($value, ENT_QUOTES).'</span></div>';
    echo '  </div>';
    echo '</div>';
  }
  echo '</div>';
};

// override your plain output text with a subtle card line
$w3out = function(string $html) use ($render_card) {
  $render_card('', '<div class="w3-small">'.$html.'</div>', 'w3-white');
};

/* ---------------------------
   Page heading
---------------------------- */
$render_header('Analysis Builder', 'Play-by-play aggregation, coaches backfill, and adhoc rollups');

/* ---------------------------
   Truncate phase (show nicely)
---------------------------- */
$to_truncate = [
  'n_s_mv_off','n_s_mv_def','n_s_pe_off','n_s_pe_def','n_s_pi_off','n_s_pi_def',
  'n_s_mv_off_f','n_s_mv_def_f','n_s_pe_off_f','n_s_pe_def_f','n_s_pi_off_f','n_s_pi_def_f',
  'n_OffDef','n_OffDefForm','n_OffForm','n_roundup'
];

$ok = []; $fail = [];
foreach ($to_truncate as $tbl) {
  $_cp_sql = "TRUNCATE `$tbl`;";
  try {
    $stmt = $conn->prepare($_cp_sql);
    $stmt->execute();
    $ok[] = "$tbl — truncated";
  } catch (Throwable $e) {
    $fail[] = "$tbl — ".$e->getMessage();
  }
}

ob_start();
echo '<div class="w3-row-padding">';
echo '  <div class="w3-col s12 m6">';
echo '    <h4 class="w3-margin-top">Successful</h4>';
echo '    <ul class="w3-ul w3-border w3-hoverable w3-small">';
foreach ($ok as $line) echo '<li class="w3-text-green"><i class="w3-small">✔</i> '.htmlspecialchars($line, ENT_QUOTES).'</li>';
echo '    </ul>';
echo '  </div>';
echo '  <div class="w3-col s12 m6">';
echo '    <h4 class="w3-margin-top">Errors</h4>';
echo '    <ul class="w3-ul w3-border w3-hoverable w3-small">';
if (!$fail) echo '<li class="w3-text-grey">None</li>';
foreach ($fail as $line) echo '<li class="w3-text-red"><i class="w3-small">✖</i> '.htmlspecialchars($line, ENT_QUOTES).'</li>';
echo '    </ul>';
echo '  </div>';
echo '</div>';
$render_card('Reset phase', ob_get_clean(), 'w3-pale-yellow');

/* ---------------------------
   Main league loop
---------------------------- */
$_cp_sql = "SELECT `myleague`, `myteam`, `mycode` FROM `n_relevant` WHERE 1 ORDER BY `myleague` ASC, `myteam` ASC";
$result = $conn->prepare($_cp_sql);
$result->execute();

$j=0; $mycount=0; $number_of_rowstotal=0;

while($row = fetch_row_db($result)){
  $_cp_myleague = $row[0];
  $_cp_myteam   = $row[1];
  $_cp_mycode   = $row[2];

  $_cp_sql2 = "SELECT `a_season` FROM `n_playbyplay` WHERE `a_league`=? AND `a_poss`=? GROUP BY `a_season` ORDER BY `a_season` DESC;";
  $result2 = $conn->prepare($_cp_sql2);
  $result2->execute([$_cp_myleague,$_cp_myteam]);
  $number_of_rows = $result2->rowCount();

  $w3out("<span class='w3-tag w3-blue w3-round'>".$_cp_myteam."</span> <span class='w3-tag w3-light-grey w3-round'>".$_cp_myleague."</span> — Processing <b>$number_of_rows</b> seasons");

  while($row2 = fetch_row_db($result2)){
    $_cp_myseason = $row2[0];

    // Offensive season totals (no formation)
    $_cp_sql3 = "SELECT `a_ocall` as `Play`, COUNT(`a_ocall`) as `Number`, ROUND(AVG(`a_yards`),2) as `Average`
                 FROM `n_playbyplay`
                 WHERE `a_league`=? AND `a_off`=? AND `a_season`=? AND `a_form` NOT IN ('P','X','F')
                   AND `a_penalty`<>'1' AND `a_intercept`<>1 AND `a_fumble`<>1
                 GROUP BY `a_ocall` ORDER BY AVG(`a_yards`) DESC";
    $result3 = $conn->prepare($_cp_sql3);
    $result3->execute([$_cp_myleague,$_cp_myteam,$_cp_myseason]);

    while($row3 = fetch_row_db($result3)){
      $_cp_myplay    = $row3[0];
      $_cp_mynumber  = $row3[1];
      $_cp_myaverage = $row3[2];
      $_cp_sql4 = "INSERT INTO `n_s_".strtolower($_cp_myteam)."_off`
                  (`playID`, `league`, `season`, `playcall`, `number`, `average`)
                  VALUES (NULL, :lg, :sn, :pc, :num, :avg)";
      $result4 = $conn->prepare($_cp_sql4);
      $result4->execute([
        ':lg'=>$_cp_myleague, ':sn'=>$_cp_myseason, ':pc'=>$_cp_myplay,
        ':num'=>$_cp_mynumber, ':avg'=>$_cp_myaverage
      ]);
    }

    // Defensive season totals (no formation)
    $_cp_sql3 = "SELECT `a_dcall` as `Play`, COUNT(`a_ocall`) as `Number`, ROUND(AVG(`a_yards`),2) as `Average`
                 FROM `n_playbyplay`
                 WHERE `a_league`=? AND `a_def`=? AND `a_season`=? AND `a_form` NOT IN ('P','X','F')
                   AND `a_penalty`<>'1' AND `a_intercept`<>1 AND `a_fumble`<>1
                 GROUP BY `a_dcall` ORDER BY AVG(`a_yards`) DESC";
    $result3 = $conn->prepare($_cp_sql3);
    $result3->execute([$_cp_myleague,$_cp_myteam,$_cp_myseason]);

    while($row3 = fetch_row_db($result3)){
      $_cp_myplay    = $row3[0];
      $_cp_mynumber  = $row3[1];
      $_cp_myaverage = $row3[2];
      $_cp_sql4 = "INSERT INTO `n_s_".strtolower($_cp_myteam)."_def`
                  (`playID`, `league`, `season`, `playcall`, `number`, `average`)
                  VALUES (NULL, :lg, :sn, :pc, :num, :avg)";
      $result4 = $conn->prepare($_cp_sql4);
      $result4->execute([
        ':lg'=>$_cp_myleague, ':sn'=>$_cp_myseason, ':pc'=>$_cp_myplay,
        ':num'=>$_cp_mynumber, ':avg'=>$_cp_myaverage
      ]);
    }

    // Offensive season totals (with formation)
    $_cp_sql3 = "SELECT `a_ocall` as `Play`, COUNT(`a_ocall`) as `Number`, ROUND(AVG(`a_yards`),2) as `Average`, `a_form` as `Formation`
                 FROM `n_playbyplay`
                 WHERE `a_league`=? AND `a_off`=? AND `a_season`=? AND `a_form` NOT IN ('P','X','F')
                   AND `a_penalty`<>'1' AND `a_intercept`<>1 AND `a_fumble`<>1
                 GROUP BY `a_form`,`a_ocall` ORDER BY AVG(`a_yards`) DESC";
    $result3 = $conn->prepare($_cp_sql3);
    $result3->execute([$_cp_myleague,$_cp_myteam,$_cp_myseason]);

    while($row3 = fetch_row_db($result3)){
      $_cp_myplay      = $row3[0];
      $_cp_mynumber    = $row3[1];
      $_cp_myaverage   = $row3[2];
      $_cp_myformation = $row3[3];
      $_cp_sql4 = "INSERT INTO `n_s_".strtolower($_cp_myteam)."_off_f`
                  (`playID`, `league`, `season`, `formation`, `playcall`, `number`, `average`)
                  VALUES (NULL, :lg, :sn, :fm, :pc, :num, :avg)";
      $result4 = $conn->prepare($_cp_sql4);
      $result4->execute([
        ':lg'=>$_cp_myleague, ':sn'=>$_cp_myseason, ':fm'=>$_cp_myformation, ':pc'=>$_cp_myplay,
        ':num'=>$_cp_mynumber, ':avg'=>$_cp_myaverage
      ]);
    }

    // Defensive season totals (with formation) — includes a_form in selection
    $_cp_sql3 = "SELECT `a_dcall` as `Play`, COUNT(`a_ocall`) as `Number`, ROUND(AVG(`a_yards`),2) as `Average`, `a_form` as `Formation`
                 FROM `n_playbyplay`
                 WHERE `a_league`=? AND `a_def`=? AND `a_season`=? AND `a_form` NOT IN ('P','X','F')
                   AND `a_penalty`<>'1' AND `a_intercept`<>1 AND `a_fumble`<>1
                 GROUP BY `a_form`,`a_dcall` ORDER BY AVG(`a_yards`) DESC";
    $result3 = $conn->prepare($_cp_sql3);
    $result3->execute([$_cp_myleague,$_cp_myteam,$_cp_myseason]);

    while($row3 = fetch_row_db($result3)){
      $_cp_myplay      = $row3[0];
      $_cp_mynumber    = $row3[1];
      $_cp_myaverage   = $row3[2];
      $_cp_myformation = $row3[3];
      $_cp_sql4 = "INSERT INTO `n_s_".strtolower($_cp_myteam)."_def_f`
                  (`playID`, `league`, `season`, `formation`, `playcall`, `number`, `average`)
                  VALUES (NULL, :lg, :sn, :fm, :pc, :num, :avg)";
      $result4 = $conn->prepare($_cp_sql4);
      $result4->execute([
        ':lg'=>$_cp_myleague, ':sn'=>$_cp_myseason, ':fm'=>$_cp_myformation, ':pc'=>$_cp_myplay,
        ':num'=>$_cp_mynumber, ':avg'=>$_cp_myaverage
      ]);
    }

    $mycount++;
  }
}

/* ---------------------------
   Adhoc tables (pretty echo)
---------------------------- */
$render_card('Populating adhoc tables', '<p class="w3-small">Creating Off/Def matrices and roundups…</p>', 'w3-pale-green');

$_cp_sql = "INSERT INTO `n_OffDef` SELECT NULL, concat(`a`.`a_ocall`,`a`.`a_dcall`) , `b`.`playtype` , `a`.`a_ocall` , `a`.`a_dcall` , count(`a`.`a_id`) , avg(`a`.`a_yards`)
            FROM (`n_playbyplay` `a` join `n_playtypes` `b` on(`a`.`a_ocall` = `b`.`play`))
            WHERE `b`.`playtype`<>'ST'
            GROUP BY `b`.`playtype`, `a`.`a_ocall`, `a`.`a_dcall`
            ORDER BY `a`.`a_ocall` ASC, `a`.`a_dcall` ASC, avg(`a`.`a_yards`) ASC;";
$stmt = $conn->prepare($_cp_sql); $stmt->execute();

$_cp_sql = "INSERT INTO `n_OffDefForm` SELECT NULL, concat(`a`.`a_form` ,`a`.`a_ocall`,`a`.`a_dcall`) , `b`.`playtype` , `a`.`a_form` , `a`.`a_ocall` , `a`.`a_dcall` , count(`a`.`a_id`) , avg(`a`.`a_yards`)
            FROM (`n_playbyplay` `a` join `n_playtypes` `b` on(`a`.`a_ocall` = `b`.`play`))
            WHERE `b`.`playtype`<>'ST'
            GROUP BY `a`.`a_form`, `b`.`playtype`, `a`.`a_ocall`, `a`.`a_dcall`
            ORDER BY `a`.`a_form` ASC, `a`.`a_ocall` ASC, `a`.`a_dcall` ASC, avg(`a`.`a_yards`) ASC;";
$stmt = $conn->prepare($_cp_sql); $stmt->execute();

$_cp_sql = "INSERT INTO `n_OffForm` SELECT NULL, concat(`a`.`a_form` ,`a`.`a_ocall`) , `b`.`playtype` , `a`.`a_form` , `a`.`a_ocall` ,  count(`a`.`a_id`) , avg(`a`.`a_yards`)
            FROM (`n_playbyplay` `a` join `n_playtypes` `b` on(`a`.`a_ocall` = `b`.`play`))
            WHERE `b`.`playtype`<>'ST'
            GROUP BY `a`.`a_form`, `a`.`a_ocall`, `b`.`playtype`
            ORDER BY `a`.`a_form` ASC, `a`.`a_ocall` ASC, avg(`a`.`a_yards`) ASC;";
$stmt = $conn->prepare($_cp_sql); $stmt->execute();

$_cp_sql = "INSERT INTO `n_roundup`
            SELECT NULL, concat(`f_games`.`league`,`f_games`.`season`,`f_games`.`week`,`f_games`.`team`) AS `roundup_id`,
                   `f_games`.`league`,`f_games`.`season`,`f_games`.`week`,`f_games`.`team`,
                   `f_games`.`passatt`,`f_games`.`rush`,`f_games`.`form1`,`f_games`.`form2`,`f_games`.`run1`,`f_games`.`run2`,
                   `f_games`.`pass1`,`f_games`.`pass2`,`f_games`.`def1`,`f_games`.`def2`,
                   concat(`f_games`.`score`,'-',`f_games`.`opp_score`) AS `score`
            FROM `f_games`
            ORDER BY `league` DESC, `season` DESC, `week` DESC;";
$stmt = $conn->prepare($_cp_sql); $stmt->execute();

/* ---------------------------
   Pro PBP coach backfill
---------------------------- */
$_cp_myoffcoach=""; $_cp_mydefcoach="";

$_cp_sql = "SELECT DISTINCT a.`league`,a.`season`,a.`week`
            FROM `f_games` a
            WHERE a.league LIKE 'NFL%' AND NOT EXISTS (
              SELECT 1 FROM `fp_gamecoaches` b
              WHERE a.league=b.league AND a.season=b.season AND a.week=b.week
            );";
$_cp_weeks=nz_pdo_array($_cp_sql,$conn);

if (!empty($_cp_weeks)) {
  $render_card('Missing NFL coach weeks', '<p class="w3-small">'.count($_cp_weeks).' week(s) to populate…</p>', 'w3-pale-red');
}

$str='<span class="w3-tag w3-indigo w3-round">Pro Play by Plays</span>';
$render_card('', "<p class='w3-small w3-text-grey'>Scanning for missing coaches…</p>", 'w3-white');

$_cp_sql3 = "SELECT `a_id`,`a_league`, `a_season`, `a_week`,`a_off`,`a_def`,`n_offcoach`, `n_defcoach`
             FROM `n_playbyplay`
             WHERE `a_league` LIKE 'NFLAR%' AND (n_offcoach IS NULL OR n_offcoach = '' OR n_defcoach IS NULL OR n_defcoach = '')
             ORDER BY `a_id`,`a_league` ASC, `a_season` ASC, `a_week` ASC;";
$stmt3 = $conn->prepare($_cp_sql3);
$stmt3->execute();
$number_of_rows = $stmt3->rowCount();
$number_of_rowsp = number_format($number_of_rows);
$number_of_rowstotal += $number_of_rows;

$render_card('Finding Pro PBP records', "<p class='w3-large'><span class='w3-badge w3-blue'>$number_of_rowsp</span> records located</p>", 'w3-pale-blue');

$i=0;
while($row3 = fetch_row_db($stmt3)){
  $_cp_myleague=$row3[1]; $_cp_myseason=$row3[2]; $_cp_myweek=$row3[3];
  $_cp_myoff=$row3[4];    $_cp_mydef=$row3[5];

  // OFF coach lookup (PDO)
  $_cp_sql4 = "SELECT `coach`
               FROM `fp_gamecoaches`
               WHERE `league`=:lg AND `season`=:sn AND `week`=:wk AND `abbr`=:ab";
  $stmt4 = $conn->prepare($_cp_sql4);
  $stmt4->execute([':lg'=>$_cp_myleague, ':sn'=>$_cp_myseason, ':wk'=>$_cp_myweek, ':ab'=>$_cp_myoff]);
  while ($row4 = fetch_row_db($stmt4)) { $_cp_myoffcoach = $row4[0]; }

  // OFF coach update (PDO)
  $_cp_sql5 = "UPDATE `n_playbyplay`
               SET `n_offcoach`=:co
               WHERE `a_league`=:lg AND `a_season`=:sn AND `a_week`=:wk AND `a_off`=:ab";
  $stmt5 = $conn->prepare($_cp_sql5);
  $stmt5->execute([':co'=>$_cp_myoffcoach, ':lg'=>$_cp_myleague, ':sn'=>$_cp_myseason, ':wk'=>$_cp_myweek, ':ab'=>$_cp_myoff]);

  // DEF coach lookup (PDO)
  $_cp_sql6 = "SELECT `coach`
               FROM `fp_gamecoaches`
               WHERE `league`=:lg AND `season`=:sn AND `week`=:wk AND `abbr`=:ab";
  $stmt6 = $conn->prepare($_cp_sql6);
  $stmt6->execute([':lg'=>$_cp_myleague, ':sn'=>$_cp_myseason, ':wk'=>$_cp_myweek, ':ab'=>$_cp_mydef]);
  while ($row6 = fetch_row_db($stmt6)) { $_cp_mydefcoach = $row6[0]; }

  // DEF coach update (PDO)
  $_cp_sql7 = "UPDATE `n_playbyplay`
               SET `n_defcoach`=:co
               WHERE `a_league`=:lg AND `a_season`=:sn AND `a_week`=:wk AND `a_def`=:ab";
  $stmt7 = $conn->prepare($_cp_sql7);
  $stmt7->execute([':co'=>$_cp_mydefcoach, ':lg'=>$_cp_myleague, ':sn'=>$_cp_myseason, ':wk'=>$_cp_myweek, ':ab'=>$_cp_mydef]);

  $i++;
}

// Report Pro
$render_card('', "<p class='w3-small'>Processed <b>$i</b> Pro PBP Records</p>", 'w3-white');

/* ---------------------------
   College PBP coach backfill
---------------------------- */
$_cp_sql = "SELECT DISTINCT a.`league`,a.`season`,a.`week`
            FROM `f_games` a
            WHERE a.league LIKE 'NC%' AND NOT EXISTS (
              SELECT 1 FROM `fc_gamecoaches` b
              WHERE a.league=b.league AND a.season=b.season AND a.week=b.week
            );";
$_cp_weeks=nz_pdo_array($_cp_sql,$conn);

if (!empty($_cp_weeks)) {
  $render_card('Missing NCAA coach weeks', '<p class="w3-small">'.count($_cp_weeks).' week(s) to populate…</p>', 'w3-pale-red');
}

$_cp_sql3 = "SELECT `a_id`,`a_league`, `a_season`, `a_week`,`a_off`,`a_def`,`n_offcoach`, `n_defcoach`
             FROM `n_playbyplay`
             WHERE `a_league` LIKE 'NCAA5%' AND (n_offcoach IS NULL OR n_offcoach = '' OR n_defcoach IS NULL OR n_defcoach = '')
             ORDER BY `a_id`,`a_league` ASC, `a_season` ASC, `a_week` ASC;";
$stmt3 = $conn->prepare($_cp_sql3);
$stmt3->execute();
$number_of_rows = $stmt3->rowCount();
$number_of_rowsp = number_format($number_of_rows);
$number_of_rowstotal += $number_of_rows;

$render_card('Finding College PBP records', "<p class='w3-large'><span class='w3-badge w3-blue'>$number_of_rowsp</span> records located</p>", 'w3-pale-blue');

$i=0;
while($row3 = fetch_row_db($stmt3)){
  $_cp_myleague=$row3[1]; $_cp_myseason=$row3[2]; $_cp_myweek=$row3[3];
  $_cp_myoff=$row3[4];    $_cp_mydef=$row3[5];

  // OFF coach lookup (PDO)
  $_cp_sql4 = "SELECT `coach`
               FROM `fc_gamecoaches`
               WHERE `league`=:lg AND `season`=:sn AND `week`=:wk AND `abbr`=:ab";
  $stmt4 = $conn->prepare($_cp_sql4);
  $stmt4->execute([':lg'=>$_cp_myleague, ':sn'=>$_cp_myseason, ':wk'=>$_cp_myweek, ':ab'=>$_cp_myoff]);
  while ($row4 = fetch_row_db($stmt4)) { $_cp_myoffcoach = $row4[0]; }

  // OFF coach update (PDO)
  $_cp_sql5 = "UPDATE `n_playbyplay`
               SET `n_offcoach`=:co
               WHERE `a_league`=:lg AND `a_season`=:sn AND `a_week`=:wk AND `a_off`=:ab";
  $stmt5 = $conn->prepare($_cp_sql5);
  $stmt5->execute([':co'=>$_cp_myoffcoach, ':lg'=>$_cp_myleague, ':sn'=>$_cp_myseason, ':wk'=>$_cp_myweek, ':ab'=>$_cp_myoff]);

  // DEF coach lookup (PDO)
  $_cp_sql6 = "SELECT `coach`
               FROM `fc_gamecoaches`
               WHERE `league`=:lg AND `season`=:sn AND `week`=:wk AND `abbr`=:ab";
  $stmt6 = $conn->prepare($_cp_sql6);
  $stmt6->execute([':lg'=>$_cp_myleague, ':sn'=>$_cp_myseason, ':wk'=>$_cp_myweek, ':ab'=>$_cp_mydef]);
  while ($row6 = fetch_row_db($stmt6)) { $_cp_mydefcoach = $row6[0]; }

  // DEF coach update (PDO)
  $_cp_sql7 = "UPDATE `n_playbyplay`
               SET `n_defcoach`=:co
               WHERE `a_league`=:lg AND `a_season`=:sn AND `a_week`=:wk AND `a_def`=:ab";
  $stmt7 = $conn->prepare($_cp_sql7);
  $stmt7->execute([':co'=>$_cp_mydefcoach, ':lg'=>$_cp_myleague, ':sn'=>$_cp_myseason, ':wk'=>$_cp_myweek, ':ab'=>$_cp_mydef]);

  $i++;
}

// Report College
$render_card('', "<p class='w3-small'>Processed <b>$i</b> College PBP Records</p>", 'w3-white');

/* ---------------------------
   Footer stats
---------------------------- */
$time_end = microtime(true);
$time = $time_end - $time_start;

ob_start();
$render_kv([
  ['Total PBP rows touched', number_format($number_of_rowstotal), 'w3-deep-purple'],
  ['Teams processed (loops)', number_format($mycount), 'w3-teal'],
  ['Elapsed time (s)', round($time, 2), 'w3-black'],
]);
$render_card('Summary', ob_get_clean(), 'w3-pale-yellow');
