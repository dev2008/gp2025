<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require_once 'g_functions.php';
require_once 'mydatabase.php';

$str="<br /><div class='w3-card-4'>";
$str.="<header class='w3-container w3-blue-gray'>";
$str.="<h1>Headline</h1>";
$str.="</header>";
output($str);

$str="<div class='w3-container w3-pale-blue'>";
output($str);
//Text for middle box
$str="<h2>Gameplan Baseball</h3>";
output($str);

$_cp_sql1 = "SELECT COUNT(*) FROM `bb_turns` ";
$res1 = execute_db($_cp_sql1, $conn);
        while($row = fetch_row_db($res1)){
			$_cp_mygamescount = number_format($row[0]/2,0);
        }
$_cp_mygamescount=number_format($_cp_mygamescount*108,0);

echo "<p>We have <i>$_cp_mygamescount</i> games in our database waiting to be processed, the latest turns we have are: </p>";
echo "<table style='width:30%'  class='w3-table w3-striped w3-bordered'>";
echo "<tr class='$mycolour7'><th>League</th><th>Season</th><th>Week</th></tr>";
$i=0;
$_cp_sql3 = "SELECT DISTINCT(`league`) FROM `bb_turns` ORDER BY LENGTH(`league`),`league` ASC ";
$res3 = execute_db($_cp_sql3, $conn);
        while($row = fetch_row_db($res3)){
        	$_cp_myleague=$row[0];
			$_cp_sql33 = " SELECT MAX(`season`) FROM `bb_turns` WHERE league = '$_cp_myleague'";
			$res33 = execute_db($_cp_sql33, $conn);
				while($row33 = fetch_row_db($res33)){
				$_cp_myseason=$row33[0];	
				$_cp_sql333 = " SELECT MAX(`week`) FROM `bb_turns` WHERE league = '$_cp_myleague' AND season ='$_cp_myseason'";
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
//end of content
echo "<br />";
echo "</div>";



require_once 'g_footer.php';
?>
