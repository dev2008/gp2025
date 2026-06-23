<?php
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';
$time_start = microtime(true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['uploadID'])) {

    $_cp_turnid = $_POST['uploadID'];

    // Get turn details
    $query = "SELECT turn_id, game, league, season, week, coach 
              FROM g_turnsummary WHERE uploadID = :uploadID";
    $stmt = $conn->prepare($query);
    $stmt->execute(['uploadID' => $_cp_turnid]);
    $upload = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$upload) {
        echo '<div class="w3-panel w3-red">No record found for the specified uploadID.</div>';
        die();
    }

    $game    = $upload['game'];
    $league  = $upload['league'];
    $season  = $upload['season'];
    $week    = $upload['week'];
    $coach   = $upload['coach'];

    // Look up franchise from coach and league
    $query_franchise = "SELECT f_id, f_team FROM bb_franchises 
                        WHERE f_coach = :coach AND f_league = :league 
                        ORDER BY f_season DESC, f_week DESC LIMIT 1";
    $stmt_f = $conn->prepare($query_franchise);
    $stmt_f->execute(['coach' => $coach, 'league' => $league]);
    $franchise_row = $stmt_f->fetch(PDO::FETCH_ASSOC);

    if (!$franchise_row) {
        echo '<div class="w3-panel w3-red">Could not find franchise for coach: ' 
             . htmlspecialchars($coach) . ' in league: ' . htmlspecialchars($league) . '</div>';
        die();
    }

    $_cp_franchise = $franchise_row['f_id'];
    $_cp_team      = $franchise_row['f_team'];

    echo "<h2>Processing: $_cp_team (franchise ID: $_cp_franchise) - "
         . "$league Season $season Week $week</h2>";

    // Parse roster from g_turnsfull
    $_cp_sql9 = "SELECT `tf_seq`, `tf_line` FROM `g_turnsfull` 
                 WHERE `up_id` = $_cp_turnid ORDER BY `tf_seq` ASC LIMIT 250";
    $_cp_mytext = nz_pdo_array($_cp_sql9, $conn);

    foreach ($_cp_mytext as $row) {
        $_cp_rowid   = $row['tf_seq'];
        $_cp_rowtext = $row['tf_line'];
        if ('Turn Credits' == substr($_cp_rowtext, 0, 12)) {
            $_cp_rowno  = $_cp_rowid + 2;
            $_cp_seqno  = $_cp_rowid;
        }
        if ('Key to' == substr($_cp_rowtext, 0, 6)) {
            $_cp_rownomax = $_cp_rowid;
        }
    }

    if (!isset($_cp_rowno) || !isset($_cp_rownomax)) {
        echo '<div class="w3-panel w3-red">Could not find roster boundaries in turn file.</div>';
        die();
    }

    echo "<p>Found roster start: row $_cp_rowno, end: row $_cp_rownomax</p>";

    $_cp_sql8 = "SELECT `tf_seq`, `tf_line` FROM `g_turnsfull` 
                 WHERE `up_id` = $_cp_turnid 
                 AND `tf_seq` >= $_cp_rowno AND `tf_seq` <= $_cp_rownomax 
                 ORDER BY `tf_seq` ASC";
    $_cp_myrosterlines = nz_pdo_array($_cp_sql8, $conn);

    $_cp_pitcher = 0;
    $_cp_batter  = 0;
    $_cp_catcher = 0;
    $x = 0;

    foreach ($_cp_myrosterlines as $_cp_myrosterline) {
        $_cp_myrosterline['tf_line'] = str_replace('LP', '', $_cp_myrosterline['tf_line']);
        $_cp_myrosterline['tf_line'] = str_replace('*', '', $_cp_myrosterline['tf_line']);
        $_cp_rowtext = $_cp_myrosterline['tf_line'];
        $_cp_rtext[$x] = preg_split("/[\s,]+/", $_cp_rowtext);

        if (isset($_cp_rtext[$x][3])) {
            switch ($_cp_rtext[$x][3]) {
                case "Pit":
                    $_cp_pitcher++;
                    bb_mypitcher($_cp_rtext[$x], $_cp_franchise, $_cp_turnid, $conn);
                    break;
                case "Bat":
                    $_cp_batter++;
                    bb_mybatter($_cp_rtext[$x], $_cp_franchise, $_cp_turnid, $conn);
                    break;
                case "Cat":
                    $_cp_catcher++;
                    bb_mybatter($_cp_rtext[$x], $_cp_franchise, $_cp_turnid, $conn);
                    break;
            }
            $x++;
        }
    }

    $total = $_cp_pitcher + $_cp_catcher + $_cp_batter;
    if ($total > 28 && $total < 33) {
        echo "<p class='w3-text-green'>Success! Found $_cp_pitcher Pitchers, "
             . "$_cp_catcher Catchers and $_cp_batter Batters.</p>";
    } else {
        echo "<div class='w3-panel w3-red'>Warning: unexpected player count ($total). "
             . "Found $_cp_pitcher Pitchers, $_cp_catcher Catchers, $_cp_batter Batters.</div>";
    }

} else {
    // Show form - only turns not yet in bb_myteam
    try {
        $query = "SELECT a.upload_id, a.filename, a.league, a.season, a.week 
                  FROM g_uploads AS a
                  WHERE a.upload_id NOT IN (SELECT DISTINCT m_turnid FROM bb_myteam)";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $uploads = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($uploads) {
            echo "<form method='POST' action=''>";
            echo "<label for='uploadID'>Select Upload:</label>";
            echo "<select name='uploadID' id='uploadID'>";
            foreach ($uploads as $upload) {
                echo "<option value='{$upload['upload_id']}'>";
                echo "{$upload['league']} - Season: {$upload['season']}, Week: {$upload['week']}";
                echo "</option>";
            }
            echo "</select><br>";
            echo "<input type='submit' name='submit' value='Process Roster' class='w3-button w3-green w3-margin-top'>";
            echo "</form>";
        } else {
            echo '<div class="w3-panel w3-green">All uploads have already been processed into bb_myteam.</div>';
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
