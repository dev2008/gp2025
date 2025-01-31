<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');

echo "<h1>Gameplan Pro Football Division Standings Page</h1>";

if (isset($_POST['_cp_mychoice']) && !empty($_POST['_cp_mychoice'])) {
	$_cp_myleague =  substr($_POST['_cp_mychoice'],0,5);
	$_cp_myseason =  substr($_POST['_cp_mychoice'],-4);
	$_cp_myselected = $_POST['_cp_mychoice'];
} elseif (isset($_GET['_cp_mychoice']) && !empty($_GET['_cp_mychoice'])) { 
	$_cp_myleague =  substr($_GET['_cp_mychoice'],0,5);
	$_cp_myseason =  substr($_GET['_cp_mychoice'],-4);
	$_cp_myselected = $_GET['_cp_mychoice'];
} else {
	$_cp_myleague = 'NFLAR';
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
	$_cp_sql = "SELECT DISTINCT `league`, `season` FROM `f_games` WHERE `league` LIKE '$_cp_myleague' ORDER BY `league` ASC, `season`  DESC";        
	$res = execute_db($_cp_sql, $conn);
	#echo "<p>$_cp_sql</p>";
	//Start the form
#	echo "<script type='text/javascript'>";
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

#	echo "<br />";

#	echo "<input type='submit' />";
	echo "</form>\n";	
	echo "</script>\n";
	echo "<br />\n";


//Changed this to Array so we can do it in right order
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
				echo "<table style='width:40%'  class='w3-table w3-striped w3-bordered'>";
				echo "<caption><span style=\"font-weight: 900\">$_cp_myconf</span></caption>";
				echo "<tr><th>Team</th><th>Record</th><th class='w3-right-align'>For</th><th class='w3-right-align'>Against</th></th>";
			//End of if loop
			}
			if (5==$i){
				echo "<br />";
				echo "<br />";
				echo "<table style='width:40%'  class='w3-table w3-striped w3-bordered'>";
				echo "<caption><span style=\"font-weight: 900\">$_cp_myconf</span></caption>";
				echo "<tr><th>Team</th><th>Record</th><th class='w3-right-align'>For</th><th class='w3-right-align'>Against</th></th>";
			//End of if loop
			}	
			if (9==$i){
				echo "<br />";
				echo "<br />";
				echo "<table style='width:40%'  class='w3-table w3-striped w3-bordered'>";
				echo "<caption><span style=\"font-weight: 900\">$_cp_myconf</span></caption>";
				echo "<tr><th>Team</th><th>Record</th><th class='w3-right-align'>For</th><th class='w3-right-align'>Against</th></th>";
			//End of if loop
			}
			if (13==$i){
				echo "<br />";
				echo "<br />";
				echo "<table style='width:40%'  class='w3-table w3-striped w3-bordered'>";
				echo "<caption><span style=\"font-weight: 900\">$_cp_myconf</span></caption>";
				echo "<tr><th>Team</th><th>Record</th><th class='w3-right-align'>For</th><th class='w3-right-align'>Against</th></th>";
			//End of if loop
			}
			if (17==$i){
				echo "<br />";
				echo "<br />";
				echo "<table style='width:40%'  class='w3-table w3-striped w3-bordered'>";
				echo "<caption><span style=\"font-weight: 900\">$_cp_myconf</span></caption>";
				echo "<tr><th>Team</th><th>Record</th><th class='w3-right-align'>For</th><th class='w3-right-align'>Against</th></th>";
			//End of if loop
			}
			if (21==$i){
				echo "<br />";
				echo "<br />";
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
