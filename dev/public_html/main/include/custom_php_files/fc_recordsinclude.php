<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');

echo "<h1>";
echo "<img  class='w3-right-align' src=\"images\\$_cp_image\"";
echo " alt=\"$_cp_gamename logo\" height=\"200\" width=\"200\">";
echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbspGameplan College Football $_cp_gamename Records Page";
echo "</h1>\n";
$_cp_leaguetype="NCAA%";
echo "</header>\n";
//Div 2
echo "<div class='w3-container $mycolour2'>\n";
echo "<br />\n";

if (isset($_POST['_cp_myleague']) && !empty($_POST['_cp_myleague'])) {
	$_cp_myleague =  $_POST['_cp_myleague'];
} elseif (isset($_GET['_cp_myleague']) && !empty($_GET['_cp_myleague'])) { 
	$_cp_myleague =  $_GET['_cp_myleague'];
} else {
	$_cp_myleague = 'NCAA5';
}
#Show League Selector
	$_cp_sql = "SELECT DISTINCT `league` FROM `fc_franchises` ORDER BY `league` ASC";        
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
echo "<div class='w3-col' style='width:5%'><p></p></div>";
echo "<div class='w3-col w3-panel $mycolour3 w3-card-4 w3-round-xxlarge w3-centred'  style='width:90%;'>";
//Overall cyan box - Div 3
#echo "<div class='w3-panel $mycolour4 w3-round-xxlarge'>";
#echo "<p>Special Announcement</p>";
//end of Teal box  - Div 3
#echo "</div>";
echo "<br />\n";

//Title
echo "<div class='w3-panel w3-blue'>";
echo "<h1 class='w3-text-orange' style='text-shadow:1px 1px 0 #444'>";
echo "<b>$_cp_gamename History</b></h1>";
echo "</div>";

echo "<table class='w3-table w3-striped w3-bordered'>\n";
//$_cp_gamename wins
$_cp_sql = "SELECT b.team,SUM(a.`win`) AS `TotWins`
FROM `f_games` a 
	INNER JOIN fc_franchises b ON a.`franchise` = b.`franchise`
WHERE `week`=13 AND `gametype`=$_cp_gametype AND `win`=1 AND a.`league`='$_cp_myleague'  
GROUP BY b.team
ORDER BY TotWins DESC";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$myrow2 = "";
$mymultiples=0;
$mysingles=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			if ($row[1]>1){
				$myrow .= "$row[0] ($row[1]), ";
				$mymultiples++;	
			} elseif (1==$row[1]) {
				$myrow2 .= "$row[0], ";
				$mysingles++;	
				
			}
        }          
echo "<tr class='$mycolour6'>";
echo "<th>Schools with multiple $_cp_gamename wins ($mymultiples): </th>";
echo "<td>";
$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";
echo "<tr  class='$mycolour5'>";
echo "<th>Schools with one $_cp_gamename win ($mysingles): </th>";
echo "<td>";
$myrow2=substr($myrow2,0,-2);
echo "$myrow2</td>";
echo "</tr>";

//No wins
$_cp_sql = "SELECT franchise,team FROM fc_franchises WHERE `league`='$_cp_myleague'  AND franchise NOT IN (SELECT franchise FROM f_games WHERE `gametype`=$_cp_gametype AND win=1 AND league='$_cp_myleague') AND franchise  IN (SELECT franchise FROM f_games WHERE `gametype`=$_cp_gametype AND league='$_cp_myleague') ORDER BY `conference` ASC";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "$row[1], ";
			$mynone++;
        }          
echo "<tr class='$mycolour6'>";
echo "<th>Schools with no $_cp_gamename wins ($mynone):</th>";
echo "<td>";
$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";

//No appearances
$_cp_sql = "SELECT franchise,team FROM fc_franchises WHERE `league`='$_cp_myleague'  AND franchise NOT IN (SELECT franchise FROM f_games WHERE `gametype`=$_cp_gametype AND league='$_cp_myleague') ORDER BY `conference` ASC";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "$row[1], ";
			$mynone++;
        }          
echo "<tr class='$mycolour5'>";
echo "<th>Schools with no $_cp_gamename wins or appearances ($mynone):</th>";
echo "<td>";
$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";



//Conference

echo "<tr class='$mycolour6'>";
echo "<th>Record by Conference: </th>";
echo "<td>";

$_cp_sql = "SELECT b.conference,SUM(a.`win`) AS `TotWins`
FROM `f_games` a 
	INNER JOIN fc_franchises b ON a.`franchise` = b.`franchise`
WHERE `week`=13 AND `gametype`=$_cp_gametype AND a.`league`='$_cp_myleague'  
GROUP BY b.conference
ORDER BY TotWins DESC";
$myrow = "";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "$row[0] $row[1], ";
        }
        $myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";
echo "</table>";
echo "<br />";


?>
