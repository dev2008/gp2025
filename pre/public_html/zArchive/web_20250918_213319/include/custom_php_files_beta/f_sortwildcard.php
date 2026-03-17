<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');

echo "<h1>Gameplan Football - Sort Wildcards</h1>";

#Change week 17 games to Silver Bowl
$_cp_sql =  "UPDATE f_games SET `gametype`=6 WHERE `gametype`=0 AND `week`=17";
$res = execute_db($_cp_sql, $conn);

echo "<p>All games set to Silver Bowl - remember to allocate the Wild Card teams!</p>";


$_cp_sql = "SELECT season,`gametype`,COUNT(`gametype`) AS Total FROM `f_games` WHERE `week`=17 GROUP BY season,`gametype` ORDER BY season DESC ,`gametype`";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			echo "<p>$row[0] - $row[1] - $row[2]</p>";
        }  


?>
