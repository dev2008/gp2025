<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

$time_start = microtime(true);

$_cp_league='MLB6';
$_cp_season=33;
$_cp_round=1;

$str="<br>";
$str.="<h2>Processing Draft for $_cp_league s$_cp_season.</h3>";
output($str); 

$draftees = array (
	array('23','Av','Fa','Av','Av','Cat','21'),
	array('24','Av','Fa','Av','Av','Bat','20'),
	array('40','Go','Av','Av','Fa','Pit','11'),
	array('41','Av','Av','Go','Fa','Pit','10'),
	array('42','Fa','Go','Fa','Go','Bat','10'),
	array('45','Fa','Fa','Fa','Go','Bat','18'),
	array('46','Go','Go','Fa','Av','Bat','17'),
	array('47','Av','Av','Av','Fa','Cat','16'),
	array('48','Av','Av','Av','Fa','Pit','16'),				
	array('49','Fa','Go','Av','Av','Bat','16'),
	array('50','Av','Av','Fa','Av','Bat','16'),
	array('51','Fa','Av','Go','Fa','Bat','16'),
	array('52','Av','Av','Av','Av','Bat','15'),
	array('54','Av','Av','Go','Fa','Pit','15'),	
	array('55','Av','Fa','Fa','Av','Cat','20'),
	array('73','Av','Po','Av','Av','Bat','17'),
	array('74','Av','Fa','Av','Fa','Bat','17'),		
	array('75','Po','Av','Av','Av','Pit','17'),	
	array('76','Fa','Fa','Av','Fa','Pit','16'),	
);

#nz_debug($draftees);

echo "<p>";

foreach ($draftees as $draftee) {
  $_cp_pno=$draftee[0];
  $_cp_skill1=$draftee[1];
  $_cp_skill2=$draftee[2];
  $_cp_skill3=$draftee[3];
  $_cp_skill4=$draftee[4];
  $_cp_ptype=$draftee[5];
  $_cp_pot=$draftee[6];

echo "Processing Player #$_cp_pno &nbsp; &nbsp; ";

//Update skills      
$_cp_sql="	UPDATE `bb_dplayers` 
			SET `dp_skill1`='$_cp_skill1',`dp_skill2`='$_cp_skill2',`dp_skill3`='$_cp_skill3',`dp_skill4`='$_cp_skill4', `scouted`='Y'
			WHERE `dp_league`='$_cp_league' AND `dp_season`=$_cp_season AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);	

//Calculate Gordon's rating
$grate=draft_gpm($_cp_ptype, $_cp_skill1, $_cp_skill2, $_cp_skill3, $_cp_skill4,$_cp_pot);
$_cp_sql="	UPDATE `bb_dplayers` 
			SET `grate`=$grate
			WHERE `dp_league`='$_cp_league' AND `dp_season`=$_cp_season AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);	
}

echo "</p>";
/*
//Update scouted      
$_cp_sql="	UPDATE `bb_dplayers` 
			SET `scouted`='Y'
			WHERE `dp_league`='$_cp_league' AND `dp_season`=$_cp_season AND (`dp_no`=11 OR `dp_no`=12  OR `dp_no`=13 OR `dp_no`=14);";
#echo "<p>$_cp_sql</p>";			
nz_pdo($_cp_sql,$conn);	
*/

if ($_cp_round>0) {
//Players picked
$drafted = array (
array (1, 'Brewers', 1, 1),
array (2, 'Phillies', 1, 2),
array (3, 'Guardians', 1, 3),
array (4, 'Cardinals', 1, 4),
array (5, 'Mariners', 1, 5),
array (6, 'Padres', 1, 6),
array (7, 'White Sox', 1, 7),
array (14, 'Diamondbacks', 1, 8),
array (8, 'Red Sox', 1, 9),
array (9, 'Pirates', 1, 10),
array (10, 'Giants', 1, 11),
array (11, 'Cubs', 1, 12),
array (20, 'Athletics', 1, 13),
array (17, 'Blue Jays', 1, 14),
array (29, 'Rays', 1, 15),
array (12, 'Mets', 1, 16),
array (13, 'Dodgers', 1, 17),
array (15, 'Reds', 1, 18),
array (23, 'Marlins', 1, 19),
array (25, 'Rangers', 1, 20),
array (24, 'Yankees', 1, 21),
array (16, 'Twins', 1, 22),
array (22, 'Angels', 1, 23),
array (55, 'Braves', 1, 24)
);

#nz_debug($drafted);


foreach ($drafted as $mydraft) {
  $_cp_no=$mydraft[0];
  $_cp_team=$mydraft[1];
  $_cp_round=$mydraft[2];
  $_cp_select=$mydraft[3];

$_cp_sql="	UPDATE `bb_dplayers` 
			SET `dp_team`='$_cp_team', `dp_pickround`=$_cp_round, `dp_pickselect`=$_cp_select
			WHERE `dp_league`='$_cp_league' AND `dp_season`=$_cp_season AND `dp_no`=$_cp_no;";

nz_pdo($_cp_sql,$conn);	
}

echo "<h3>Processing complete for $_cp_league s$_cp_season round $_cp_round!</h3>";

} else {
	echo "<p>Draft not processed yet for $_cp_league s$_cp_season!</p>";
}


?>
