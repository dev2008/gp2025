<?php
//Version 11.4.2 
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

$_cp_myname=$_SESSION['logged_user_infos_ar']["username_user"];

echo "<div class='w3-card-4'>";

#Header
echo "<div class='w3-container $mycolour6'>";

$_cp_dbcheck=substr($db_name,-3);
if ('DEV' == $_cp_dbcheck){
		echo "<h1>Welcome to Gameplan Analysis $_cp_myname</h1>";
		echo "<h2>You are logged on to ** DEV **</h2>";
		echo "<br>";
	} elseif ('PRE' == $_cp_dbcheck) {
		echo "<h1>Welcome to Gameplan Analysis $_cp_myname</h1>";
		echo "<h2>You are logged on to ** PRE-PROD **</h2>";
		echo "<br>";
	} else {
				echo "<h1>Welcome to Gameplan Analysis</h1>";
		echo "<br>";
	}



$str="<div class='w3-container $mycolour4 w3-round-xxlarge'>\n";
$str.="<h3>Gameplan Football</h3>";
output($str);

$_cp_sql1 = "SELECT `a_type`,COUNT(*) FROM `n_playbyplay` WHERE 1 GROUP BY `a_type` ORDER BY `a_type` ASC";
$res1 = execute_db($_cp_sql1, $conn);
$i=1;
        while($row = fetch_row_db($res1)){
			$_cp_playcount[$i] = number_format($row[1],0);
			$i++;
        }
 
echo "<p>We have <i>$_cp_playcount[1]</i> College plays and <i>$_cp_playcount[2]</i> Pro plays in our database, the latest updates are:-";

echo "<table style='width:30%'  class='w3-table w3-striped w3-bordered'>";
echo "<tr class='w3-amber'><th>League</th><th>Season</th><th>Week</th></tr>";
$_cp_sql3 = "SELECT DISTINCT(`a_league`) FROM `n_playbyplay` ORDER BY LENGTH(`a_league`) ASC ,`a_league` ASC ";
$j=$i;
$i=0;
$res3 = execute_db($_cp_sql3, $conn);
        while($row = fetch_row_db($res3)){
        	$_cp_myleague=$row[0];
			$_cp_sql33 = " SELECT MAX(`a_season`) FROM `n_playbyplay` WHERE a_league = '$_cp_myleague'";
			$res33 = execute_db($_cp_sql33, $conn);
				while($row33 = fetch_row_db($res33)){
				$_cp_myseason=$row33[0];	
				$_cp_sql333 = " SELECT MAX(`a_week`) FROM `n_playbyplay` WHERE a_league = '$_cp_myleague' AND a_season ='$_cp_myseason'";
				$res333 = execute_db($_cp_sql333, $conn);
			while($row = fetch_row_db($res333)){
				$_cp_myweek=$row[0];
			}	
		}
				if (($i % 2) == 1) {
			echo "<tr class='w3-pale-red'><td>$_cp_myleague</td><td>$_cp_myseason</td><td>$_cp_myweek</td></tr>";
		} else {
			echo "<tr class='w3-pale-yellow'><td>$_cp_myleague</td><td>$_cp_myseason</td><td>$_cp_myweek</td></tr>";
		}
        
		$i++;
        }
echo "</table>";
echo "<br />";


require_once 'g_footer.php';
?>

