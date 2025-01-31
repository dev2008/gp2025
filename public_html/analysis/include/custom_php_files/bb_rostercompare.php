<?php
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'g_functions.php';
require_once 'bb_functions.php';

// Skill ranking array
$skill_ranking = [
    'Po' => 1,  // Poor
    'Fa' => 2,  // Fair
    'Av' => 3,  // Average
    'Go' => 4,  // Good
    'Ex' => 5,  // Excellent
    'WC' => 6   // World Class
];

// Retrieve available turns
$query_turns = "
    SELECT DISTINCT g.`uploadID`, g.`turn_id`, g.`game`, g.`league`, g.`season`, g.`week`, b.`m_team`
    FROM `g_turnsummary` g
    JOIN `bb_myteam` b ON g.`uploadID` = b.`m_turnid`;
";

$stmt_turns = $conn->query($query_turns);
$turns = $stmt_turns->fetchAll(PDO::FETCH_ASSOC);

if (count($turns) < 2) {
    echo '<div class="w3-panel w3-red">Not enough turns available for comparison.</div>';
    die();
}

// Assuming user has selected two turns (using POST method for selection)
$turn1_id = isset($_POST['turn1']) ? $_POST['turn1'] : null;
$turn2_id = isset($_POST['turn2']) ? $_POST['turn2'] : null;

if (!$turn1_id || !$turn2_id) {
    echo '<form method="post" action="">
            <div class="w3-container">
                <h3>Select two turns for comparison:</h3>
                <label for="turn1">Turn 1:</label>
                <select name="turn1" class="w3-select" required>';
    foreach ($turns as $turn) {
        echo '<option value="' . $turn['uploadID'] . '">Game ' . $turn['game'] . ', League ' . $turn['league'] . ', Season ' . $turn['season'] . ', Week ' . $turn['week'] . '</option>';
    }
    echo    '</select><br>
                <label for="turn2">Turn 2:</label>
                <select name="turn2" class="w3-select" required>';
    foreach ($turns as $turn) {
        echo '<option value="' . $turn['uploadID'] . '">Game ' . $turn['game'] . ', League ' . $turn['league'] . ', Season ' . $turn['season'] . ', Week ' . $turn['week'] . '</option>';
    }
    echo    '</select><br><br>
                <input type="submit" value="Compare Turns" class="w3-button w3-green">
            </div>
          </form>';
    die();
}

// Find details of the selected turns
$turn1_details = null;
$turn2_details = null;
foreach ($turns as $turn) {
    if ($turn['uploadID'] == $turn1_id) {
        $turn1_details = $turn;
    }
    if ($turn['uploadID'] == $turn2_id) {
        $turn2_details = $turn;
    }
}

// Fetch data for both turns
$sql = "SELECT 
            `bb_myteam`.`m_id`, 
            `bb_franchises`.`f_team` AS `team_name`,  -- Selecting the correct team name
            `bb_myteam`.`m_turnid`, 
            `bb_myteam`.`m_sh`, 
            `bb_myteam`.`m_cname`, 
            `bb_myteam`.`m_sname`, 
            `bb_myteam`.`m_type`, 
            `bb_myteam`.`m_hand`, 
            `bb_myteam`.`m_level`, 
            `bb_myteam`.`m_best`, 
            `bb_myteam`.`m_exp`, 
            `bb_myteam`.`m_pot`, 
            `bb_myteam`.`m_trd`, 
            `bb_myteam`.`m_fat`, 
            `bb_myteam`.`m_frm`, 
            `bb_myteam`.`m_tot`, 
            `bb_myteam`.`m_inj`, 
            `bb_myteam`.`m_val`, 
            `bb_myteam`.`m_s1`, 
            `bb_myteam`.`m_s2`, 
            `bb_myteam`.`m_s3`, 
            `bb_myteam`.`m_s4`, 
            `bb_myteam`.`m_sqd`, 
            `bb_myteam`.`m_pos`,
            `bb_myteam`.`notes`
        FROM `bb_myteam`
        LEFT JOIN `bb_franchises` 
        ON `bb_myteam`.`m_team` = `bb_franchises`.`f_id`  -- Joining based on team ID
        WHERE `bb_myteam`.`m_turnid` = :turnid 
        ORDER BY `bb_myteam`.`m_sh` ASC";


// Fetch data for Turn 1
$stmt1 = $conn->prepare($sql);
$stmt1->execute([':turnid' => $turn1_id]);
$data_turn1 = [];
while ($row = $stmt1->fetch(PDO::FETCH_ASSOC)) {
    $data_turn1[$row['m_sh']] = $row; // Use m_sh (Shirt #) as the key
}

