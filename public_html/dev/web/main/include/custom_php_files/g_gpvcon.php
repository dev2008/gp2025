<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');
echo "<p></p>";
$_cp_mystage = "1";

switch ($_cp_mystage) {
    case "1":
		include "g_gpvcon1.php";
        break;
    case "2":
        echo "Your favorite color is blue!";
        break;
    case "3":
        echo "Your favorite color is green!";
        break;
    default:
		echo "<form action=\"index.php?function=show_static_page&id_static_page=30\" method=\"post\">";
		echo "<textarea name=\"myturn\" rows=\"50\" cols=\"75\">";
		echo "";
		echo "</textarea>";
		echo "<p></p>";
		echo "<input type=\"submit\">";
		echo "</form>";

}






?>
