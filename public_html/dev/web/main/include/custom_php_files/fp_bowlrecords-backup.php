<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');

$time_start = microtime(true);

echo "<h1>Gameplan Football Bowl Records Page</h1>\n";

if (isset($_POST['_cp_myleague']) && !empty($_POST['_cp_myleague'])) {
	$_cp_myleague =  $_POST['_cp_myleague'];
	#include_once('f_teamdetails.php');
} elseif (isset($_GET['_cp_myleague']) && !empty($_GET['_cp_myleague'])) { 
	$_cp_myleague =  $_GET['_cp_myleague'];
	#include_once('f_teamdetails.php');	
} else {
	$_cp_myleague = 'NFLAR';
}

#echo "<p>League - $_cp_myleague</p>";

#Show League Selector
	#Loop through all leagues and select
	$_cp_sql = "SELECT DISTINCT `league` FROM `fp_franchises` WHERE `ftype`='Pro' ORDER BY `league` ASC";        
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

if ('Not Set'<>$_cp_myleague){

echo "<table style='width:90%'  class='w3-table w3-striped w3-bordered'>";
#echo "<caption><h3>Bowl Records</h3></caption>";
include_once('fp_bowlrecordsinclude.php');
include_once('fp_bowlrecordsinclude2.php');
//End of Bowl Records
echo "</table>";

}



$time_end = microtime(true);
$time = $time_end - $time_start;
if (1==$debug_mode){
echo "<p>Processed updates in ";
echo  round($time,2) . " s";
echo "</p>";
}
?>
