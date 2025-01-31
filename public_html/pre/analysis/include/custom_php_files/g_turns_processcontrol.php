<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';
/*
Processed values
0	0		0
1	1		1
2	2		3
3	4		7
4	8		15
5	16		31
6	32		63
7	64		127
8	128		255
9	256		511
10	512		1023
11	1024	2047
12	2048	4095
13	4096	8191
14	8192	16383
15	16384	32767
*/

$time_start = microtime(true);
set_time_limit(0);
$_cdp_valid=0;
$str="<br />";
$str.="<div class='w3-container $mycolour15 w3-round-xxlarge'>\n";
$str.="<h1>Processing Turns!</h1>";
$str.="<h2>Select a turn to process!</h2>";
output($str);

#Show File Selector
if (isset($_POST['_cp_mychoice']) && !empty($_POST['_cp_mychoice'])) {
	$_cp_mychoice=$_POST['_cp_mychoice'];
	require_once 'g_turns_processcontrol2.php';
} else {
	//Load in unprocessed files
	$_cp_sql = "SELECT `upload_id`, `filename`, `league`, `season`, `week`, `processed`
			FROM `a_uploads` 
			WHERE 1=`processed` 
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
