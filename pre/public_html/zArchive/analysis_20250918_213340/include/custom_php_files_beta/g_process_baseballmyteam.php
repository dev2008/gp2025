<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

$time_start = microtime(true);
//Text for middle box
$str="<h2>My team is $_cp_team ($_cp_franchise) </h3>";
output($str); 

/*
  [_cp_turnid] => 1001
    [_cp_game] => Baseball
    [_cp_league] => MLB7
    [_cp_season] => 31
    [_cp_week] => 21
    [_cp_coach] => Alan Milnes
*/

$_cp_sql9="SELECT `tf_seq`, `tf_line` FROM `g_turnsfull` WHERE `up_id`=$_cp_turnid ORDER BY `tf_seq` ASC LIMIT 250 ";
$_cp_mytext=nz_pdo_array($_cp_sql9,$conn);	

foreach ($_cp_mytext as $row) {
		$_cp_rowid=$row['tf_seq'];
		$_cp_rowtext=$row['tf_line'];
		#echo "<p>$_cp_rowid - $_cp_rowtext</p>";
		if ('Turn Credits'==substr($_cp_rowtext,0,12)) {
			$_cp_rowno=$_cp_rowid+2;
			$_cp_seqno=$_cp_rowid;
		}	
		if ('Key to'==substr($_cp_rowtext,0,6)) {
			$_cp_rownomax=$_cp_rowid;
		}
}

echo "<h2>Found start of roster on row $_cp_rowno with id $_cp_turnid, end of roster is line $_cp_rownomax</h3>";

$_cp_sql8="SELECT `tf_seq`, `tf_line` FROM `g_turnsfull` WHERE `up_id`=$_cp_turnid AND `tf_seq`>=$_cp_rowno AND `tf_seq`<=$_cp_rownomax ORDER BY `tf_seq` ASC ";
$_cp_myrosterlines=nz_pdo_array($_cp_sql8,$conn);	

$_cp_pitcher=0;
$_cp_batter=0;
$_cp_catcher=0;
$x=0;
echo "<p>";
foreach ($_cp_myrosterlines as $_cp_myrosterline) {
	$_cp_rowtext=$_cp_myrosterline['tf_line'];
	$_cp_rtext[$x] = preg_split("/[\s,]+/", $_cp_rowtext);
	#nz_debug($_cp_rtext[$x],'');
	#TODO This doesn't work for people with multiple names
	if (isset($_cp_rtext[$x][3])) {
	switch ($_cp_rtext[$x][3]) {
		case "Pit":
		#echo "This is a Pitcher! / ";
		$_cp_pitcher++;
		bb_mypitcher($_cp_rtext[$x],$_cp_franchise,$_cp_turnid,$conn);	
		break;
	  case "Bat":
		#echo "This is a Batter! / ";
		$_cp_batter++;
		break;	  
	  case "Cat":
		#echo "This is a Catcher! / ";
		$_cp_catcher++;
		break;
	  default:
		;
	}
	$x++;
	}
}
echo "Finished loading my team!</p>";

if ((($_cp_pitcher+$_cp_catcher+$_cp_batter)>28) AND (($_cp_pitcher+$_cp_catcher+$_cp_batter)<33)) {
	echo "<p>Found $_cp_pitcher Pitchers, $_cp_catcher Catchers and $_cp_batter Batters!</p>";
} else {
	echo "<h2>Sorry there has been an issue!!</h3>";
}
?>
