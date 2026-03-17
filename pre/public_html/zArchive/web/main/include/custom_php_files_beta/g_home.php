<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once('include/custom_php_files/g_functions.php');
$_cp_myname=$_SESSION['logged_user_infos_ar']["username_user"];
echo "<br />";
echo "<div class='w3-card-4'>";

echo "<header class='w3-container $mycolour1'>";
if ('pre-gp' == $db_name ){
		echo "<h1>Welcome to gameplan.org.uk</h1>";
		echo " <h1>$_cp_myname logged on to ** PRE **</h1>";
		echo "<br />";
	}
		elseif ('dev-gp' == $db_name ) {
		echo "<h1>Welcome to gameplan.org.uk";
		echo " <h1>$_cp_myname</em>  logged on to ** DEV **</h1>";
		echo "<br />";
	}	else {
		echo "<h1>Welcome to gameplan.org.uk</h1>";
	}
echo "</header>";

//Main section
echo "<div class='w3-container $mycolour2'>";
//Start of content
echo "<div class='w3-panel $mycolour3 w3-card-4 w3-round-xxlarge'>";

//NZ 20200820 Altered this to report on each League individually.

echo "<h1>Gameplan Football</h1>";
$_cp_sql1 = "SELECT COUNT(*) FROM `f_games` WHERE `week`<90 AND league LIKE 'NC%'";
$res1 = execute_db($_cp_sql1, $conn);
        while($row = fetch_row_db($res1)){
		$_cp_collegegamescount = number_format($row[0]/2,0);
		}
			
$_cp_sql1 = "SELECT COUNT(*) FROM `f_games` WHERE `week`<90 AND league LIKE 'NF%'";
$res1 = execute_db($_cp_sql1, $conn);
        while($row = fetch_row_db($res1)){
			$_cp_progamescount = number_format($row[0]/2,0);
        }


echo "<p>We have <i>$_cp_collegegamescount</i> College games and <i>$_cp_progamescount</i> Pro games in our database, the latest updates are:-";

echo "<table style='width:30%'  class='w3-table w3-striped w3-bordered'>";
echo "<tr class='$mycolour7'><th>League</th><th>Season</th><th>Week</th></tr>";
$_cp_sql3 = "SELECT DISTINCT(`league`) FROM `f_games` ORDER BY `league` ASC ";
$res3 = execute_db($_cp_sql3, $conn);
$i=0;
        while($row = fetch_row_db($res3)){
        	$_cp_myleague=$row[0];
			$_cp_sql33 = " SELECT MAX(`season`) FROM `f_games` WHERE league = '$_cp_myleague'";
			$res33 = execute_db($_cp_sql33, $conn);
				while($row33 = fetch_row_db($res33)){
				$_cp_myseason=$row33[0];	
				$_cp_sql333 = " SELECT MAX(`week`) FROM `f_games` WHERE league = '$_cp_myleague' AND season ='$_cp_myseason'";
				$res333 = execute_db($_cp_sql333, $conn);
			while($row = fetch_row_db($res333)){
				$_cp_myweek=$row[0];
			}	
		}
        

		if (($i % 2) == 1) {
			echo "<tr class='$mycolour5'><td>$_cp_myleague</td><td>$_cp_myseason</td><td>$_cp_myweek</td></tr>";
		} else {
			echo "<tr class='$mycolour6'><td>$_cp_myleague</td><td>$_cp_myseason</td><td>$_cp_myweek</td></tr>";
		}
		$i++;


        }
echo "</table>";
echo "<br />";


echo "<h1>Gameplan Baseball</h1>";

echo "<p>We are currently developing the Baseball system to expand our coverage.</p>";

//end of content
echo "<br />";
echo "</div>";
#if ($current_id_group==1) {
#echo "<pre>";
#print_r($_SESSION);
#echo "</pre>";
#}

require_once 'g_footer.php';
?>
