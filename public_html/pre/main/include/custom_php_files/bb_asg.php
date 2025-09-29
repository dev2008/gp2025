<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');
include_once('include/custom_php_files/g_functions.php');
ini_set('display_errors', '1');
echo "<br />";
#Div Card Class - 1
echo "<div class='w3-card-4'>";
echo "<div class='w3-container w3-theme-l2'>";

//Define parameters
$_cp_gametype=8;
$_cp_gamename="All Star Game";
$_cp_dannyname="All Star Game";
$_cp_image="MLB8.png";

include_once('bb_asginclude.php');
include_once('bb_asginclude2.php');



include_once('g_footernew.php');

?>
