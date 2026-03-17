<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
//This should only ever be called from a form submission
$time_start = microtime(true);
require_once 'g_functions.php';
require_once 'bb_functions.php';
require_once 'mydatabase.php';

$str="<br /><div class='nz-card'>";
$str.="<header class='w3-container w3-blue-gray'>";
$str.="<h1>Baseball ASG Update</h1>";
$str.="</header>";
output($str);


$str="<div class='w3-container w3-pale-blue'>";
output($str);
//Text for middle box
$str="<h2>About to process ASG updates</h3>";
output($str);

$str="<div class='w3-container w3-teal'>\n";
output($str);

$_cp_sql="UPDATE `bb_asgames` SET `totscore` = `al`+`nl` WHERE 1";
$_cp_data="1";
$_cp_updateasg=nz_pdo($_cp_sql,$conn);
if (0<>$_cp_updateasg) {
		$str="<h2>**ERROR** DB update failed ($str) **ERROR**</h2>\n";
		output($str);
	}

$_cp_sql="UPDATE `bb_asgames` SET `hiscore`=`al` WHERE 1";
$_cp_data="1";
$_cp_updateasg=nz_pdo($_cp_sql,$conn);
if (0<>$_cp_updateasg) {
		$str="<h2>**ERROR** DB update failed ($str) **ERROR**</h2>\n";
		output($str);
	}

$_cp_sql="UPDATE `bb_asgames` SET `hiscore`=`nl` WHERE `nl`>`al`;";
$_cp_data="1";
$_cp_updateasg=nz_pdo($_cp_sql,$conn);
if (0<>$_cp_updateasg) {
		$str="<h2>**ERROR** DB update failed ($str) **ERROR**</h2>\n";
		output($str);
	}

$_cp_sql="UPDATE `bb_asgames` SET `margin`=`nl`-`al` WHERE `nl`>`al`;";
$_cp_data="1";
$_cp_updateasg=nz_pdo($_cp_sql,$conn);
if (0<>$_cp_updateasg) {
		$str="<h2>**ERROR** DB update failed ($str) **ERROR**</h2>\n";
		output($str);
	}

$_cp_sql="UPDATE `bb_asgames` SET `margin`=`al`-`nl` WHERE `al`>`nl`;";
$_cp_data="1";
$_cp_updateasg=nz_pdo($_cp_sql,$conn);
if (0<>$_cp_updateasg) {
		$str="<h2>**ERROR** DB update failed ($str) **ERROR**</h2>\n";
		output($str);
	}
	
$_cp_sql="UPDATE `bb_asgames` SET `winner` = 'AL' WHERE `al`>`nl`";
$_cp_data="1";
$_cp_updateasg=nz_pdo($_cp_sql,$conn);
if (0<>$_cp_updateasg) {
		$str="<h2>**ERROR** DB update failed ($str) **ERROR**</h2>\n";
		output($str);
	}
	
$_cp_sql="UPDATE `bb_asgames` SET `winner` = 'NL' WHERE `nl`>`al`";
$_cp_data="1";
$_cp_updateasg=nz_pdo($_cp_sql,$conn);
if (0<>$_cp_updateasg) {
		$str="<h2>**ERROR** DB update failed ($str) **ERROR**</h2>\n";
		output($str);
	}

//Add path to report
$_cp_sql = "SELECT `id`,`league`,`season` FROM `bb_asgames` WHERE `url` IS NULL;";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$number_of_rows = number_format($result->rowCount() ); 
$mycount=0;	
//Loop through results
	while($row = fetch_row_db($result)){
		$_cp_myid = $row[0];
		$_cp_myleague = $row[1];
		$_cp_myseason = $row[2];
		$_cp_myurl="'https://files.gameplan.org.uk/asg/";
		$_cp_myurl.=$_cp_myleague;
		$_cp_myurl.="_Season";
		$_cp_myurl.=$_cp_myseason;
		$_cp_myurl.=".pdf'";
		$_cp_sql = "UPDATE `bb_asgames` SET `url`=$_cp_myurl WHERE `id` = '$_cp_myid'";
		$result1 = $conn->prepare($_cp_sql); 
		$result1->execute(); 
	}	

$str="<h3>All Star Games Updated</h3>\n";
output($str);



require_once 'g_footer.php';
?>
