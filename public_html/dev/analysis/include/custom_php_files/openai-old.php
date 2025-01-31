<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }

// Define the API key at the top for reuse
$api_key = "";  // Replace with your OpenAI API key
$debugmode = false;  // Set to true to enable debugging, false to disable it



// Step 1: Fetch data for the dropdowns from the `g_turnsummary` table
try {
    $sql = "SELECT `turn_id`, `game`, `league`, `season`, `week`, `uploadID` FROM `g_turnsummary`";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $turns = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}

// Step 2: Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $turn1_id = $_POST['turn1'];
    $turn2_id = $_POST['turn2'];

    // Conditionally print debug output
    if ($debugmode) {
        echo '<h3>Selected Turn 1 ID:</h3><pre>';
        print_r($turn1_id);
        echo '</pre>';

        echo '<h3>Selected Turn 2 ID:</h3><pre>';
        print_r($turn2_id);
        echo '</pre>';
    }

    // Step 3: Fetch the `uploadID` from g_turnsummary for the selected turns
    try {
        $sql = "SELECT `uploadID` FROM `g_turnsummary` WHERE `turn_id` = :turn_id";

        // Fetch uploadID for Turn 1
        $stmt1 = $conn->prepare($sql);
        $stmt1->execute(['turn_id' => $turn1_id]);
        $turn1_uploadID = $stmt1->fetchColumn();

        // Fetch uploadID for Turn 2
        $stmt2 = $conn->prepare($sql);
        $stmt2->execute(['turn_id' => $turn2_id]);
        $turn2_uploadID = $stmt2->fetchColumn();

        // Conditionally print debug output for uploadID
        if ($debugmode) {
            echo '<h3>Turn 1 UploadID:</h3><pre>';
            print_r($turn1_uploadID);
            echo '</pre>';

            echo '<h3>Turn 2 UploadID:</h3><pre>';
            print_r($turn2_uploadID);
            echo '</pre>';
        }

    } catch (PDOException $e) {
        die("Error fetching data from g_turnsummary: " . $e->getMessage());
    }

    // Step 4: Fetch data from bb_myteam based on the `uploadID`
    try {
        $sql = "SELECT `m_id`, `m_team`, `m_turnid`, `m_sh`, `m_cname`, `m_sname`, `m_type`, `m_hand`, `m_level`, 
                `m_best`, `m_exp`, `m_pot`, `m_trd`, `m_fat`, `m_frm`, `m_tot`, `m_inj`, `m_val`, 
                `m_s1`, `m_s2`, `m_s3`, `m_s4`, `m_sqd`, `m_pos` 
                FROM `bb_myteam` WHERE `m_turnid` = :uploadID";

        // Fetch data for Turn 1
        $stmt1 = $conn->prepare($sql);
        $stmt1->execute(['uploadID' => $turn1_uploadID]);
        $turn1_data = $stmt1->fetchAll(PDO::FETCH_ASSOC);

        // Conditionally print debug output for Turn 1 data
        if ($debugmode) {
            echo '<h3>Turn 1 Data</h3><pre>';
            print_r($turn1_data);
            echo '</pre>';
        }

        // Fetch data for Turn 2
        $stmt2 = $conn->prepare($sql);
        $stmt2->execute(['uploadID' => $turn2_uploadID]);
        $turn2_data = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // Conditionally print debug output for Turn 2 data
        if ($debugmode) {
            echo '<h3>Turn 2 Data</h3><pre>';
            print_r($turn2_data);
            echo '</pre>';
        }

    } catch (PDOException $e) {
        die("Error fetching data from bb_myteam: " . $e->getMessage());
    }

    // Step 5: Match players based on `m_sh`
    $matched_players = [];
    foreach ($turn1_data as $player1) {
        foreach ($turn2_data as $player2) {
            if ($player1['m_sh'] == $player2['m_sh']) {
                $matched_players[] = ['turn1' => $player1, 'turn2' => $player2];
            }
        }
    }

    // Step 6: Compare matched players and identify significant changes
    $comparison_results = [];
    foreach ($matched_players as $players) {
        $player1 = $players['turn1'];
        $player2 = $players['turn2'];

        $changes = [];

        // Compare attributes except `m_exp`
        foreach ($player1 as $key => $value) {
            if ($key == 'm_exp') {
                // Skip experience
                continue;
            }

            if ($key == 'm_pot') {
                // Only note changes in potential if difference > 1
                if (abs($player1[$key] - $player2[$key]) > 1) {
                    $changes[$key] = ['from' => $player1[$key], 'to' => $player2[$key]];
                }
                continue;
            }

            // Compare other attributes normally
            if ($player1[$key] != $player2[$key]) {
                $changes[$key] = ['from' => $player1[$key], 'to' => $player2[$key]];
            }
        }

        if (!empty($changes)) {
            $comparison_results[] = [
                'player' => $player1['m_sh'],  // Use player identifier
                'changes' => $changes
            ];
        }
    }

    // Step 7: Output the comparison results
    echo '<h3>Comparison Results:</h3>';
    echo '<pre>';
    print_r($comparison_results);
    echo '</pre>';
}

// Step 8: Output the form and results in PHP
echo '<h2>Select Two Turns to Compare</h2>';

echo '<form method="POST">';

echo '<label for="turn1">Select First Turn:</label>';
echo '<select name="turn1" id="turn1" required>';
echo '<option value="">-- Select Turn --</option>';
foreach ($turns as $turn) {
    $turn_id = htmlspecialchars($turn['turn_id']);
    $game = htmlspecialchars($turn['game']);
    $league = htmlspecialchars($turn['league']);
    $season = htmlspecialchars($turn['season']);
    $week = htmlspecialchars($turn['week']);
    echo "<option value=\"$turn_id\">Game: $game, League: $league, Season: $season, Week: $week</option>";
}
echo '</select>';

echo '<br><br>';

echo '<label for="turn2">Select Second Turn:</label>';
echo '<select name="turn2" id="turn2" required>';
echo '<option value="">-- Select Turn --</option>';
foreach ($turns as $turn) {
    $turn_id = htmlspecialchars($turn['turn_id']);
    $game = htmlspecialchars($turn['game']);
    $league = htmlspecialchars($turn['league']);
    $season = htmlspecialchars($turn['season']);
    $week = htmlspecialchars($turn['week']);
    echo "<option value=\"$turn_id\">Game: $game, League: $league, Season: $season, Week: $week</option>";
}
echo '</select>';

echo '<br><br>';

echo '<button type="submit">Compare</button>';

echo '</form>';
