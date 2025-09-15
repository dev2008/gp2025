<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'g_functions.php';
require_once 'mydatabase.php';

$time_start = microtime(true);
$str="<br /><div class='nz-card'>";
$str.="<div class='w3-container $mycolour6'>";
$str.="<div class='w3-pale-green'>";
$str.="<h1>Update All Game Results</h1>";
$str.="</div>";
$str.="</header>";
output($str);
$str= "<div class='w3-panel $mycolour4 nz-card w3-round-xxlarge'>";
output($str);
$str="<h3>Started update process</h3>\n";
output($str);
//Fix Tampa Bay
$str="<h3>Fixing Tampa Bay bug</h3>";
output($str);
$_cp_sql = "UPDATE `f_games` SET `opp_team` = 'Tampa Bay Buccs' WHERE `opp_team` LIKE 'Tampa Bay Buccaneers'";
$result0 = $conn->prepare($_cp_sql); 
$result0->execute(); 
$str="<h3>Setting game types.</h3>";
output($str);

//ensure correct gametypes
$_cp_sql2 = "	UPDATE `f_games` 
				SET `gametype`=1 
				WHERE `league` LIKE 'NCAA%' AND `week`>0 AND `week`<12";

$res2 = execute_db($_cp_sql2, $conn);
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 

$_cp_sql2 = "	UPDATE `f_games` 
				SET `gametype`=24
				WHERE `league` LIKE 'NFL%' AND `week`>0 AND `week`<17";

$res2 = execute_db($_cp_sql2, $conn);
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 

$_cp_sql2 = "	UPDATE `f_games` 
				SET `gametype`=30
				WHERE `league` LIKE 'NFL%' AND `week`=17 AND gametype =20";

$res2 = execute_db($_cp_sql2, $conn);
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 

$_cp_sql2 = "	UPDATE `f_games` 
				SET `gametype`=8 
				WHERE `gametype`=3 AND `league` LIKE 'NCAA%' AND `week`=13";

$res2 = execute_db($_cp_sql2, $conn);
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 

//PDO Example with row count
$_cp_sql2 = "	UPDATE `f_games` 
				SET `gametype`=9 
				WHERE `gametype`=4 AND `league` LIKE 'NCAA%' AND `week`=13";

$res2 = execute_db($_cp_sql2, $conn);
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 
#$str="<h3>Set game type to Rose Bowl</h3>";
#output($str);

//PDO Example with row count
$_cp_sql2 = "	UPDATE `f_games` 
				SET `gametype`=10 
				WHERE `gametype`=6 AND `league` LIKE 'NCAA%' AND `week`=13";

$res2 = execute_db($_cp_sql2, $conn);
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 
#$str="<h3>Set game type to Cotton Bowl</h3>";
#output($str);

//PDO Example with row count
$_cp_sql2 = "	UPDATE `f_games` 
				SET `gametype`=12
				WHERE `gametype`=7 AND `league` LIKE 'NCAA%' AND `week`=13";

$res2 = execute_db($_cp_sql2, $conn);
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 
#$str="<h3>Set game type to Hawaii Bowl</h3>";
#output($str);


//PDO Example with row count
$_cp_sql2 = "	UPDATE `f_games` 
				SET `gametype`=14 
				WHERE `gametype`=2 AND `league` LIKE 'NCAA%' AND `week`=12";

$res2 = execute_db($_cp_sql2, $conn);
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 
#$str="<h3>Set game type to NC Playoffs</h3>";
#output($str);


//PDO Example with row count
$_cp_sql2 = "	UPDATE `f_games` 
				SET `gametype`=15 
				WHERE `gametype`=6 AND `league` LIKE 'NCAA%' AND `week`=12";

$res2 = execute_db($_cp_sql2, $conn);
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 
#$str="<h3>Set game type to Cotton Bowl playoffs</h3>";
#output($str);

//PDO Example with row count
$_cp_sql2 = "	UPDATE `f_games` 
				SET `gametype`=16 
				WHERE `gametype`=7 AND `league` LIKE 'NCAA%' AND `week`=12";

$res2 = execute_db($_cp_sql2, $conn);
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 

$str="<h3>All game types set to correct values.</h3>";
output($str);

//Temporary fix for completed seasons
$_cp_sql = "SELECT Distinct `id_game` FROM `f_games` WHERE `week`<90 AND `season` > 2020 ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$number_of_rows = number_format($result->rowCount() ); 
#$str="<h3>Temporary fix for completed seasons</h3>";
$str="<h3>Assigning results to $number_of_rows records</h3>";
output($str);
$mycount=0;	

