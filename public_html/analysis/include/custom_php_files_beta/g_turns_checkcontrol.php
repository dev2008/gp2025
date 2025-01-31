<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';


$str="<br /><div class='w3-card-4' >";
$str.="<header class='w3-container w3-blue-gray'>";
$str.="<h1>Check Turns</h1>";
$str.="</header>";
output($str);

$str="<div class='w3-container w3-pale-blue'>";
output($str);


#Load turn from database
//TODO work out processing numbers
$_cp_sql = "SELECT `turn_id`, `game`, `league`, `season`, `week`, `coach` FROM `g_turnsummary` WHERE `processed`=3 ORDER BY `turn_id` ASC LIMIT 1";
$_cp_myturn=nz_pdo_array($_cp_sql,$conn);	
#nz_debug($_cp_myturn);
if(empty($_cp_myturn)){
			exit ("<h1>****Warning - processing stopped on general error Z****</h1>");
} else {

foreach ($_cp_myturn as $row) {
    $_cp_turnid=$row['turn_id'];
    $_cp_game=$row['game'];
    $_cp_league=$row['league'];
    $_cp_season=$row['season'];
    $_cp_week=$row['week'];
    $_cp_coach=$row['coach'];
}

#$_cp_game="Run Chase";


switch ($_cp_game) {
  case "Baseball":
	//Text for middle box
	$str="<h2>About to check a Baseball turn!</h2>";
    $str.="<div class='w3-container w3-teal'>\n";
	output($str);
    require 'g_checkcontrol_baseball.php';
    break;
  case "Football":
    //Text for middle box
	$str="<h2>Sorry Gameplan Football is not yet supported!</h2>";
    $str.="<div class='w3-container w3-teal'>\n";
	output($str);
    break;
  case "Run Chase":
    //Text for middle box
	$str="<h2>Sorry Run Chase is not yet supported!</h2>";
    $str.="<div class='w3-container w3-teal'>\n";
	output($str);

    break;
  default:
    //Text for middle box
	$str="<h2>Sorry this type of turn is not yet supported!</h2>";
    $str.="<div class='w3-container w3-teal'>\n";
	output($str);

}

}

require_once 'g_footer.php';
?>
