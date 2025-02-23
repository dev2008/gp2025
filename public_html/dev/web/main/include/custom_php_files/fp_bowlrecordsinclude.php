<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');

//Superbowl wins
$_cp_sql = "SELECT b.team,SUM(a.`win`) AS `TotWins`
FROM `f_games` a 
	INNER JOIN fp_franchises b ON a.`franchise` = b.`franchise`
WHERE `week`=20 AND `gametype`=36 AND `win`=1 AND a.`league`='$_cp_myleague'  
GROUP BY b.team
ORDER BY TotWins DESC";
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
echo "<tr class='w3-theme-l3'>";
echo "<th>Franchises with multiple Superbowl wins ($mymultiples): </th>";
echo "<td>";
$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";
echo "<tr class='w3-theme-l4'>";
echo "<th>Franchises with one Superbowl win ($mysingles): </th>";
echo "<td>";
$myrow2=substr($myrow2,0,-2);
echo "$myrow2</td>";
echo "</tr>";

//No wins
$_cp_sql = "SELECT franchise,team FROM fp_franchises WHERE `league`='$_cp_myleague'  AND franchise NOT IN (SELECT franchise FROM f_games WHERE gametype=36 AND win=1 AND league='$_cp_myleague') AND franchise  IN (SELECT franchise FROM f_games WHERE gametype=36 AND league='$_cp_myleague') ORDER BY `division` ASC";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "$row[1], ";
			$mynone++;
        }          
echo "<tr class='w3-theme-l3'>";
echo "<th>Franchises with no Superbowl wins ($mynone):</th>";
echo "<td>";
$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";

//No appearances
$_cp_sql = "SELECT franchise,team FROM fp_franchises WHERE `league`='$_cp_myleague'  AND franchise NOT IN (SELECT franchise FROM f_games WHERE gametype=36 AND league='$_cp_myleague') ORDER BY `division` ASC";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "$row[1], ";
			$mynone++;
        }          
echo "<tr class='w3-theme-l4'>";
echo "<th>Franchises with no Superbowl wins or appearances ($mynone):</th>";
echo "<td>";
$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";

//Conference
echo "<tr class='w3-theme-l3'>";
echo "<th>Record by Conference: </th>";
echo "<td>";

$_cp_sql = "SELECT b.conference,SUM(a.`win`) AS `TotWins`
FROM `f_games` a 
	INNER JOIN fp_franchises b ON a.`franchise` = b.`franchise`
WHERE `week`=20 AND `gametype`=36  AND a.`league`='$_cp_myleague'  
GROUP BY b.conference
ORDER BY TotWins DESC";
$myrow = "";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			if ($row[1]>1){
			$myrow .= "$row[0] $row[1] - ";
			}
        }          $myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";

//Division
echo "<tr class='w3-theme-l4'>";
echo "<th>Record by Division: </th>";
echo "<td>";

$_cp_sql = "SELECT b.division,SUM(a.`win`) AS `TotWins`
FROM `f_games` a 
	INNER JOIN fp_franchises b ON a.`franchise` = b.`franchise`
WHERE `week`=20 AND `gametype`=36  AND a.`league`='$_cp_myleague'  
GROUP BY b.division
ORDER BY TotWins DESC";
$myrow = "";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "$row[0] $row[1], ";
        }
        $myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";
?>