// Fetch data for Turn 2
$stmt2 = $conn->prepare($sql);
$stmt2->execute([':turnid' => $turn2_id]);
$data_turn2 = [];
while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    $data_turn2[$row['m_sh']] = $row; // Use m_sh (Shirt #) as the key
}

// Debug: Check if data was retrieved
if (empty($data_turn1) || empty($data_turn2)) {
    echo '<div class="w3-panel w3-red">No data found for one or both turns. Please check the database or turn selection.</div>';
    die();
}

// Debug: Check if same team
#$title="Turn 1 details";
#nz_debug($turn1_details,$title);
#print_r($turn1_details);
#print_r($turn2_details);
#print_r($data_turn1);
#print_r($data_turn2);
if ($turn1_details['m_team'] <>$turn2_details['m_team'] ) {
    echo '<div class="w3-panel w3-red">Different teams selected!!</div>';
    die();
}

// Output comparison heading with turn details
#echo '<div class="w3-container"><h3>Comparison of Selected Turns</h3>';
#echo '<p><em>Turn 1: League ' . $turn1_details['league'] . ', Season ' . $turn1_details['season'] . ', Week ' . $turn1_details['week'] . '</em></p>';
#echo '<p><em>Turn 2: League ' . $turn2_details['league'] . ', Season ' . $turn2_details['season'] . ', Week ' . $turn2_details['week'] . '</em></p>';

// Initialize separate counters for Pit and Bat/Cat
$level_up_count_pit = 0;
$level_down_count_pit = 0;
$skill_up_count_pit = 0;
$skill_down_count_pit = 0;
$double_potential_loss_count_pit = 0;

$level_up_count_batcat = 0;
$level_down_count_batcat = 0;
$skill_up_count_batcat = 0;
$skill_down_count_batcat = 0;
$double_potential_loss_count_batcat = 0;

// Collect the table rows in a variable to render after the summary
$table_rows = '';

