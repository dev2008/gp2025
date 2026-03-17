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
echo "<img  class='w3-right-align' src=\"images\New_CBs.jpg\" alt=\"College Bowls\" width=\"200\" height=\"200\" >";
echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbspGameplan Football College Bowls Summary Page";
echo "</h1>";
$_cp_leaguetype="NCAA%";
echo "</header>";
//Main section - Div 2
echo "<div class='w3-container $mycolour2'>";
echo "<br />";
echo "<p> </p>";

if (isset($_POST['_cp_myleague']) && !empty($_POST['_cp_myleague'])) {
	$_cp_myleague =  $_POST['_cp_myleague'];
	#include_once('f_teamdetails.php');
} elseif (isset($_GET['_cp_myleague']) && !empty($_GET['_cp_myleague'])) { 
	$_cp_myleague =  $_GET['_cp_myleague'];
	#include_once('f_teamdetails.php');	
} else {
	$_cp_myleague = 'NCAA5';
}

#Show League Selector
	#Loop through all leagues and select
	$_cp_sql = "SELECT DISTINCT `league` FROM `fc_franchises` ORDER BY `league` ASC";        
	$res = execute_db($_cp_sql, $conn);
	//Start the form
#	echo "<script type='text/javascript'>";
	echo "<form action='index.php?function=show_static_page&id_static_page=12' method='post'>\n";	
	echo "<select name='_cp_myleague'  onchange='this.form.submit()'>\n";
	//Populate the drop down list	
        while($row = fetch_row_db($res)){
			echo "<option value='$row[0]'";
			$x = ($_cp_myleague == $row[0]) ? " selected ": "";
			echo "$x>$row[0]</option>\n";
			
        } 
	echo "</select>\n";	

#	echo "<br />";

#	echo "<input type='submit' />";
	echo "</form>\n";	
	echo "</script>\n";
	echo "<br />\n";

include_once('fc_bowlrecordsinclude.php');

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
