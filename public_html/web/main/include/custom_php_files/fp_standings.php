<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');
#NZ
include_once('include/custom_php_files/g_functions.php');
#debug_print("Post",$_POST);
ini_set('display_errors', '1');

#TODO Seperate different gametypes
#TODO Fix formatting
#TODO make game link clickable
#TODO Sync to College

//Header
echo "<h1>Gameplan Pro Football Standings Page</h1>";
echo "<p><img src=\"images\NFL_logo.png\" alt=\"NFL logo\" width=\"91\" height=\"125\" ></p>";
$_cp_leaguetype="NFL%";

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
	$_cp_myleague = 'NFLAR';

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
	
#echo "<p>$_cp_myleague / $_cp_myselected / $_cp_myseason / $_cp_mychoice</p>";



#Show League Selector
	#Loop through all leagues and select
	$_cp_sql = "SELECT DISTINCT `league`, `season` FROM `f_games` WHERE `league` LIKE '$_cp_leaguetype' ORDER BY `league` ASC, `season`  DESC";        
	$res = execute_db($_cp_sql, $conn);
	#echo "<p>$_cp_sql</p>";
	//Start the form
#	echo "<script type='text/javascript'>";
	echo "<form action='index.php?function=show_static_page&id_static_page=34' method='post'>\n";	
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
	#echo "<br />\n";






///Changed this to Array so we can do it in right order
$divisions = array("AFC East", "AFC Central", "AFC West","NFC East", "NFC Central", "NFC West");
$arrlength = count($divisions);