//Loop through results

	while($row = fetch_row_db($result)){
		$_cp_myid = $row[0];
		$_cp_sql = "UPDATE `f_games` SET `win`=1, `lose`=0, `tie`=0  WHERE `id_game` = '$_cp_myid' AND `score` > `opp_score` AND `week` < 90";
		$result1 = $conn->prepare($_cp_sql); 
		$result1->execute(); 
		$_cp_sql = "UPDATE `f_games` SET `win`=0, `lose`=1, `tie`=0  WHERE `id_game` = '$_cp_myid' AND `score` < `opp_score` AND `week` < 90";
		$result2 = $conn->prepare($_cp_sql); 
		$result2->execute(); 
		$_cp_sql = "UPDATE `f_games` SET `win`=0, `lose`=0, `tie`=1  WHERE `id_game` = '$_cp_myid' AND `score` = `opp_score` AND `week` < 90";
		$result3 = $conn->prepare($_cp_sql); 
		$result3->execute(); 
			$mycount++;	
		if ($mycount % 2500 == 0) {
				$str='Processed ';
				$str.=number_format($mycount);
				$str.=' records / ';
				output($str);
			}
		
	}        
$str='Processed ';
$str.=number_format($mycount);
$str.=' records.<br>';
output($str);
				
//PDO Example with row count
$_cp_sql = "SELECT SUM(`win`),SUM(`lose`),SUM(`tie`) FROM `f_games` WHERE `week`<90";
$result4 = $conn->prepare($_cp_sql); 
$result4->execute(); 

//Loop through results
while($row = fetch_row_db($result4)){
	$wins=number_format($row[0]);
	$losses=number_format($row[1]);
	$ties=number_format($row[2]);
	$str= "<h3>Found: wins -  $wins / losses - $losses / ties - $ties</h3>";
	output($str);
   }

$str= "<h3>Cross checking $wins records.</h3>";
output($str);

//Loop through all games and extract details
$_cp_sql = "SELECT `id_game`,`league`, `season`, `week`, `team`, `franchise`, `coach` 
			FROM `f_games` 
			WHERE `week`<90 AND `win`=1";
$result5 = $conn->prepare($_cp_sql); 
$result5->execute(); 
$number_of_rows5 = number_format($result5->rowCount()) ; 
$i=0;
$mycount2=0;	
//Loop through results
while($row = fetch_row_db($result5)){
	//Check for corresponding loss
	$_cp_myid = $row[0];
	$_cp_myleague = $row[1];
	$_cp_myseason = $row[2];
	$_cp_myweek = $row[3];
	$_cp_myteam = $row[4];
	//NZ 20200618 This was <90 but picking up old NFLAR post season games 
	//WHERE `week`<17 AND `lose`=1 AND `league`='$_cp_myleague' AND `season`='$_cp_myseason' AND `week`=$_cp_myweek AND `opp_team`='$_cp_myteam'";
try {
    $_cp_sql = "SELECT `id_game`
                FROM `f_games` 
                WHERE `lose`=1 
                  AND `league`=:league 
                  AND `season`=:season 
                  AND `week`=:week 
                  AND `opp_team`=:opp_team";
    
    // Prepare the SQL query
    $result6 = $conn->prepare($_cp_sql); 
   
    // Bind parameters
    $result6->bindParam(':league', $_cp_myleague);
    $result6->bindParam(':season', $_cp_myseason);
    $result6->bindParam(':week', $_cp_myweek);
    $result6->bindParam(':opp_team', $_cp_myteam);
    
    // Execute the query
    $result6->execute(); 
    $number_of_rows6 = $result6->rowCount();
   
    // Output no-match message and SQL query if no match is found
    if (1 <> $number_of_rows6 AND $_cp_myweek < 17) {
        // Manually substitute the parameters into the query for output
        $sql_with_params = str_replace(
            [':league', ':season', ':week', ':opp_team'],
            [$conn->quote($_cp_myleague), $conn->quote($_cp_myseason), (int)$_cp_myweek, $conn->quote($_cp_myteam)],
            $_cp_sql
        );
        
        // Output the SQL query and no match message
        $str = "<h3 class='w3-red'>No match found for <em>$_cp_myid ($_cp_myleague $_cp_myseason $_cp_myweek $_cp_myteam)</em></h3>";
        $str .= "<p>SQL Query: <code>" . htmlspecialchars($sql_with_params) . "</code></p>";
        output($str);
    }
} catch (PDOException $e) {
    // Log or handle the error
    echo "Error: " . $e->getMessage();
}



		$mycount2++;	
	if ($mycount2 % 2500 == 0) {
		$str='Checked ';
		$str.=number_format($mycount2);
		$str.=' records / ';
		output($str);
	}
   }
$str='Checked ';
$str.=number_format($mycount2);
$str.=' records.<br><hr>';
output($str);

