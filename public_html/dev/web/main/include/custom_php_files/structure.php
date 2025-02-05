<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require_once 'g_functions.php';

//Create basic layout
$str="";
$str.= "<div class='w3-panel w3-theme-d5 w3-text-white w3-round-xxlarge '>";
$str.="<h1><img src=\"images\NCAA_football.png\" alt=\"AAC logo\" width='160' height='95'> Gameplan College Football XYZ Page</h1>";
$str.= "<div class='w3-panel w3-theme w3-round-large '>";
output($str);

#Main content
$str="<h2>Page Content</h2>";
$str.="<br />";
$str.="<hr />";
output($str);


//End of page
$str="</div>";
output($str);

//Start of footer
require_once 'g_footer.php';
?>
