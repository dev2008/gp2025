<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

$time_start = microtime(true);
set_time_limit(0);
$_cdp_valid=0;
$str="<br /><div class='w3-card-4 w3-container $mycolour6'>";
$str.="<h1>Turn Processing</h1>";
output($str);

#$str="<div class='w3-container w3-pale-blue'>";
#output($str);
//Text for middle box
$str="<div class='w3-container $mycolour4 w3-round-xxlarge'>\n";
$str.="<h3>Select a turn to process!</h3>";

output($str);

#Show File Selector
if (isset($_POST['_cp_mychoice']) && !empty($_POST['_cp_mychoice'])) {
	$_cp_mychoice=$_POST['_cp_mychoice'];
	require_once 'g_turns_processcontrol2.php';
} else {
	//Load in unprocessed files
	$_cp_sql = "SELECT `upload_id`, `filename`, `league`, `season`, `week`, `processed`
			FROM `a_uploads` 
			WHERE `processed`=1 
			ORDER BY `processed` ASC,`league` ASC, `season` ASC, `week` ASC, `filename` ASC;";
	$_cp_myturn=nz_pdo_array($_cp_sql,$conn);	
	#nz_debug($_cp_myturn,"Turn:");
		echo "<form action='index.php?function=show_static_page&id_static_page=28' method='post'>";
		echo "<select name='_cp_mychoice'>";
		echo "<option value=''>Select Turn</option>";
	foreach ($_cp_myturn as $row){
		//Retrieve turn details
		$_cp_myid =  $row['upload_id'];
		$_cp_myfile =  $row['filename'];
		$_cp_myleague =  $row['league'];
		$_cp_myseason =  $row['season'];
		$_cp_myweek =  $row['week'];

		echo "<option value='$_cp_myid'>$_cp_myleague  - s$_cp_myseason - w$_cp_myweek ($_cp_myfile)</option>";
		
		}
		echo "</select>";
		echo "<br>";
		echo "<input type='submit' name='submit' value='Submit' />";
		echo "</form>";	
		echo "</br>";	
		
}	
	
	
	

 	  
require_once 'g_footer.php';
?>
