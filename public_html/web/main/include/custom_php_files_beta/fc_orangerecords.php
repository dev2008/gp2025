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

//Define parameters
$_cp_gametype=11;
$_cp_gamename="Orange Bowl";
$_cp_dannyname="Consolation Silver";
$_cp_image="New_OB.png";


include_once('fc_recordsinclude.php');
include_once('fc_recordsinclude2.php');

echo "<div class='w3-col' style='width:5%'><p></p></div>";

echo "<footer class='w3-container $mycolour1'>";
$_cp_sql1 = "SELECT UPDATE_TIME
FROM   information_schema.tables
WHERE  TABLE_SCHEMA = '";
$_cp_sql1 .=$db_name ;
$_cp_sql1 .="' ORDER BY `tables`.`UPDATE_TIME` DESC LIMIT 1";
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
