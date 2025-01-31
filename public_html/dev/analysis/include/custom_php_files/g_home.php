<?php
//Version 11.4.2 
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

$_cp_myname=$_SESSION['logged_user_infos_ar']["username_user"];

//Create basic layout
$str="<div class='w3-container $mycolour6 w3-round-xxlarge'>";
$str.="<div class='w3-pale-green w3-round-large'>";
$str.="<h1>&nbsp;Welcome to Gameplan Analysis</h1>";
$str.="</div>";
$str.= "<div class='w3-panel $mycolour4 w3-round-medium '>";
output($str);

$_cp_dbcheck=substr($db_name,0,3);
if ('dev' == $_cp_dbcheck){
		echo "<h2>$_cp_myname you are logged on to ** DEV **</h2>";
	} elseif ('pre' == $_cp_dbcheck) {
		echo "<h2>$_cp_myname you are logged on to ** PRE-PROD **</h2>";
	} else {
				echo "<h1>Welcome to Gameplan Analysis</h1>";
	}


#Main content
$str="<h3>Please choose an item from the menu to begin.</h3>";
output($str);


//End of page
$str="</div>";
output($str);

//Start of footer
require_once 'g_footer.php';
?>
