<?php
//Version 11.4.2 
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require_once 'g_functions.php';

$_cp_myname=$_SESSION['logged_user_infos_ar']["username_user"];

//Create basic layout
$str="";
#$str="<div class='w3-container w3-theme-d5 w3-round-xxlarge'>";
#$str.="<div class=' w3-round-large w3-text-white'>";
#$str.="</div>";
$str.= "<div class='w3-panel w3-theme-d5 w3-text-white w3-round-xxlarge '>";
$str.="<h1>&nbsp;Welcome to the Gameplan PBM site</h1>";
$str.= "<div class='w3-panel w3-theme w3-round-large '>";


$_cp_dbcheck=substr($db_name,0,3);
if ('dev' == $_cp_dbcheck){
		$str.= "<h2>$_cp_myname you are logged on to ** DEV **</h2>";
	} elseif ('pre' == $_cp_dbcheck) {
		$str.= "<h2>$_cp_myname you are logged on to ** PRE-PROD **</h2>";
	} else {
		$str.= "<h2>Welcome to Gameplan Analysis</h2>";
	}
#$str.="</div>";
output($str);

#Main content
$str="<h1>Gameplan Football</h1>";
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


$str.="<p>We have <i>$_cp_collegegamescount</i> College games and <i>$_cp_progamescount</i> Pro games in our database, the latest updates are:-";

$str.="<table style='width:30%'  class='w3-table w3-striped w3-bordered'>";
$str.="<tr class='w3-theme-d5'><th>League</th><th>Season</th><th>Week</th></tr>";
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
			$str.="<tr class='w3-theme-l3'><td>$_cp_myleague</td><td>$_cp_myseason</td><td>$_cp_myweek</td></tr>";
		} else {
			$str.="<tr class='w3-theme-l4'><td>$_cp_myleague</td><td>$_cp_myseason</td><td>$_cp_myweek</td></tr>";
		}
		$i++;


        }
$str.="</table>";
$str.="<br />";
$str.="<hr />";


$str.="<h1>Gameplan Baseball</h1>";

$str.="<p>Please stay tuned for some exciting news regarding our baseball coverage.</p>";
output($str);


//End of page
$str="</div>";
output($str);

//Start of footer
require_once 'g_footer.php';
?>
