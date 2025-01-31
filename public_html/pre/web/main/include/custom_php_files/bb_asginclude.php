<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');

echo "<h1 class='$mycolour19 w3-cursive' style='text-shadow:1px 1px 0 #444'>";
echo "<img  class='w3-right-align' src=\"images\\$_cp_image\"";
echo " alt=\"$_cp_gamename logo\" height=\"108\" width=\"192\">";
echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbspGameplan Baseball $_cp_gamename Summary Page";
echo "</h1>\n";
$_cp_leaguetype="MLB%";

echo "<div class='w3-panel w3-theme-d4 w3-card-4 w3-round-xxlarge'>";
echo "<br />\n";

if (isset($_POST['_cp_myleague']) && !empty($_POST['_cp_myleague'])) {
	$_cp_myleague =  $_POST['_cp_myleague'];
} elseif (isset($_GET['_cp_myleague']) && !empty($_GET['_cp_myleague'])) { 
	$_cp_myleague =  $_GET['_cp_myleague'];
} else {
	$_cp_myleague = 'MLB8';
}
#Show League Selector
	$_cp_sql = "SELECT DISTINCT `league` FROM `bb_asgames` ORDER BY `league` ASC";        
	$res = execute_db($_cp_sql, $conn);
	echo "<form action='index.php?function=show_static_page&id_static_page=8' method='post'>\n";	
	echo "<select name='_cp_myleague'  onchange='this.form.submit()'>\n";
        while($row = fetch_row_db($res)){
			echo "<option value='$row[0]'";
			$x = ($_cp_myleague == $row[0]) ? " selected ": "";
			echo "$x>$row[0]</option>\n";
			
        } 
	echo "</select>\n";	
	echo "</form>\n";	
	echo "</script>\n";
	echo "<br />\n";


//start of content
echo "<br />\n";

//Title
echo "<div class='w3-panel w3-blue'>";
echo "<h1 class='w3-text-orange' style='text-shadow:1px 1px 0 #444'>";
echo "<b>$_cp_gamename History</b></h1>";
echo "</div>";

echo "<table class='w3-table w3-striped w3-bordered' style='width:300px'>\n";

//$_cp_gamename wins
$_cp_sql = "select
COUNT(if(winner='AL',1,NULL)) as AL,
COUNT(if(winner='NL',1,NULL)) as NL
from  `bb_asgames`";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$myrow2 = "";
$mymultiples=0;
$mysingles=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$_cp_alwins=$row[0];
			$_cp_nlwins=$row[1];
        }          
echo "<tr class='$mycolour6'>";
echo "<th>American League Wins: </th>";
echo "<td>";
echo "$_cp_alwins</td>";
echo "</tr>";
echo "<tr class='$mycolour5'>";
echo "<th>National League Wins: </th>";
echo "<td>";
echo "$_cp_nlwins</td>";
echo "</tr>";
echo "</table>";
echo "<br />";


?>
