<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require_once 'g_functions.php';

//Create basic layout

$str="";
$str.= "<div class='w3-panel w3-theme-d5 w3-text-white w3-round-xxlarge '>";
$str.="<h1><img src=\"images\NFL_logo.png\" alt=\"NFL logo\" width='91' height='125'> Gameplan Pro Football Bowl Records Page</h1>";
$str.= "<div class='w3-panel w3-theme w3-round-large '>";
output($str);

#Main content
if (isset($_POST['_cp_myleague']) && !empty($_POST['_cp_myleague'])) {
	$_cp_myleague =  $_POST['_cp_myleague'];
	#include_once('f_teamdetails.php');
} elseif (isset($_GET['_cp_myleague']) && !empty($_GET['_cp_myleague'])) { 
	$_cp_myleague =  $_GET['_cp_myleague'];
	#include_once('f_teamdetails.php');	
} else {
	$_cp_myleague = 'NFLAR';
}
$str="Choose League: ";
$_cp_sql = "SELECT DISTINCT `league` FROM `fp_franchises` WHERE `ftype`='Pro' ORDER BY `league` ASC";        
$res = execute_db($_cp_sql, $conn);
$str.="<form action='index.php?function=show_static_page&id_static_page=12' method='post'>\n";	
$str.= "<select name='_cp_myleague'  onchange='this.form.submit()'>\n";
while($row = fetch_row_db($res)){
	$str.= "<option value='$row[0]'";
	$x = ($_cp_myleague == $row[0]) ? " selected ": "";
	$str.= "$x>$row[0]</option>\n";			
} 
$str.="</select>\n";	
$str.="</form>\n";	
$str.="</script>\n";
$str.="<hr />";
$str.="<br />\n";
output($str);

if ('Not Set'<>$_cp_myleague){
	echo "<table class='w3-theme-d5 w3-bordered'>\n";
	include_once('fp_bowlrecordsinclude.php');
	include_once('fp_bowlrecordsinclude2.php');

//End of Bowl Records
echo "</table>";
}

//End of page
$str="<br />\n";
$str.="</div>";
$str.="<br />\n";
output($str);

//Start of footer
require_once 'g_footer.php';
?>
