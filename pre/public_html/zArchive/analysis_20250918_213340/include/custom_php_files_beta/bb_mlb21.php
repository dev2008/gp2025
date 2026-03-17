<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

$time_start = microtime(true);

$_cp_league='MLB21';
$_cp_season=32;
$_cp_round=0;
$d_id=41;

$str="<br>";
$str.="<h2>Processing Draft for $_cp_league s$_cp_season.</h3>";
output($str); 


$draftees = array (
	array('70','Go','Av','Fa','Fa','Bat','13'),
	array('71','Go','Av','Av','Fa','Bat','13'),
	array('76','Av','Fa','Fa','Fa','Pit','16'),

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

echo "Processing Player #$_cp_pno  &nbsp; &nbsp; ";

//Update skills      
$_cp_sql="	UPDATE `bb_dplayers` 
			SET `dp_skill1`='$_cp_skill1',`dp_skill2`='$_cp_skill2',`dp_skill3`='$_cp_skill3',`dp_skill4`='$_cp_skill4', `scouted`='Y'
			WHERE `d_id`=41 AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);	

//Calculate Gordon's rating
$grate=draft_gpm($_cp_ptype, $_cp_skill1, $_cp_skill2, $_cp_skill3, $_cp_skill4,$_cp_pot);
$_cp_sql="	UPDATE `bb_dplayers` 
			SET `grate`=$grate
			WHERE `d_id`=41 AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);	
}

echo "</p>";


//Update scouted (used when orders placed)
#$_cp_sql="	UPDATE `bb_dplayers` 
#			SET `scouted`='Y'
#			WHERE `d_id`=$d_id AND (`dp_no`=20 OR `dp_no`=25  OR `dp_no`=26 OR `dp_no`=50 OR `dp_no`=16 OR `dp_no`=51  OR `dp_no`=52 OR `dp_no`=53);";
#echo "<p>$_cp_sql</p>";			
#nz_pdo($_cp_sql,$conn);	



//Players picked
// Round 1
$drafted = array (
array (NULL,41,2,1,12100,1816),
array (NULL,41,2,2,12100,1819),
array (NULL,41,2,3,12100,1822),
array (NULL,41,2,4,12100,1825),
array (NULL,41,2,5,12100,1834),
array (NULL,41,2,6,12100,1837),
array (NULL,41,2,7,12100,1840),
array (NULL,41,2,8,12100,1763),
array (NULL,41,2,9,12100,1766),
array (NULL,41,2,10,12100,1769),
array (NULL,41,2,11,12100,1772),
array (NULL,41,2,12,12100,1775),
array (NULL,41,2,13,12100,1798),
array (NULL,41,2,14,12100,1778),
array (NULL,41,2,15,12100,1781),
array (NULL,41,2,16,12100,1784),
array (NULL,41,2,17,12100,1820),
array (NULL,41,2,18,12100,1823),
array (NULL,41,2,19,12100,1826),
array (NULL,41,2,20,12100,1829),
array (NULL,41,2,21,12100,1832),
array (NULL,41,2,22,12100,1835),
array (NULL,41,2,23,12100,1838),
array (NULL,41,2,24,12100,1821)
);

#nz_debug($drafted);

foreach ($drafted as $mydraft) {
  $_cp_draft=$mydraft[1];
  $_cp_round=$mydraft[2];
  $_cp_pick=$mydraft[3];
  $_cp_team=$mydraft[4];
  $_cp_player=$mydraft[5];

$_cp_sql="INSERT INTO `bb_draftpicks` (`pick_id`, `d_id`, `round`, `pick`, `f_id`, `dp_id`) VALUES (NULL, $_cp_draft, $_cp_round, $_cp_pick, $_cp_team, $_cp_player);";

nz_pdo($_cp_sql,$conn);	
}

echo "<h3>Processing complete for $_cp_league s$_cp_season round $_cp_round!</h3>";
?>
