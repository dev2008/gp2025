<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');

//Biggest Margin
$_cp_sql = "SELECT a.`season`, b.`team`, a.`score`,a.`opp_score`,c.`team`, (a.`score`-a.`opp_score`) AS `margin`
FROM `f_games` a
	INNER JOIN `fp_franchises` b ON a.`franchise` = b.`franchise`
    INNER JOIN `fp_franchises` c ON a.`opp_franchise` = c.`franchise`
WHERE `gametype`= 36
ORDER BY `margin` DESC 
LIMIT 5";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "<span style=\"font-weight: 900\">$row[5] : $row[1]  $row[2]</span> - $row[3] $row[4] <em>($row[0]</em>)<br />";
			$mynone++;
        }          
echo "<tr class='w3-theme-l3'>";
echo "<th>Biggest margin of victory:</th>";
echo "<td>";
//$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";

//Smallest Margin
$_cp_sql = "SELECT a.`season`, b.`team`, a.`score`,a.`opp_score`,c.`team`, (a.`score`-a.`opp_score`) AS `margin`
FROM `f_games` a
	INNER JOIN `fp_franchises` b ON a.`franchise` = b.`franchise`
    INNER JOIN `fp_franchises` c ON a.`opp_franchise` = c.`franchise`
WHERE `gametype`= 36 and `win`=1
ORDER BY `margin` ASC  
LIMIT 5";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "<span style=\"font-weight: 900\">$row[5] : $row[1]  $row[2]</span> - $row[3] $row[4] <em>($row[0]</em>)<br />";
			$mynone++;
        }          
echo "<tr class='w3-theme-l4'>";
echo "<th>Smallest margin of victory:</th>";
echo "<td>";
//$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";


//Most points
$_cp_sql = "SELECT a.`season`, b.`team`, a.`score`,a.`opp_score`,c.`team`
FROM `f_games` a
	INNER JOIN `fp_franchises` b ON a.`franchise` = b.`franchise`
    INNER JOIN `fp_franchises` c ON a.`opp_franchise` = c.`franchise`
WHERE `gametype`= 36
ORDER BY `score` DESC 
LIMIT 5";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "<span style=\"font-weight: 900\">$row[1]  $row[2]</span> - $row[3] $row[4] <em>($row[0]</em>)<br />";
			$mynone++;
        }          
echo "<tr class='w3-theme-l3'>";
echo "<th>Most points scored:</th>";
echo "<td>";
//$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";

//Least points
$_cp_sql = "SELECT a.`season`, b.`team`, a.`score`,a.`opp_score`,c.`team`
FROM `f_games` a
	INNER JOIN `fp_franchises` b ON a.`franchise` = b.`franchise`
    INNER JOIN `fp_franchises` c ON a.`opp_franchise` = c.`franchise`
WHERE `gametype`= 36
ORDER BY `score` ASC, `season` ASC 
LIMIT 5";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "<span style=\"font-weight: 900\">$row[1]  $row[2]</span> - $row[3] $row[4] <em>($row[0]</em>)<br />";
			$mynone++;
        }          
echo "<tr class='w3-theme-l4'>";
echo "<th>Least points scored:</th>";
echo "<td>";
//$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";


//Total Points
$_cp_sql = "SELECT a.`season`, b.`team`, a.`score`,a.`opp_score`,c.`team`, (a.`score`+a.`opp_score`) AS `totalpnts`
FROM `f_games` a
	INNER JOIN `fp_franchises` b ON a.`franchise` = b.`franchise`
    INNER JOIN `fp_franchises` c ON a.`opp_franchise` = c.`franchise`
WHERE `gametype`= 36 AND `win`=1
ORDER BY `totalpnts` DESC  
LIMIT 5";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "<span style=\"font-weight: 900\">$row[5] : $row[1]  $row[2]</span> - $row[3] $row[4] <em>($row[0]</em>)<br />";
			$mynone++;
        }          
echo "<tr class='w3-theme-l3'>";
echo "<th>Most total points:</th>";
echo "<td>";
//$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";

//Total Points
$_cp_sql = "SELECT a.`season`, b.`team`, a.`score`,a.`opp_score`,c.`team`
FROM `f_games` a
	INNER JOIN `fp_franchises` b ON a.`franchise` = b.`franchise`
    INNER JOIN `fp_franchises` c ON a.`opp_franchise` = c.`franchise`
WHERE `gametype`= 36 AND `win`=0
ORDER BY a.`score` DESC  
LIMIT 5";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "<span style=\"font-weight: 900\">$row[1] $row[2]  </span> - $row[3] $row[4] <em>($row[0]</em>)<br />";
			$mynone++;
        }          
echo "<tr class='w3-theme-l4'>";
echo "<th>Most points in defeat:</th>";
echo "<td>";
//$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";

?>
