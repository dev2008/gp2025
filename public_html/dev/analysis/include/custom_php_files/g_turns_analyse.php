<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

$time_start = microtime(true);
set_time_limit(0);
$_cdp_valid=0;

$str="<br />";
$str.="<div class='w3-container $mycolour15 w3-round-xxlarge'>\n";
$str.="<h1>Analysing Turns!</h1>";
$str.="<h2>Select a turn to analyse!</h2>";
output($str);

#if (1==$debug_mode) {
#	echo "<p>current user: ".get_current_user()."</p>";
#	echo "</p>script was executed under user: ".exec('whoami')."</p>";
#	pgetpost_print();
#}
 
//Set default
$roundup='N';

#Show File Selector
if (isset($_POST['_cp_mychoice']) && !empty($_POST['_cp_mychoice'])) {
	$_cp_mychoice=$_POST['_cp_mychoice'];
	require_once 'g_turns_analyse2.php';
} else {
	//Load in unprocessed files
	$_cp_sql = "SELECT `upload_id`, `filename`
			FROM `a_uploads` 
			WHERE `processed`=0 
			ORDER BY `filename` ASC";
	$_cp_myturn=nz_pdo_array($_cp_sql,$conn);	
	#nz_debug($_cp_myturn,"Turn:");
		echo "<form action='index.php?function=show_static_page&id_static_page=27' method='post'>";
		echo "<select name='_cp_mychoice'>";
		echo "<option value=''>Select Turn</option>";
	foreach ($_cp_myturn as $row){
		//Retrieve turn details
		$_cp_myid =  $row['upload_id'];
		$_cp_myfile =  $row['filename'];

		echo "<option value='$_cp_myid'>$_cp_myfile</option>";
		
		}
		echo "</select>";
		echo "<br>";
		echo "<input type='submit' name='submit' value='Submit' />";
		echo "</form>";	
		echo "</br>";	
		
}  

require_once 'g_footer.php';
?>
