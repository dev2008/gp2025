<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');

echo "<table class='w3-table w3-striped w3-bordered' style='width:65%'>\n";
echo "<caption>$_cp_gamename Records</caption>\n";
//Most runs
$_cp_sql = "SELECT `season`,`winner`,`hiscore` FROM `bb_asgames` WHERE 1 ORDER BY `hiscore` DESC,`season` ASC LIMIT 5;";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "<span style=\"font-weight: 900\">$row[2]</span> $row[1] - Season $row[0]<br />";
			$mynone++;
        }          
echo "<tr class='$mycolour6'>";
echo "<th>Most runs scored:</th>";
echo "<td>";
//$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";

//Biggest Margin
$_cp_sql = "SELECT `margin`,`winner`,`season`,`al`, `nl` FROM `bb_asgames` WHERE 1 ORDER BY `margin` DESC,`season` DESC LIMIT 5;";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "<span style=\"font-weight: 900\">$row[0] $row[1]</span> - Season $row[2], AL $row[3] - NL $row[4]<br />";
			$mynone++;
        }          
echo "<tr class='$mycolour5'>";
echo "<th>Biggest margin of victory:</th>";
echo "<td>";
//$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";

//Smallest Margin
$_cp_sql = "SELECT `margin`,`winner`,`season`,`al`, `nl`,`ex` FROM `bb_asgames` WHERE 1 ORDER BY `margin` ASC, `season` ASC LIMIT 5;";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "<span style=\"font-weight: 900\">$row[0] $row[1]</span> - Season $row[2], AL $row[3] - NL $row[4]";
			if ('N'<>$row[5]) {
				$myex=$row[5];
				$myex=str_replace("(","",$myex);
				$myex=str_replace(")","",$myex);
				$myex=str_replace("Y"," (Ex - ",$myex);
				$myrow.= $myex;
				$myrow.=")";
			}
			$myrow .= "<br />";
			$mynone++;
        }          
echo "<tr class='$mycolour6'>";
echo "<th>Smallest margin of victory:</th>";
echo "<td>";
//$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";


//Total Runs
$_cp_sql = "SELECT `season`,`totscore`, `al`, `nl` FROM `bb_asgames` WHERE 1 ORDER BY `totscore` DESC, `season` ASC  LIMIT 5;";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "<span style=\"font-weight: 900\">$row[1]</span> Season $row[0], AL $row[2] - NL $row[3]<br />";
			$mynone++;
        }          
echo "<tr class='$mycolour5'>";
echo "<th>Most total runs:</th>";
echo "<td>";
//$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";


echo "</table>";
echo "<br />";
echo "<br />\n";


?>
