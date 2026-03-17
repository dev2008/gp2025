<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');
include_once('include/custom_php_files/g_functions.php');
ini_set('display_errors', '1');

#TODO Seperate different gametypes
#TODO make game link clickable
#TODO Sync to College
echo "<br />";
echo "<div class='w3-card-4'>";
echo "<header class='w3-container $mycolour4'>";

//Header
echo "<h1>Gameplan Pro Football League Page</h1>";
echo "</header>";
echo "<div class='w3-container $mycolour3'>";
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
	echo "<form action='index.php?function=show_static_page&id_static_page=15' method='post'>\n";	
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



$_cp_sql200 = "SELECT MAX(`week`) AS 'myweek' FROM `f_games` WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason";
#echo "<p>$_cp_sql200</p>";
$result200 = $conn->prepare($_cp_sql200); 
$result200->execute(); 	
$row = $result200->fetch(PDO::FETCH_ASSOC);
$myweek=$row['myweek'];
#echo "<p>Week set to $myweek</p>";

#If we have week 20 then show winners
#If we have week 13 then show winners
if (20==$myweek) {
echo "<div class='w3-panel w3-amber'>";
$_cp_sql20 = "SELECT `AFC_Champions`,`AFC_Coach`,`franchise`,`NFC_Champions`,`NFC_Coach`,`opp_franchise` FROM `fp_bowlgames` WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason";
$result20 = $conn->prepare($_cp_sql20); 
$result20->execute(); 	
$row = $result20->fetch(PDO::FETCH_ASSOC);
$afc=$row['AFC_Champions'];
$afcp=$row['AFC_Coach'];
$afcf=$row['franchise'];
$nfc=$row['NFC_Champions'];
$nfcp=$row['NFC_Coach'];
$nfcf=$row['opp_franchise'];

if (isset($row['AFC_Champions'])) {

if (strpos($afc, 'span') !== false) {
	echo "<h3>$_cp_myleague Superbowl Winner: $afc ($afcp)";
	$_cp_sql22 = "SELECT `won` FROM `fp_seasons` WHERE `franchise`='$afcf' AND `season`=$_cp_myseason";
	$result22 = $conn->prepare($_cp_sql22); 
	$result22->execute(); 	
	$row = $result22->fetch(PDO::FETCH_ASSOC);
	$perfect=$row['won'];
	if (16==$perfect){
		echo " - <em>Perfect Season!!</em>";
	}
	echo "</h3>";
	echo "<h4>NFC Conference Champions: $nfc ($nfcp)";
	echo "</h4>";
	
} else {
	echo "<h4>AFC Conference Champions: $afc ($afcp)</h4>";
	echo "<h3>$_cp_myleague Superbowl  Winner: $nfc ($nfcp)";
	$_cp_sql22 = "SELECT `won` FROM `fp_seasons` WHERE `franchise`='$nfcf' AND `season`=$_cp_myseason";
	$result22 = $conn->prepare($_cp_sql22); 
	$result22->execute(); 	
	$row = $result22->fetch(PDO::FETCH_ASSOC);
	$perfect=$row['won'];
	if (16==$perfect){
		echo " - <em>Perfect Season!!</em>";
	}
	echo "</h3>";
}
}	
} else {
	#20210124 Removed this
	#echo "<h3>AFC Champions: TBD";
	#echo "</h3>";
	#echo "<h3>NFC Champions: TBD";
	#echo "</h3>";
	
}


echo "<div class='w3-panel $mycolour7'>";
echo "<h3><a href='index.php?function=show_static_page&id_static_page=32'>Latest results can be found here</a></h3>";
echo "</div>";
echo "<div class='w3-panel w3-deep-orange'>";
echo "<h3><a href='index.php?function=show_static_page&id_static_page=34'>Latest standings can be found here</a></h3>";
echo "</div>";


echo "<div class='w3-panel w3-aqua'>";
echo "<table style='width:90%'  class='w3-table w3-striped w3-bordered'>";
echo "<caption class='w3-xxlarge'>League Records</caption>";
include_once('fp_bowlrecordsinclude.php');
//End of Bowl Records
echo "</table>";
echo "<br />";
echo "</div>";
echo "</div>";
echo "</div>";

//Footer
echo "<footer class='w3-container $mycolour4'>";
$_cp_sql1 = "SELECT `modification_time` FROM `f_games` WHERE 1 ORDER BY `modification_time` DESC LIMIT 1";
$res1 = execute_db($_cp_sql1, $conn);
        while($row = fetch_row_db($res1)){
			$_cp_updated = $row[0];
        }

echo "<div class='w3-right-align'>System was last updated on ";
$changeDate = date("d-m-Y H:i", strtotime($_cp_updated));
echo "$changeDate";
echo "</div>";

echo "</footer>";
?>
