<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

$time_start = microtime(true);

$_cp_league='MLB7';
$_cp_season=33;
$_cp_round=0;

$str="<br>";
$str.="<h2>Processing Draft for $_cp_league s$_cp_season.</h3>";
output($str); 

/*
$draftees = array (
	array('14','Av','Av','Fa','Av','Bat','20'),
	array('15','Fa','Go','Av','Fa','Bat','20'),
	array('36','Go','Av','Fa','Fa','Pit','19'),
	array('37','Fa','Av','Av','Av','Pit','17'),
	array('40','Fa','Av','Av','Av','Bat','15'),
	array('41','Av','Go','Fa','Av','Pit','15'),
	array('42','Av','Fa','Fa','Fa','Bat','22'),
	array('43','Av','Go','Av','Ex','Pit','4'),
	array('44','Go','Go','Av','Av','Bat','4'),
	array('45','Go','Ex','Av','Av','Pit','3'),
	array('46','Go','Go','Av','Av','Bat','3'),
	array('68','Av','Fa','Fa','Av','Bat','18'),
	array('69','Fa','Ex','Fa','Fa','Bat','16')			
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


//Players picked
/* Round 1
$drafted = array (
array (NULL,37,1,1,10812,1442),
array (NULL,37,1,2,10812,1445),
array (NULL,37,1,3,10812,1448),
array (NULL,37,1,4,10812,1451),
array (NULL,37,1,5,10812,1454),
array (NULL,37,1,6,10812,1457),
array (NULL,37,1,7,10812,1460),
array (NULL,37,1,8,10812,1463),
array (NULL,37,1,9,10812,1466),
array (NULL,37,1,10,10812,1469),
array (NULL,37,1,11,10812,1472),
array (NULL,37,1,12,10812,1475),
array (NULL,37,1,13,10812,1478),
array (NULL,37,1,14,10812,1481),
array (NULL,37,1,15,10812,1484),
array (NULL,37,1,16,10812,1487),
array (NULL,37,1,17,10812,1490),
array (NULL,37,1,18,10812,1502),
array (NULL,37,1,19,10812,1467),
array (NULL,37,1,20,10812,1470),
array (NULL,37,1,21,10812,1473),
array (NULL,37,1,22,10812,1476),
array (NULL,37,1,23,10812,1485),
array (NULL,37,1,24,10812,1497)
);
*/

#nz_debug($drafted);

$drafted = array (
array (NULL,37,2,1,10812,1493),
array (NULL,37,2,2,10812,1496),
array (NULL,37,2,3,10812,1499),
array (NULL,37,2,4,10812,1505),
array (NULL,37,2,5,10812,1508),
array (NULL,37,2,6,10812,1511),
array (NULL,37,2,7,10812,1514),
array (NULL,37,2,8,10812,1517),
array (NULL,37,2,9,10812,1520),
array (NULL,37,2,10,10812,1443),
array (NULL,37,2,11,10812,1446),
array (NULL,37,2,12,10812,1449),
array (NULL,37,2,13,10812,1452),
array (NULL,37,2,14,10812,1455),
array (NULL,37,2,15,10812,1458),
array (NULL,37,2,16,10812,1461),
array (NULL,37,2,17,10812,1464),
array (NULL,37,2,18,10812,1483),
array (NULL,37,2,19,10812,1450),
array (NULL,37,2,20,10812,1479),
array (NULL,37,2,21,10812,1482),
array (NULL,37,2,22,10812,1486),
array (NULL,37,2,23,10812,1489),
array (NULL,37,2,24,10812,1468)
);


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
