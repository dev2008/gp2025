<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');

//Title
echo "<div class='w3-panel w3-blue'>";
echo "<h1 class='w3-text-orange' style='text-shadow:1px 1px 0 #444'>";
echo "<b>$_cp_gamename Records</b></h1>";
echo "</div>";

echo "<table class='w3-table w3-striped w3-bordered'>\n";

//Biggest Margin
$_cp_sql = "SELECT a.`season`, b.`team`, a.`score`,a.`opp_score`,c.`team`, (a.`score`-a.`opp_score`) AS `margin`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise` = b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise` = c.`franchise`
WHERE `gametype`=$_cp_gametype
ORDER BY `margin` DESC ,  a.`season` ASC
LIMIT 5";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "<span style=\"font-weight: 900\">$row[5] : $row[1]  $row[2]</span> - $row[3] $row[4] <em>($row[0]</em>)<br />";
			$mynone++;
        }          
echo "<tr class='$mycolour6'>";
echo "<th>Biggest margin of victory:</th>";
echo "<td>";
//$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";

//Smallest Margin
$_cp_sql = "SELECT a.`season`, b.`team`, a.`score`,a.`opp_score`,c.`team`, (a.`score`-a.`opp_score`) AS `margin`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise` = b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise` = c.`franchise`
WHERE `gametype`=$_cp_gametype and `win`=1
ORDER BY `margin` ASC,  a.`season` ASC 
LIMIT 5";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "<span style=\"font-weight: 900\">$row[5] : $row[1]  $row[2]</span> - $row[3] $row[4] <em>($row[0]</em>)<br />";
			$mynone++;
        }          
echo "<tr class='$mycolour5'>";
echo "<th>Smallest margin of victory:</th>";
echo "<td>";
//$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";

//Most points
$_cp_sql = "SELECT a.`season`, b.`team`, a.`score`,a.`opp_score`,c.`team`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise` = b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise` = c.`franchise`
WHERE `gametype`=$_cp_gametype
ORDER BY `score` DESC ,  a.`season` ASC
LIMIT 5";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "<span style=\"font-weight: 900\">$row[1]  $row[2]</span> - $row[3] $row[4] <em>($row[0]</em>)<br />";
			$mynone++;
        }          
echo "<tr class='$mycolour6'>";
echo "<th>Most points scored:</th>";
echo "<td>";
//$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";

//Least points
$_cp_sql = "SELECT a.`season`, b.`team`, a.`score`,a.`opp_score`,c.`team`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise` = b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise` = c.`franchise`
WHERE `gametype`=$_cp_gametype
ORDER BY `score` ASC ,  a.`season` ASC
LIMIT 5";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "<span style=\"font-weight: 900\">$row[1]  $row[2]</span> - $row[3] $row[4] <em>($row[0]</em>)<br />";
			$mynone++;
        }          
echo "<tr class='$mycolour5'>";
echo "<th>Least points scored:</th>";
echo "<td>";
//$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";


//Total Points
$_cp_sql = "SELECT a.`season`, b.`team`, a.`score`,a.`opp_score`,c.`team`, (a.`score`+a.`opp_score`) AS `totalpnts`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise` = b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise` = c.`franchise`
WHERE `gametype`=$_cp_gametype AND `win`=1
ORDER BY `totalpnts` DESC  ,  a.`season` ASC
LIMIT 5";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "<span style=\"font-weight: 900\">$row[5] : $row[1]  $row[2]</span> - $row[3] $row[4] <em>($row[0]</em>)<br />";
			$mynone++;
        }          
echo "<tr class='$mycolour6'>";
echo "<th>Most total points:</th>";
echo "<td>";
//$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";

//Total Points
$_cp_sql = "SELECT a.`season`, b.`team`, a.`score`,a.`opp_score`,c.`team`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise` = b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise` = c.`franchise`
WHERE `gametype`=$_cp_gametype AND `win`=0
ORDER BY a.`score` DESC  ,  a.`season` ASC
LIMIT 5";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "<span style=\"font-weight: 900\">$row[1] $row[2]  </span> - $row[3] $row[4] <em>($row[0]</em>)<br />";
			$mynone++;
        }          
echo "<tr class='$mycolour5'>";
echo "<th>Most points in defeat:</th>";
echo "<td>";
//$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";
echo "</table>";
echo "<br />";
echo "<br />\n";

#Close box - 2
echo "<h4 class='w3-left-align'><em>* $_cp_gamename also known as $_cp_dannyname</em></h4>";
echo "<br />";
echo "</div>";
echo "</div>";
?>
