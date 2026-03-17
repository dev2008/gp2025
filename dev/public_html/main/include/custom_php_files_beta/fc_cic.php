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
echo "<img  class='w3-right-align' src=\"images\CIC_logo_alt.jpg\" alt=\"AAC logo\" width=\"170\" height=\"256\" >";
echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbspGameplan College Football Commander in Chief Trophy Page";
echo "</h1>";
$_cp_leaguetype="NCAA%";
echo "</header>";
//Main section - Div 2
echo "<div class='w3-container $mycolour2'>";
echo "<br />";

if (isset($_POST['_cp_mychoice']) && !empty($_POST['_cp_mychoice'])) {
	$_cp_myleague =  substr($_POST['_cp_mychoice'],0,5);
	$_cp_myseason =  substr($_POST['_cp_mychoice'],-4);
	$_cp_myselected = $_POST['_cp_mychoice'];
} elseif (isset($_GET['_cp_mychoice']) && !empty($_GET['_cp_mychoice'])) { 
	$_cp_myleague =  substr($_GET['_cp_mychoice'],0,5);
	$_cp_myseason =  substr($_GET['_cp_mychoice'],-4);
	$_cp_myselected = $_GET['_cp_mychoice'];
} else {
	$_cp_myleague = 'NCAA5';
	//Select maximum season for this league
	//PDO Example with row count
	$_cp_sql = "SELECT MAX(`season`) FROM `f_games` WHERE `League` ='$_cp_myleague'  ";
	$result = $conn->prepare($_cp_sql); 
	$result->execute(); 
	//Loop through results
	while($row = fetch_row_db($result)){
		$_cp_myseason =  $row[0];
	   }  
	$_cp_myselected = ' - ';
}


#Show League Selector
	#Loop through all leagues and select
	$_cp_sql = "SELECT DISTINCT `league`, `season` FROM `fc_confgames` WHERE `league` LIKE '$_cp_myleague' ORDER BY `league` ASC, `season`  DESC";        
	$res = execute_db($_cp_sql, $conn);
	//Start the form
	echo "<form action='index.php?function=show_static_page&id_static_page=29' method='post'>\n";	
	echo "<select name='_cp_mychoice'  onchange='this.form.submit()'>\n";
	//Populate the drop down list	
        while($row = fetch_row_db($res)){
			echo "<option value='$row[0]-$row[1]'";
			$_cp_mychoice = $row[0];
			$_cp_mychoice .= '-';
			$_cp_mychoice .= $row[1];
			$x = ($_cp_mychoice == $_cp_myselected) ? " selected ": "";
			echo "$x>$row[0] - $row[1]</option>\n";
			#echo "$_cp_mychoice / $_cp_myselected";
        } 
	echo "</select>\n";	
	echo "</form>\n";	
	echo "<br />\n";



//start of content
echo "<div class='w3-col' style='width:5%'><p></p></div>\n";
echo "<div class='w3-col w3-panel $mycolour3 w3-card-4 w3-round-xxlarge w3-centred'  style='width:90%;'>\n";
//start of div 3
#echo "<div class='w3-panel $mycolour4 w3-round-xxlarge'>";
#echo "<p>Special Announcement</p>";
//end of div 3
#echo "</div>";



//List this years standings
$_cp_sql2 = "	SELECT b.`team` AS 'Academy' ,a.`wins`, a.`losses`, a.`ties`, a.`pfor`, a.`pagn`,a.`diff`
				FROM `fc_cicgames` a 
					INNER JOIN `fc_franchises` b on a.`franchise` = b.`franchise` 
				WHERE a.`league` = '$_cp_myleague' AND a.`season` = $_cp_myseason  
				ORDER BY a.`pts` DESC, `diff` DESC, a.`wins` DESC, a.`ties` DESC, a.`pfor` DESC 
	";
	#echo "<p>$_cp_sql2</p>";
$result2 = $conn->prepare($_cp_sql2); 
$result2->execute();
echo "<br />\n";
echo "<table class='w3-table w3-striped w3-bordered w3-mobile'>\n";
echo "<caption text-align: left;>$_cp_myseason Standings</caption>\n";
echo "<tr class='$mycolour7'><th>School</th><th>Record</th><th class='w3-right-align'>For</th><th class='w3-right-align'>Against</th></th>";	
$j=0;
while($row2 = fetch_row_db($result2)){
		if (0==$row2[3]){
			$_cp_myrecord="$row2[1] - $row2[2]";
		} else {
			$_cp_myrecord="$row2[1] - $row2[2] ($row2[3]t)";
		}
	$_cp_myteam=str_pad($row2[0],30," ");
									if (($j % 2) == 1) {
												echo "<tr class='$mycolour5'><td>$row2[0]</td><td>$_cp_myrecord</td><td class='w3-right-align'>$row2[4]</td><td class='w3-right-align'>$row2[5]</td></tr>";
									} else {
												echo "<tr class='$mycolour6'><td>$row2[0]</td><td>$_cp_myrecord</td><td class='w3-right-align'>$row2[4]</td><td class='w3-right-align'>$row2[5]</td></tr>";
									}
									$j++;
}  
echo "</table>";

#Show all time totals
echo "<h3>All time wins:-</h3>";
echo "<p><em>";
$_cp_sql22="SELECT `team`, `CICWins` FROM `fc_franchises` WHERE `Academy`=1 AND `league` LIKE '$_cp_myleague' ORDER BY `CICWins` DESC";
$result22 = $conn->prepare($_cp_sql22); 
$result22->execute();
$i=0;
while($row22 = fetch_row_db($result22)){
if ($i<2){
echo "$row22[0] ($row22[1]), ";
} else {
echo "$row22[0] ($row22[1])";	
}
$i++;
}
echo "</em></p>";
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
