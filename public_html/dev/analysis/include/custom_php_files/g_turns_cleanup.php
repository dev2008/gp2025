<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require_once 'ih_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

echo "<br />";
echo "<div class='nz-card'>";

#Header
echo "<div class='w3-container $mycolour6'>";
echo "<h1>Welcome to Gameplan Analysis</h1>";
echo "<br>";
$str="<div class='w3-container $mycolour4 w3-round-xxlarge'>\n";
$str.="<h2>Cleanup</h2>";
output($str);





$str="<br>";
output($str);
require_once 'g_footer.php';
?>

