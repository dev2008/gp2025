<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';
$time_start = microtime(true);

/*
  [_cp_turnid] => 1001
    [_cp_game] => Baseball
    [_cp_league] => MLB7
    [_cp_season] => 31
    [_cp_week] => 21
    [_cp_coach] => Alan Milnes

$_cp_team="Chicago White Sox";
$_cp_franchise=10704;
$_cp_turnid=1039;
$targetUploadID = 1039;
*/

// Handle form submission to store selected uploadID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['uploadID'])) {
     //Turn has been chosen
     $_cp_turnid= $_POST['uploadID'];
        
// Assuming $_cp_turnid contains the value for uploadID, like '1039'
$query = "SELECT turn_id, game, league, season, week, coach FROM g_turnsummary WHERE uploadID = :uploadID";
$stmt = $conn->prepare($query);
$stmt->execute(['uploadID' => $_cp_turnid]);

// Use fetch() instead of fetchAll() to retrieve a single row
$upload = $stmt->fetch(PDO::FETCH_ASSOC);

if ($upload) {
    // Access the fields from the fetched row
    $game = $upload['game'];
    $league = $upload['league'];
    $season = $upload['season'];
    $week = $upload['week'];
    $coach = $upload['coach'];
} else {
    echo "No record found for the specified uploadID.";
}

// Ensure that $_cp_team and $_cp_franchise are defined
// Example definitions (replace with actual values if available)
$_cp_team = $_cp_team ?? 'default_team_value';
$_cp_franchise = $_cp_franchise ?? 'default_franchise_value';
//TODO Update turns summary with this information


				//Text for middle box
				$str="<h2>My team is $_cp_team ($_cp_franchise) </h3>";
				output($str); 
}  else {
			//Turn has NOT been chosen so show form
					try {
						// Step 1: Retrieve upload IDs that are not present in bb_myteam
						$query = "SELECT a.upload_id, a.filename, a.league, a.season, a.week 
																FROM a_uploads AS a
																WHERE a.upload_id NOT IN (SELECT DISTINCT m_turnid FROM bb_myteam);
						";
						
						$stmt = $conn->prepare($query);
						$stmt->execute();
						
						$uploads = $stmt->fetchAll(PDO::FETCH_ASSOC);

						// Step 2: Display the results in a dropdown list within a form
						if ($uploads) {
										echo "<form method='POST' action=''>";
										echo "<label for='uploadID'>Select Upload:</label>";
										echo "<select name='uploadID' id='uploadID'>";
										
										foreach ($uploads as $upload) {
														echo "<option value='{$upload['upload_id']}'>";
														echo "{$upload['league']} - Season: {$upload['season']}, Week: {$upload['week']}";
														echo "</option>";
										}
										
										echo "</select>";
										echo "<br>";
										echo "<input type='submit' name='submit' value='Submit'>";
										echo "</form>";
						} else {
										echo "No uploads found that are not already in bb_myteam.";
						}


		} catch (PDOException $e) {
						echo "Error: " . $e->getMessage();
		}
}



/*





$_cp_sql9="SELECT `tf_seq`, `tf_line` FROM `g_turnsfull` WHERE `up_id`=$_cp_turnid ORDER BY `tf_seq` ASC LIMIT 250 ";
$_cp_mytext=nz_pdo_array($_cp_sql9,$conn);	

foreach ($_cp_mytext as $row) {
		$_cp_rowid=$row['tf_seq'];
		$_cp_rowtext=$row['tf_line'];
		#echo "<p>$_cp_rowid - $_cp_rowtext</p>";
		if ('Turn Credits'==substr($_cp_rowtext,0,12)) {
			$_cp_rowno=$_cp_rowid+2;
			$_cp_seqno=$_cp_rowid;
		}	
		if ('Key to'==substr($_cp_rowtext,0,6)) {
			$_cp_rownomax=$_cp_rowid;
		}
}

echo "<h2>Found start of roster on row $_cp_rowno with id $_cp_turnid, end of roster is line $_cp_rownomax</h3>";

$_cp_sql8="SELECT `tf_seq`, `tf_line` FROM `g_turnsfull` WHERE `up_id`=$_cp_turnid AND `tf_seq`>=$_cp_rowno AND `tf_seq`<=$_cp_rownomax ORDER BY `tf_seq` ASC ";
$_cp_myrosterlines=nz_pdo_array($_cp_sql8,$conn);	

$_cp_pitcher=0;
$_cp_batter=0;
$_cp_catcher=0;
$x=0;
echo "<p>";
foreach ($_cp_myrosterlines as $_cp_myrosterline) {
	#nz_debug($_cp_myrosterline,'');
	$_cp_myrosterline = str_replace('LP', '', $_cp_myrosterline);
	$_cp_myrosterline = str_replace('*', '', $_cp_myrosterline);  // Remove the asterisk
	$_cp_rowtext=$_cp_myrosterline['tf_line'];
	$_cp_rtext[$x] = preg_split("/[\s,]+/", $_cp_rowtext);
	#nz_debug($_cp_rtext[$x],'');
	#TODO This doesn't work for people with multiple names
	if (isset($_cp_rtext[$x][3])) {
	switch ($_cp_rtext[$x][3]) {
		case "Pit":
		#echo "This is a Pitcher!";
		$_cp_pitcher++;
		bb_mypitcher($_cp_rtext[$x],$_cp_franchise,$_cp_turnid,$conn);	
		break;
	  case "Bat":
		#nz_debug($_cp_rtext[$x],'');
		$_cp_batter++;
		bb_mybatter($_cp_rtext[$x],$_cp_franchise,$_cp_turnid,$conn);	
		break;	  
	  case "Cat":
		#echo "This is a Catcher!";
		$_cp_catcher++;
		bb_mybatter($_cp_rtext[$x],$_cp_franchise,$_cp_turnid,$conn);			
		break;
	  default:
		;
	}
	$x++;
	}
}
echo "Finished loading my team!</p>";

// Query to find the previous turn
$query = "
    SELECT turn_id, game, league, season, week, uploadID 
    FROM g_turnsummary
    WHERE league = :league 
      AND season = :season
      AND (
          (week < :week AND coach = :coach) 
          OR (week < :week)
      )
    ORDER BY week DESC
    LIMIT 1
";

$stmt = $pdo->prepare($query);
$stmt->execute([
    'league' => $pleague,
    'season' => $pseason,
    'week' => $pweek,
    'coach' => $pcoach
]);

$previousTurn = $stmt->fetch(PDO::FETCH_ASSOC);

if ($previousTurn) {
    // Process $previousTurn as needed
} else {
    // Handle case where no previous turn is found
}


//Copy role and notes


echo "Finished copying player info!</p>";


if ((($_cp_pitcher+$_cp_catcher+$_cp_batter)>28) AND (($_cp_pitcher+$_cp_catcher+$_cp_batter)<33)) {
	echo "<p>Found $_cp_pitcher Pitchers, $_cp_catcher Catchers and $_cp_batter Batters!</p>";
} else {
	echo "<h2>Sorry there has been an issue!!</h3>";
}
*/ 
?>
