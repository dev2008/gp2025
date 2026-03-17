<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');

echo "<h1>Gameplan College Football Commander in Chief Trophy Standings Page</h1>";

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


#CIC Trophy
echo "<p><img src=\"images\CIC_logo_alt.jpg\" alt=\"CIC logo\" height=\"256\" width=\"170\"></p>";

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
	echo "<table style='width:40%'  class='w3-table w3-striped w3-bordered'>";
	echo "<tr><th>School</th><th>Record</th><th class='w3-right-align'>For</th><th class='w3-right-align'>Against</th></th>";	

while($row2 = fetch_row_db($result2)){
		if (0==$row2[3]){
			$_cp_myrecord="$row2[1] - $row2[2]";
		} else {
			$_cp_myrecord="$row2[1] - $row2[2] ($row2[3]t)";
		}
	$_cp_myteam=str_pad($row2[0],30," ");
	echo "<tr><td>$row2[0]</td><td>$_cp_myrecord</td><td class='w3-right-align'>$row2[4]</td><td class='w3-right-align'>$row2[5]</td></tr>";
}  
echo "</table>";
		

?>
