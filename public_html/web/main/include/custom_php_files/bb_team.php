<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once('include/custom_php_files/g_functions.php');
$_cp_myname=$_SESSION['logged_user_infos_ar']["username_user"];
echo "<br />";
echo "<div class='w3-card-4'>";

echo "<div class='w3-container w3-theme-l2'>";
echo "<h1 class='w3-cursive'>Gameplan Baseball Team Page</h1>";
//Start of content
echo "<div class='w3-panel w3-theme-d4 w3-card-4 w3-round-xxlarge'>";
echo "<br />";
#$_cp_sql = "SELECT `f_id`,CONCAT(`f_league`,' - ',`f_team`) AS bbteam FROM `bb_franchises` WHERE 1 ORDER BY `f_league` ASC, `f_city` ASC;";        

if (isset($_POST['_cp_myleague']) && !empty($_POST['_cp_myleague'])) {
	$_cp_myleague =  $_POST['_cp_myleague'];
} elseif (isset($_GET['_cp_myleague']) && !empty($_GET['_cp_myleague'])) { 
	$_cp_myleague =  $_GET['_cp_myleague'];
} else {
	$_cp_myleague ="none";
}

if (isset($_POST['_cp_myfranchise']) && !empty($_POST['_cp_myfranchise'])) {
	$_cp_myfranchise =  $_POST['_cp_myfranchise'];
} elseif (isset($_GET['_cp_myfranchise']) && !empty($_GET['_cp_myfranchise'])) { 
	$_cp_myfranchise =  $_GET['_cp_myfranchise'];
} else {
	$_cp_myfranchise ="none";
} 

echo "<span>";
#Show League Selector
	$j=0;
	$_cp_sql = "SELECT `name` FROM `a_leagues` 
				WHERE `ltype`='Baseball'  AND `name` IN (SELECT DISTINCT `f_league` FROM `bb_franchises` )
				ORDER BY `leagueID` ASC;";        
	$res = execute_db($_cp_sql, $conn);
	echo "<form action='index.php?function=show_static_page&id_static_page=44' method='post'>";	
	echo "<select name='_cp_myleague'  onchange='this.form.submit()'>";
	echo "<option value=''>Please choose a League</option>";
      while($row = fetch_row_db($res)){
									echo "<option value='$row[0]'";
									$x = ($_cp_myleague == $row[0]) ? " selected ": "";
									echo "$x>$row[0]</option>";
									$j++;
      } 
	echo "</select>";	
	echo "</form>";	
	echo "<br />";
echo "</span>";
echo "<span>";
#Show Team Selector
	if ("none"<>$_cp_myleague) {
	$i=0;
	#$_cp_sql = "SELECT `franchise`,`league`,`team` FROM `fp_franchises` WHERE `ftype`='Pro' ORDER BY `league`,`city`,`nickname`";        
	$_cp_sql = "SELECT `f_id`,`f_league`, `f_team` AS team FROM `bb_franchises` WHERE `f_league`='$_cp_myleague'  ORDER BY `f_league` ASC, `f_city` ASC;";        
	$res = execute_db($_cp_sql, $conn);
	echo "<form action='index.php?function=show_static_page&id_static_page=44' method='post'>";	
	echo "<input type='hidden' id='_cp_myleague' name='_cp_myleague' value='$_cp_myleague'>";
	echo "<select name='_cp_myfranchise'  onchange='this.form.submit()'>";
	echo "<option value=''>Please choose a Team</option>";
      while($row = fetch_row_db($res)){
									echo "<option value='$row[0]'";
									$x = ($_cp_myfranchise == $row[0]) ? " selected ": "";
									echo "$x>$row[2]</option>";
									$i++;
      } 
	echo "</select>";	
	echo "</form>";	
	echo "<br />";
}
echo "</span>";

if ('none'<>$_cp_myfranchise) {
    require_once('include/custom_php_files/bb_team2.php');
}

require_once('include/custom_php_files/g_footernew.php');

?>
