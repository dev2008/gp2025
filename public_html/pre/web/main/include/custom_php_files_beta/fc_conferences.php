<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');
include_once('include/custom_php_files/g_functions.php');
ini_set('display_errors', '1');
echo "<br />";
#Div Card Class - 1
echo "<div class='w3-card-4'>";
echo "<header class='w3-container $mycolour1'>";
echo "<h1>";
echo "<img  class='w3-right-align' src=\"images\NCAA_logo.png\" alt=\"AAC logo\" width=\"208\" height=\"59\" >";
echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbspGameplan College Football Conference Page";
echo "</h1>";
$_cp_leaguetype="NCAA%";
echo "</header>";
//Main section - Div 2
echo "<div class='w3-container $mycolour2'>";
echo "<br />";


//Overall cyan box - Div 3
#echo "<div class='w3-panel $mycolour4 w3-round-xxlarge'>";
echo "<br />";
//Check for League choice 
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
	$_cp_sql = "SELECT MAX(`season`) FROM `f_games` WHERE `League` ='$_cp_myleague'  ";
	$result = $conn->prepare($_cp_sql); 
	$result->execute(); 
	//Loop through results
	while($row = fetch_row_db($result)){
		$_cp_myseason =  $row[0];
	   }  
	$_cp_myselected = ' - ';
}
//League selector
	#Loop through all leagues and select
	$_cp_sql = "SELECT DISTINCT `league`, `season` FROM `fc_confgames` WHERE `league` LIKE '$_cp_myleague' ORDER BY `league` ASC, `season`  DESC";        
	$res = execute_db($_cp_sql, $conn);
	echo "<form action='index.php?function=show_static_page&id_static_page=17' method='post'>\n";	
	echo "<select name='_cp_mychoice'  onchange='this.form.submit()'>\n";
        while($row = fetch_row_db($res)){
			echo "<option value='$row[0]-$row[1]'";
			$_cp_mychoice = $row[0];
			$_cp_mychoice .= '-';
			$_cp_mychoice .= $row[1];
			$x = ($_cp_mychoice == $_cp_myselected) ? " selected ": "";
			echo "$x>$row[0] - $row[1]</option>\n";
        } 
	echo "</select>\n";	
	echo "</form>\n";	
	echo "</script>\n";
	echo "<br />\n";
	echo "<hr />\n";


//start of content
echo "<div class='w3-col' style='width:5%'><p></p></div>\n";
echo "<div class='w3-col w3-panel $mycolour3 w3-card-4 w3-round-xxlarge w3-centred'  style='width:90%;'>\n";
//Select Conferences
$_cp_sql = "SELECT `conf_id`, `conf_name`, `conf_logo`, `conf_alt` FROM `fc_conferences` WHERE `conf_league`='$_cp_myleague' ORDER BY `conf_name`";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$number_of_rows = $result->rowCount() ; 
$i=1;
//Loop through Conferences
while($row = fetch_row_db($result)){
		$_cp_myconf = $row[1];
		$_cp_myconflogo = $row[2];
		$_cp_myconfalt = $row[2];
		echo "<p><img src='images\\$_cp_myconflogo' alt='$_cp_myconfalt' height='86' width='80'></p>\n";
		//Start table
		echo "<table style='width:40%' class='w3-table w3-striped w3-bordered w3-mobile'>\n";
		echo "<caption text-align: left;>$_cp_myconf Conference</caption>\n";
		echo "<tr class='$mycolour7'><th>School&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</th><th>Record</th><th class='w3-right-align'>For</th><th class='w3-right-align'>Against</th></th>\n";
		$_cp_sql2 = "SELECT a.`myconf`,b.`team`,a.`wins`, a.`losses`, a.`ties`, a.`pfor`, a.`pagn` , b.`team` 
			FROM `fc_confgames` a 
				INNER JOIN `fc_franchises` b on a.`franchise` = b.`franchise` 
			WHERE a.`league` = '$_cp_myleague' AND a.`season` = $_cp_myseason AND a.`myconf` = '$_cp_myconf' 
			GROUP BY a.`myconf`,a.`franchise` 
			ORDER BY a.`myconf`,a.`pts` DESC, `diff` DESC,  a.`pfor` DESC
			";
		#echo "<p>$_cp_sql2</p>";	
		$result2 = $conn->prepare($_cp_sql2); 
		$result2->execute(); 
		//Loop through Conference Standings
		while($row2 = fetch_row_db($result2)){
				//Check for any ties then create record
				if (0==$row2[4]){
					$_cp_myrecord="$row2[2] - $row2[3]";
				} else {
					$_cp_myrecord="$row2[2] - $row2[3] ($row2[4]t)";
				}
			if (($i % 2) == 1) {
				echo "<tr class='$mycolour6 w3-right-align'><td>$row2[1]</td><td>$_cp_myrecord</td><td class='w3-right-align'>$row2[5]</td><td class='w3-right-align'>$row2[6]</td></tr>";
			} else {
				echo "<tr class='$mycolour1 w3-right-align'><td>$row2[1]</td><td>$_cp_myrecord</td><td class='w3-right-align'>$row2[5]</td><td class='w3-right-align'>$row2[6]</td></tr>";
			} 
			$i++;
		//End of loop through Conference Standings
		}
	//End of loop through Conferences
	echo "</table>\n";
	echo "<br />\n";
					//All tiem Conference records
					echo "<h3>All time wins:-</h3>";
					echo "<p><em>";
					$_cp_sql22="SELECT `team`, `ConfWins` FROM `fc_franchises` WHERE `conference` = '$_cp_myconf' AND `league` LIKE '$_cp_myleague' ORDER BY `ConfWins` DESC";
					#echo "<p>$_cp_sql22</p>";
					$result22 = $conn->prepare($_cp_sql22); 
					$result22->execute();
					$j=0;
					while($row22 = fetch_row_db($result22)){
					if ($j<3){
					echo "$row22[0] ($row22[1]), ";
					} else {
					echo "$row22[0] ($row22[1])";	
					}
					$j++;
					}
					echo "</em></p>";	
	echo "<br />\n";
	echo "<hr />\n";
	}


//End of content
echo "<br />";
echo "</div>";


#Close yellow box - 2
echo "<br />";
echo "</div>";
echo "<div class='w3-col' style='width:5%'><p></p></div>";


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