echo "<div class='w3-red'>";
//check Wild Card records
//PDO Example with row count
$_cp_sql = "SELECT DISTINCT `league`, `season`
			FROM  `f_games` 
			WHERE `league` LIKE 'NFL%'  
			AND `season` NOT IN (SELECT MAX(`season`) FROM  `f_games`  WHERE `league` LIKE 'NFL%' ) 
			AND `season` NOT IN (SELECT `season` FROM  `f_games`  WHERE `league` LIKE 'NFL%' AND `week` = 17 AND `gametype` = '35');";
$result4 = $conn->prepare($_cp_sql); 
$result4->execute(); 
//Loop through results
while($row = fetch_row_db($result4)){
	$league=$row[0];
	$season=$row[1];
	$str= "<h2>No Wild Card games found for $league s$season w17</h2>";
	output($str);
   }

//Check Divisional records
//PDO Example with row count
$_cp_sql = "SELECT DISTINCT `league`, `season`
			FROM  `f_games` 
			WHERE `league` LIKE 'NFL%'  
			AND `season` NOT IN (SELECT MAX(`season`) FROM  `f_games`  WHERE `league` LIKE 'NFL%' ) 
			AND `season` NOT IN (SELECT `season` FROM  `f_games`  WHERE `league` LIKE 'NFL%' AND `week` = 18 AND `gametype` = '35');";
$result4 = $conn->prepare($_cp_sql); 
$result4->execute(); 
//Loop through results
while($row = fetch_row_db($result4)){
	$league=$row[0];
	$season=$row[1];
	$str= "<h2>No Divisional Round games found for $league s$season w18</h2>";
	output($str);
   }

//Check Championship records
//PDO Example with row count
$_cp_sql = "SELECT DISTINCT `league`, `season`
			FROM  `f_games` 
			WHERE `league` LIKE 'NFL%'  
			AND `season` NOT IN (SELECT MAX(`season`) FROM  `f_games`  WHERE `league` LIKE 'NFL%' ) 
			AND `season` NOT IN (SELECT `season` FROM  `f_games`  WHERE `league` LIKE 'NFL%' AND `week` = 19 AND `gametype` =35);";
$result4 = $conn->prepare($_cp_sql); 
$result4->execute(); 
//Loop through results
while($row = fetch_row_db($result4)){
	$league=$row[0];
	$season=$row[1];
	$str= "<h2>No Championship Games found for $league s$season w19</h2>";
	output($str);
   }

//Check Superbowl records
//PDO Example with row count
$_cp_sql = "SELECT DISTINCT `league`, `season`
			FROM  `f_games` 
			WHERE `league` LIKE 'NFL%'  
			AND `season` NOT IN (SELECT MAX(`season`) FROM  `f_games`  WHERE `league` LIKE 'NFL%' ) 
			AND `season` NOT IN (SELECT `season` FROM  `f_games`  WHERE `league` LIKE 'NFL%' AND `week` = 20 AND `gametype` = '36');";
$result4 = $conn->prepare($_cp_sql); 
$result4->execute(); 
//Loop through results
while($row = fetch_row_db($result4)){
	$league=$row[0];
	$season=$row[1];
	$str= "<h2>No Superbowl found for $league s$season w20</h2>";
	output($str);
   }

//Check College Playoff records
//PDO Example with row count
$_cp_sql = "SELECT DISTINCT `league`, `season`
			FROM  `f_games` 
			WHERE `league` LIKE 'NCAA%'  
			AND `season` NOT IN (SELECT MAX(`season`) FROM  `f_games`  WHERE `league` LIKE 'NCAA%' ) 
			AND `season` NOT IN (SELECT `season` FROM  `f_games`  WHERE `league` LIKE 'NCAA%' AND `week` = 12 AND (`gametype` > 13 AND `gametype` < 17));";
$result4 = $conn->prepare($_cp_sql); 
$result4->execute(); 
//Loop through results
while($row = fetch_row_db($result4)){
	$league=$row[0];
	$season=$row[1];
	$str= "<h2>No Playoff games found for $league s$season w12</h2>";
	output($str);
   }

//Check College Bowl records
//PDO Example with row count
$_cp_sql = "SELECT DISTINCT `league`, `season`
			FROM  `f_games` 
			WHERE `league` LIKE 'NCAA%'  
			AND `season` NOT IN (SELECT MAX(`season`) FROM  `f_games`  WHERE `league` LIKE 'NCAA%' ) 
			AND `season` NOT IN (SELECT `season` FROM  `f_games`  WHERE `league` LIKE 'NCAA%' AND `week` = 13 AND (`gametype` > 7 AND `gametype` < 14));";
$result4 = $conn->prepare($_cp_sql); 
$result4->execute(); 
//Loop through results
while($row = fetch_row_db($result4)){
	$league=$row[0];
	$season=$row[1];
	$str= "<h2>No Bowl games found for $league s$season w13</h2>";
	output($str);
   }

echo "</div>";


$str="</div>";
output($str);
      
require_once 'g_footer.php';

?>
