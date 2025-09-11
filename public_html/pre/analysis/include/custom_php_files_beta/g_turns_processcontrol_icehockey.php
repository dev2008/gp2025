<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

$str="<h3>Processing $_cp_league Season $_cp_season Week $_cp_week at stage $_cp_processed.</h3>";
output($str); 

#My Roster Processing 
#Stage 2
require 'g_process_icehockeyteam.php';




require_once 'g_footer.php';
?>