$i=1;
//Loop through results
for($x = 0; $x < $arrlength; $x++) {
		$_cp_myconf = $divisions[$x];
		$_cp_sql2 = "SELECT b.`division` AS `myconf`,a.`franchise`,SUM(a.`win`) AS  `wins`, SUM(a.`lose`) AS `losses`, SUM(a.`tie`) AS `ties`, SUM(a.`score`) AS `pfor`, SUM(a.`opp_score`) AS `pagn` , b.`team` 
						FROM `f_games` a 
							INNER JOIN `fp_franchises` b on a.`franchise` = b.`franchise` 
						WHERE a.`league` = '$_cp_myleague' AND a.`season` = $_cp_myseason AND b.`division` = '$_cp_myconf'  AND a.`week` <>0 AND a.`week` <>17  AND a.`week` <> 18  AND a.`week` <> 19 AND a.`week` <> 20  
						GROUP BY `division`,a.`franchise` 
						ORDER BY `division`,`wins` DESC, `ties` DESC, (SUM(a.`score`) -SUM(a.`opp_score`) ) DESC";
			
			#echo "<p>$_cp_sql2</p>";
		$result2 = $conn->prepare($_cp_sql2); 
		$result2->execute(); 	
		while($row2 = fetch_row_db($result2)){
			if (1==$i){
				echo "<br />";				
				echo "<br />";		
				echo "<p><img src=\"images/afc_logo.png\" alt=\"AFC logo\" height=\"86\" width=\"120\"></p>";	
					//Display All Time winners
					echo "<br />";
					echo "<h3>AFC East all time wins:-</h3>";
					$mytext='';
					echo "<p><em>";
					$_cp_sql22="SELECT `team`,`DivisionW` FROM `fp_franchises` 
								WHERE `league`='$_cp_myleague' AND `DivisionW`>0 AND `division`='$_cp_myconf' 
								ORDER BY `DivisionW` DESC, `Wildcard` DESC, `Winner` DESC, `Runnerup` DESC";
					#echo "<p>$_cp_sql22</p>";
					$result22 = $conn->prepare($_cp_sql22); 
					$result22->execute();
					while($row22 = fetch_row_db($result22)){
						$mytext.="$row22[0] ($row22[1]), ";
					}
					$mytext = substr($mytext, 0, -2);
					echo "$mytext</em></p>";
				echo "<table style='width:40%'  class='w3-table w3-striped w3-bordered'>";
				echo "<caption><span style=\"font-weight: 900\">$_cp_myconf</span></caption>";
				echo "<tr><th>Team</th><th>Record</th><th class='w3-right-align'>For</th><th class='w3-right-align'>Against</th></th>";
			//End of if loop
			}
			if (5==$i){
				echo "<br />";
				echo "<br />";
					//Display All Time winners
					echo "<br />";
					echo "<h3>AFC Central all time wins:-</h3>";
					$mytext='';
					echo "<p><em>";
					$_cp_sql22="SELECT `team`,`DivisionW` FROM `fp_franchises` 
								WHERE `league`='$_cp_myleague' AND `DivisionW`>0 AND `division`='$_cp_myconf' 
								ORDER BY `DivisionW` DESC, `Wildcard` DESC, `Winner` DESC, `Runnerup` DESC";
					#echo "<p>$_cp_sql22</p>";
					$result22 = $conn->prepare($_cp_sql22); 
					$result22->execute();
					while($row22 = fetch_row_db($result22)){
						$mytext.="$row22[0] ($row22[1]), ";
					}
					$mytext = substr($mytext, 0, -2);
					echo "$mytext</em></p>";
				
				echo "<table style='width:40%'  class='w3-table w3-striped w3-bordered'>";
				echo "<caption><span style=\"font-weight: 900\">$_cp_myconf</span></caption>";
				echo "<tr><th>Team</th><th>Record</th><th class='w3-right-align'>For</th><th class='w3-right-align'>Against</th></th>";
			//End of if loop
			}	
			if (9==$i){
				echo "<br />";
				echo "<br />";
					//Display All Time winners
					echo "<br />";
					echo "<h3>AFC West all time wins:-</h3>";
					$mytext='';
					echo "<p><em>";
					$_cp_sql22="SELECT `team`,`DivisionW` FROM `fp_franchises` 
								WHERE `league`='$_cp_myleague' AND `DivisionW`>0 AND `division`='$_cp_myconf' 
								ORDER BY `DivisionW` DESC, `Wildcard` DESC, `Winner` DESC, `Runnerup` DESC";
					#echo "<p>$_cp_sql22</p>";
					$result22 = $conn->prepare($_cp_sql22); 
					$result22->execute();
					while($row22 = fetch_row_db($result22)){
						$mytext.="$row22[0] ($row22[1]), ";
					}
					$mytext = substr($mytext, 0, -2);
					echo "$mytext</em></p>";

				echo "<table style='width:40%'  class='w3-table w3-striped w3-bordered'>";
				echo "<caption><span style=\"font-weight: 900\">$_cp_myconf</span></caption>";
				echo "<tr><th>Team</th><th>Record</th><th class='w3-right-align'>For</th><th class='w3-right-align'>Against</th></th>";
			//End of if loop
			}
			if (13==$i){
				echo "<br />";
				echo "<br />";
				echo "<p><img src=\"images/nfc_logo.png\" alt=\"NFC logo\" height=\"86\" width=\"120\"></p>";	
					//Display All Time winners
					echo "<br />";
					echo "<h3>NFC East all time wins:-</h3>";
					$mytext='';
					echo "<p><em>";
					$_cp_sql22="SELECT `team`,`DivisionW` FROM `fp_franchises` 
								WHERE `league`='$_cp_myleague' AND `DivisionW`>0 AND `division`='$_cp_myconf' 
								ORDER BY `DivisionW` DESC, `Wildcard` DESC, `Winner` DESC, `Runnerup` DESC";
					#echo "<p>$_cp_sql22</p>";
					$result22 = $conn->prepare($_cp_sql22); 
					$result22->execute();
					while($row22 = fetch_row_db($result22)){
						$mytext.="$row22[0] ($row22[1]), ";
					}
					$mytext = substr($mytext, 0, -2);
					echo "$mytext</em></p>";
				
				echo "<table style='width:40%'  class='w3-table w3-striped w3-bordered'>";
				echo "<caption><span style=\"font-weight: 900\">$_cp_myconf</span></caption>";
				echo "<tr><th>Team</th><th>Record</th><th class='w3-right-align'>For</th><th class='w3-right-align'>Against</th></th>";
			//End of if loop
			}
			if (17==$i){
				echo "<br />";
				echo "<br />";
									//Display All Time winners
					echo "<br />";
					echo "<h3>NFC Central all time wins:-</h3>";
					$mytext='';
					echo "<p><em>";
					$_cp_sql22="SELECT `team`,`DivisionW` FROM `fp_franchises` 
								WHERE `league`='$_cp_myleague' AND `DivisionW`>0 AND `division`='$_cp_myconf' 
								ORDER BY `DivisionW` DESC, `Wildcard` DESC, `Winner` DESC, `Runnerup` DESC";
					#echo "<p>$_cp_sql22</p>";
					$result22 = $conn->prepare($_cp_sql22); 
					$result22->execute();
					while($row22 = fetch_row_db($result22)){
						$mytext.="$row22[0] ($row22[1]), ";
					}
					$mytext = substr($mytext, 0, -2);
					echo "$mytext</em></p>";

				echo "<table style='width:40%'  class='w3-table w3-striped w3-bordered'>";
				echo "<caption><span style=\"font-weight: 900\">$_cp_myconf</span></caption>";
				echo "<tr><th>Team</th><th>Record</th><th class='w3-right-align'>For</th><th class='w3-right-align'>Against</th></th>";
			//End of if loop
			}
			if (21==$i){
				echo "<br />";
				echo "<br />";
					//Display All Time winners
					echo "<br />";
					echo "<h3>NFC West all time wins:-</h3>";
					$mytext='';
					echo "<p><em>";
					$_cp_sql22="SELECT `team`,`DivisionW` FROM `fp_franchises` 
								WHERE `league`='$_cp_myleague' AND `DivisionW`>0 AND `division`='$_cp_myconf' 
								ORDER BY `DivisionW` DESC, `Wildcard` DESC, `Winner` DESC, `Runnerup` DESC";
					#echo "<p>$_cp_sql22</p>";
					$result22 = $conn->prepare($_cp_sql22); 
					$result22->execute();
					while($row22 = fetch_row_db($result22)){
						$mytext.="$row22[0] ($row22[1]), ";
					}
					$mytext = substr($mytext, 0, -2);
					echo "$mytext</em></p>";

				echo "<table style='width:40%'  class='w3-table w3-striped w3-bordered'>";
				echo "<caption><span style=\"font-weight: 900\">$_cp_myconf</span></caption>";
				echo "<tr><th>Team</th><th>Record</th><th class='w3-right-align'>For</th><th class='w3-right-align'>Against</th></th>";
			//End of if loop
			}			
			if (0==$row2[4]){
					$_cp_myrecord="$row2[2] - $row2[3]";
				} else {
					$_cp_myrecord="$row2[2] - $row2[3] ($row2[4]t)";
				}
			$_cp_myteam=str_pad($row2[7],30," ");
			echo "<tr><td>$_cp_myteam</td><td>$_cp_myrecord</td><td class='w3-right-align'>$row2[5]</td><td class='w3-right-align'>$row2[6]</td></tr>";
			$i++;
			

			
		}  
		echo "</table>";
}


?>