// Compare and display data based on m_sh (Shirt #)
foreach ($data_turn2 as $shirt_num => $player2) {
    if (isset($data_turn1[$shirt_num])) {
        $player1 = $data_turn1[$shirt_num];

        // Determine if changes occurred
        $level_change = ($player1['m_level'] != $player2['m_level']);
        $pot_change = ($player1['m_pot'] - $player2['m_pot'] > 1); // Check for double potential loss

        // Skill changes
        $s1_change = ($player1['m_s1'] != $player2['m_s1']);
        $s2_change = ($player1['m_s2'] != $player2['m_s2']);
        $s3_change = ($player1['m_s3'] != $player2['m_s3']);
        $s4_change = ($player1['m_s4'] != $player2['m_s4']);

        // Determine if the player is Pit or Bat/Cat
        $is_pit = ($player1['m_type'] == 'Pit');

        // Update counters for level, skill, and potential changes based on type
        if ($level_change) {
            if ($player2['m_level'] > $player1['m_level']) {
                $is_pit ? $level_up_count_pit++ : $level_up_count_batcat++;
            } else {
                $is_pit ? $level_down_count_pit++ : $level_down_count_batcat++;
            }
        }

        if ($pot_change) {
            $is_pit ? $double_potential_loss_count_pit++ : $double_potential_loss_count_batcat++;
        }

        if ($s1_change) {
            if ($skill_ranking[$player2['m_s1']] > $skill_ranking[$player1['m_s1']]) {
                $is_pit ? $skill_up_count_pit++ : $skill_up_count_batcat++;
            } else {
                $is_pit ? $skill_down_count_pit++ : $skill_down_count_batcat++;
            }
        }
        if ($s2_change) {
            if ($skill_ranking[$player2['m_s2']] > $skill_ranking[$player1['m_s2']]) {
                $is_pit ? $skill_up_count_pit++ : $skill_up_count_batcat++;
            } else {
                $is_pit ? $skill_down_count_pit++ : $skill_down_count_batcat++;
            }
        }
        if ($s3_change) {
            if ($skill_ranking[$player2['m_s3']] > $skill_ranking[$player1['m_s3']]) {
                $is_pit ? $skill_up_count_pit++ : $skill_up_count_batcat++;
            } else {
                $is_pit ? $skill_down_count_pit++ : $skill_down_count_batcat++;
            }
        }
        if ($s4_change) {
            if ($skill_ranking[$player2['m_s4']] > $skill_ranking[$player1['m_s4']]) {
                $is_pit ? $skill_up_count_pit++ : $skill_up_count_batcat++;
            } else {
                $is_pit ? $skill_down_count_pit++ : $skill_down_count_batcat++;
            }
        }

// Collect the row HTML for rendering later
$table_rows .= '<tr>';
$table_rows .= '<td>' . htmlspecialchars($player2['team_name']) . '</td>';
$table_rows .= '<td>' . htmlspecialchars($player2['m_sh']) . '</td>';
$table_rows .= '<td>' . htmlspecialchars($player2['m_cname'] . ' ' . $player1['m_sname']) . '</td>';
$table_rows .= '<td>' . htmlspecialchars($player2['m_type']) . '</td>';
$table_rows .= '<td>' . htmlspecialchars($player2['m_pos']) . '</td>';
$table_rows .= '<td>' . htmlspecialchars($player2['m_exp']) . '</td>';

// Handle level change color: Green for increase, Red for decrease
if ($level_change) {
    if ($player2['m_level'] > $player1['m_level']) {
        // Level went up, add green class
        $table_rows .= '<td class="w3-green">' . htmlspecialchars($player1['m_level']) . ' → ' . htmlspecialchars($player2['m_level']) . '</td>';
    } else {
        // Level went down, add red class
        $table_rows .= '<td class="w3-red">' . htmlspecialchars($player1['m_level']) . ' → ' . htmlspecialchars($player2['m_level']) . '</td>';
    }
} else {
    // No level change, render as normal
    $table_rows .= '<td>' . htmlspecialchars($player1['m_level']) . '</td>';
}

// Handle potential changes
$table_rows .= '<td' . ($pot_change ? ' class="w3-cyan"' : '') . '>' . htmlspecialchars($player1['m_pot']) . ' → ' . htmlspecialchars($player2['m_pot']) . '</td>';

$table_rows .= '<td' . 
    ($player2['m_val'] > 99 ? ' class="w3-red"' : ($player2['m_val'] >= 70 && $player2['m_val'] <= 99 ? ' class="w3-amber"' : '')) . 
    '>' . htmlspecialchars($player1['m_val']) . 
    ' → ' . 
    htmlspecialchars($player2['m_val']) . 
    '</td>';


// Handle skill changes
$table_rows .= '<td' . ($s1_change ? skillColor($player1['m_s1'], $player2['m_s1'], $skill_ranking) : '') . '>' . formatSkill($player1['m_s1'], $player2['m_s1'], $s1_change) . '</td>';
$table_rows .= '<td' . ($s2_change ? skillColor($player1['m_s2'], $player2['m_s2'], $skill_ranking) : '') . '>' . formatSkill($player1['m_s2'], $player2['m_s2'], $s2_change) . '</td>';
$table_rows .= '<td' . ($s3_change ? skillColor($player1['m_s3'], $player2['m_s3'], $skill_ranking) : '') . '>' . formatSkill($player1['m_s3'], $player2['m_s3'], $s3_change) . '</td>';
$table_rows .= '<td' . ($s4_change ? skillColor($player1['m_s4'], $player2['m_s4'], $skill_ranking) : '') . '>' . formatSkill($player1['m_s4'], $player2['m_s4'], $s4_change) . '</td>';
$table_rows .= '<td class="w3-panel w3-pale-blue w3-leftbar w3-rightbar w3-border-blue">' . htmlspecialchars($player2['notes'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
$table_rows .= '</tr>';

    } else {
		//New player
		#$title="Turn 2 details";
		#nz_debug($player2,$title);
		$table_rows .= '<tr>';
		$table_rows .= '<td>' . htmlspecialchars($player2['team_name']) . '</td>';
		$table_rows .= '<td>' . htmlspecialchars($player2['m_sh']) . '</td>';
		$table_rows .= '<td>' . htmlspecialchars($player2['m_cname'] . ' ' . $player2['m_sname']) . '</td>';
		$table_rows .= '<td>' . htmlspecialchars($player2['m_type']) . '</td>';
		$table_rows .= '<td>' . htmlspecialchars($player2['m_pos']) . '</td>';
		$table_rows .= '<td>' . htmlspecialchars($player2['m_exp']) . '</td>';	
		$table_rows .= '<td>' . htmlspecialchars($player2['m_level']) . '</td>';	
		$table_rows .= '<td>' . htmlspecialchars($player2['m_pot']) . '</td>';		
		$table_rows .= '<td>' . htmlspecialchars($player2['m_val']) . '</td>';
		$table_rows .= '<td>' . htmlspecialchars($player2['m_s1']) . '</td>';
		$table_rows .= '<td>' . htmlspecialchars($player2['m_s2']) . '</td>';
		$table_rows .= '<td>' . htmlspecialchars($player2['m_s3']) . '</td>';
		$table_rows .= '<td>' . htmlspecialchars($player2['m_s4']) . '</td>';
		$table_rows .= '<td class="w3-panel w3-pale-blue w3-leftbar w3-rightbar w3-border-blue">' . htmlspecialchars($player2['notes'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
		$table_rows .= '</tr>';
	}
}


//Summary section
echo '<div class="w3-container w3-margin-top">';
echo '    <button id="toggleButton" class="w3-button w3-blue w3-margin-bottom">Toggle Summary</button>';

echo "<div id='summarySection' class='w3-card w3-padding w3-light-grey' style='display: block;'>"; // Initially visible
echo "<h3 class='w3-text-blue'>Summary of Changes for League ".$turn2_details['league']." Season ".$turn2_details['season']." Week ".$turn2_details['week']."</h3>";
echo '        <p><span class="w3-tag w3-green w3-round">Level changes up: Pit - ' . $level_up_count_pit . ', Bat/Cat - ' . $level_up_count_batcat . '</span></p>';
echo '        <p><span class="w3-tag w3-red w3-round">Level changes down: Pit - ' . $level_down_count_pit . ', Bat/Cat - ' . $level_down_count_batcat . '</span></p>';
echo '        <p><span class="w3-tag w3-green w3-round">Skill changes up: Pit - ' . $skill_up_count_pit . ', Bat/Cat - ' . $skill_up_count_batcat . '</span></p>';
echo '        <p><span class="w3-tag w3-red w3-round">Skill changes down: Pit - ' . $skill_down_count_pit . ', Bat/Cat - ' . $skill_down_count_batcat . '</span></p>';
echo '        <p><span class="w3-tag w3-yellow w3-round">Players with double potential loss: Pit - ' . $double_potential_loss_count_pit . ', Bat/Cat - ' . $double_potential_loss_count_batcat . '</span></p>';
echo '    </div>';
echo '</div>';
echo '<br>'; // Added a line break for better readability between the summary and the table

// Output the table after the summary
echo '<table id="rosterTable" class="display w3-table w3-bordered w3-striped">';
echo '<thead>
        <tr class="w3-blue">
            <th>Team</th>
            <th>Shirt #</th>
            <th>Name</th>
            <th>Type</th>
            <th>Position</th>
            <th>Exp</th>
            <th>Level</th>
            <th>Potential</th>
            <th>Value</th>
            <th>Skill 1</th>
            <th>Skill 2</th>
            <th>Skill 3</th>
            <th>Skill 4</th>
            <th>Notes</th>
        </tr>
      </thead>';
echo '<tbody>';
echo $table_rows; // Render the table rows collected in memory
echo '</tbody>';
echo '</table>';

// Function to determine the color class based on skill change
function skillColor($old_skill, $new_skill, $ranking) {
    if ($ranking[$new_skill] > $ranking[$old_skill]) {
        return ' class="w3-green"';
    } elseif ($ranking[$new_skill] < $ranking[$old_skill]) {
        return ' class="w3-red"';
    } else {
        return '';
    }
}

// Function to format skill display (show change if it occurred)
function formatSkill($old_skill, $new_skill, $changed) {
    if ($changed) {
        return $old_skill . ' → ' . $new_skill;
    } else {
        return $new_skill;
    }
}
?>

<!-- jQuery is already available -->
<!-- DataTables CSS and JS inclusion -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        // Check if DataTable is already initialized, and initialize if not
        if (!$.fn.DataTable.isDataTable('#rosterTable')) {
            $('#rosterTable').DataTable({
                "pageLength": 50
            });
        }

        // Function to toggle the visibility of the summary section using display property
        function toggleSummary() {
            console.log('Toggle button clicked');  // Log when the button is clicked
            
            var summary = document.getElementById("summarySection");
            
            // Toggle display between 'block' and 'none'
            if (summary.style.display === "none" || summary.style.display === "") {
                console.log('Showing the summary');
                summary.style.display = "block";  // Show the summary
            } else {
                console.log('Hiding the summary');
                summary.style.display = "none";   // Hide the summary
            }
        }

        // Attach the toggleSummary function to the button click event
        $('#toggleButton').on('click', toggleSummary);
    });
</script>





