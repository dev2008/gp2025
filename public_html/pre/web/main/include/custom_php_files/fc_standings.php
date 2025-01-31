<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');
#NZ
include_once('include/custom_php_files/g_functions.php');
#debug_print("Post",$_POST);
ini_set('display_errors', '1');

//Header
echo "<h1>Gameplan College Football Standings Page</h1>";
echo "<p><img src=\"images\NCAA_logo.png\" alt=\"AAC logo\" width=\"208\" height=\"59\" ></p>";
$_cp_leaguetype="NCAA%";

if (isset($_POST['_cp_mychoice']) && !empty($_POST['_cp_mychoice'])) {
	$_cp_myleague =  substr($_POST['_cp_mychoice'],0,5);
	$_cp_myseason =  substr($_POST['_cp_mychoice'],-4);
	$_cp_myselected = $_POST['_cp_mychoice'];
	$_cp_mychoice = $_POST['_cp_mychoice'];
} elseif (isset($_GET['_cp_mychoice']) && !empty($_GET['_cp_mychoice'])) { 
	$_cp_myleague =  substr($_GET['_cp_mychoice'],0,5);
	$_cp_myseason =  substr($_GET['_cp_mychoice'],-4);
	$_cp_myselected = $_GET['_cp_mychoice'];
	$_cp_mychoice = $_GET['_cp_mychoice'];
} else {
	$_cp_myleague = 'NCAA5';

	//Select maximum season for this league
	//PDO Example with row count
	$_cp_sql = "SELECT MAX(`season`) AS `myseason` FROM `f_games` WHERE `League` ='$_cp_myleague'  ";
	$result = $conn->prepare($_cp_sql); 
	$result->execute(); 
	//Loop through results
	while($row = fetch_row_db($result)){
		$_cp_myseason =  $row['myseason'];
	   }  
	$_cp_myselected = ' - ';
	$_cp_mychoice=$_cp_myleague;
	$_cp_mychoice.=$_cp_myselected;
	$_cp_mychoice.=$_cp_myseason;
}
	
#Show League Selector
	#Loop through all leagues and select
	$_cp_sql = "SELECT DISTINCT `league`, `season` FROM `f_games` WHERE `league` LIKE '$_cp_leaguetype' ORDER BY `league` ASC, `season`  DESC";        
	$res = execute_db($_cp_sql, $conn);
	#echo "<p>$_cp_sql</p>";
	//Start the form
#	echo "<script type='text/javascript'>";
	echo "<form action='index.php?function=show_static_page&id_static_page=35' method='post'>\n";	
	echo "<select name='_cp_mychoice'  onchange='this.form.submit()'>\n";
	//Populate the drop down list	
        while($row = fetch_row_db($res)){
			echo "<option value='$row[0]-$row[1]'";
			$mytext = $row[0];
			$mytext .= '-';
			$mytext .= $row[1];
			$x = ($mytext == $_cp_myselected) ? " selected ": "";
			echo "$x>$row[0] - $row[1]</option>\n";
			#echo "$_cp_mychoice / $_cp_myselected";
        } 
	echo "</select>\n";	

#	echo "<br />";

#	echo "<input type='submit' />";
	echo "</form>\n";	
#	echo "</script>\n";
	echo "<br />\n";



$_cp_sql200 = "SELECT MAX(`week`) AS 'myweek' FROM `f_games` WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason";
#echo "<p>$_cp_sql200</p>";
$result200 = $conn->prepare($_cp_sql200); 
$result200->execute(); 	
$row = $result200->fetch(PDO::FETCH_ASSOC);
$myweek=$row['myweek'];
#echo "<p>Week set to $myweek</p>";





//Display table of all teams
$_cp_sql2 = "SELECT a.`franchise`,SUM(a.`win`) AS  `wins`, SUM(a.`lose`) AS `losses`, SUM(a.`tie`) AS `ties`, SUM(a.`score`) AS `pfor`, SUM(a.`opp_score`) AS `pagn` , b.`team` 
			 FROM `f_games` a 
			     INNER JOIN `fc_franchises` b on a.`franchise` = b.`franchise` 
			 WHERE a.`league` = '$_cp_myleague' AND a.`season` = $_cp_myseason AND a.`week` <>0 AND a.`week` <>12  AND a.`week` <> 13    
			 GROUP BY a.`franchise` 
			 ORDER BY `wins` DESC, `ties` DESC, (SUM(a.`score`) -SUM(a.`opp_score`) ) DESC";
			
		#echo "<p>$_cp_sql2</p>";

		$result2 = $conn->prepare($_cp_sql2); 
		$result2->execute(); 	
		echo "<br />";				
		echo "<br />";				
		echo "<table style='width:40%'  class='w3-table w3-striped w3-bordered'>";
		echo "<tr><th>School</th><th>Record</th><th class='w3-right-align'>For</th><th class='w3-right-align'>Against</th></th>";
			while($row2 = fetch_row_db($result2)){
				if (0==$row2[3]){
					$_cp_myrecord="$row2[1] - $row2[2]";
				} else {
					$_cp_myrecord="$row2[1] - $row2[2] ($row2[3]t)";
				}
		$_cp_myteam=str_pad($row2[6],30," ");
		echo "<tr><td>$_cp_myteam</td><td>$_cp_myrecord</td><td class='w3-right-align'>$row2[4]</td><td class='w3-right-align'>$row2[5]</td></tr>";
		
			

			
		}  
		echo "</table>";



?>
