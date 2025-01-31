<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');
include_once('include/custom_php_files/g_functions.php');
ini_set('display_errors', '1');
echo "<br />";
#Div Card Class - 1
echo "<div class='w3-card-4' style='width:90%;'>";
//Header
echo "<header class='w3-container $mycolour1'>";
echo "<h1>";
echo "<img  class='w3-right-align' src=\"images\NCAA_logo.png\" alt=\"AAC logo\" width=\"208\" height=\"59\" >";
echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbspGameplan College Football League Page";
echo "</h1>";
$_cp_leaguetype="NCAA%";
echo "</header>";
//Main section - Div 2
echo "<div class='w3-container $mycolour2'>";
echo "<br />";
echo "<h1>2nd section</h1>";


//start of content
echo "<div class='w3-col' style='width:5%'><p></p></div>\n";
echo "<div class='w3-col w3-panel $mycolour3 w3-card-4 w3-round-xxlarge w3-centred'  style='width:90%;'>\n";
//start of div 3
echo "<div class='w3-panel $mycolour4 w3-round-xxlarge'>";
echo "<p>Special Announcement</p>";
//end of div 3
echo "</div>";



echo "<h1>3rd section</h1>";
//Example of table with results
$_cp_sql2 = "SELECT `id`, `gametype` FROM `f_gametypes` WHERE `id`>7 ORDER BY `id` ASC LIMIT 6";
$j=0;
$result2 = $conn->prepare($_cp_sql2); 
$result2->execute(); 	
echo "<br />";				
echo "<table class='w3-table w3-striped w3-bordered w3-mobile'>\n";
echo "<caption text-align: left;>Example Table</caption>\n";
echo "<tr class='$mycolour7'><th>This</th><th>That</th></tr>";
				while($row2 = fetch_row_db($result2)){
									if (($j % 2) == 1) {
												echo "<tr class='$mycolour5'><td>$row2[0]</td><td>$row2[1]</td></tr>";
									} else {
												echo "<tr class='$mycolour6'><td>$row2[0]</td><td>$row2[1]</td></tr>";
									}
									$j++;
		}  
echo "</table>";
echo "<br />";




//End of content
echo "</div>";

#Close yellow box - 2
echo "<br />";
echo "</div>";
echo "<div class='w3-col' style='width:5%'><p></p></div>";
//Footer
echo "<footer class='w3-container $mycolour1'>";
$_cp_sql1 = "SELECT `modification_time` FROM `f_games` WHERE 1 ORDER BY `modification_time` DESC LIMIT 1";
$res1 = execute_db($_cp_sql1, $conn);
        while($row = fetch_row_db($res1)){
			$_cp_updated = $row[0];
        }

#Footer div - Div 4
echo "<div class='w3-right-align'>System was last updated on ";
$changeDate = date("d-m-Y H:i", strtotime($_cp_updated));
echo "$changeDate";
#End footer div - Div 4
echo "</div>";

echo "</footer>";
#End of card class - Div 1
echo "</div>";

?>
