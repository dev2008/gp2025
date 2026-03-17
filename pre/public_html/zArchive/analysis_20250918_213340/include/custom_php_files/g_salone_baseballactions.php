<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

$time_start = microtime(true);
$str="<br /><div class='nz-card'>";
$str.="<header class='w3-container w3-blue-gray'>";
$str.="<h1>Process Special actions</h1>";
$str.="</header>";
output($str);

$str="<div class='w3-container w3-pale-blue'>";
output($str);
//Text for middle box
$str="<h2>Finding Leagues</h2>";
output($str);
$str="<div class='w3-container w3-teal'>\n";
$str.="<h2>Making updates</h2>";
output($str);


#require_once 'bb_mlb6.php';
#require_once 'bb_mlb7.php';
#require_once 'bb_mlb8.php';
require_once 'bb_mlb21.php';

require_once 'g_footer.php';

?>
