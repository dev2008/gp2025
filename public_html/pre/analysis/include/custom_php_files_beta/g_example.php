<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
//Bring in required files
require_once 'g_functions.php';
require_once 'bb_functions.php';

$str="<br /><div class='w3-card-4'>";
$str.="<header class='w3-container w3-blue-gray'>";
$str.="<h1>Example page</h1>";
$str.="</header>";
output($str);


$str="<div class='w3-container w3-pale-blue'>";
output($str);
//Text for middle box
$str="<h2>This is an example!</h2>";
output($str);

$str="<div class='w3-container w3-teal'>\n";
output($str);


require_once 'pdo_example.php';


require_once 'g_footer.php';
?>
