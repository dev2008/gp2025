<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

$time_start = microtime(true);
#$str="<br /><div class='w3-card-4'>";
#$str.="<header class='w3-container w3-blue-gray'>";
#$str.="<h1>Baseball Processing</h1>";
#$str.="</header>";
#output($str);

#$str="<div class='w3-container w3-pale-blue'>";
#output($str);
//Text for middle box
$str="<h2>Processing Special Actions.</h3>";
output($str); 


/*
//TODO - Change it to use appropriate turnset ID
$_cp_sql55="SELECT `tf_seq` as `lineno`, `tf_line` as `linetext` FROM `g_turnsfull` WHERE `ts_id`=1;";
$_cp_myarray=nz_pdo_assoc($_cp_sql55,$conn);	
$str="<div class='w3-container w3-teal'>\n";
output($str);
#nz_debug($_cp_myarray);
foreach ($_cp_myarray as $row) {
    $mylineno=$row['lineno'];
    $mylinetext=trim($row['linetext']);
    if ('Special Actions'==substr($mylinetext,0,15)){
			$str="<h2>Found special starting actions on line $mylineno.</h3>";
			output($str);		
		}	
	$pattern = "/SCOUT /";
	if(1==preg_match($pattern, $mylinetext)) {
		$str="<h2>Found Scouting action on line $mylineno</h3>";
		output($str);
		$_cp_etext = preg_split("/[\s,:]+/", $mylinetext);
		nz_debug($_cp_etext);
		//Work out skill values
		//Run grate function
		//Update DB 
		if ('Pit'==$_cp_etext[3]) {
			echo "<p>#$_cp_etext[1] is a Pitcher</p>";
			
		} else {
			echo "<p>#$_cp_etext[1] is a not a Pitcher</p>";
		}
	} 
	
}
*/
//Values
$_cp_ptype='Pit';
$_cp_skill1='Av';
$_cp_skill2='Av';
$_cp_skill3='Go';
$_cp_skill4='Av';
$_cp_pno=35;
//DB Updates
$_cp_sql="	UPDATE `bb_dplayers` 
			SET `dp_skill1`='$_cp_skill1',`dp_skill2`='$_cp_skill2',`dp_skill3`='$_cp_skill3',`dp_skill4`='$_cp_skill4', `scouted`='Y'
			WHERE `dp_league`='MLB21' AND `dp_season`=30 AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);	
$grate=draft_gpm($_cp_ptype, $_cp_skill1, $_cp_skill2, $_cp_skill3, $_cp_skill4);
$_cp_sql="	UPDATE `bb_dplayers` 
			SET `grate`=$grate
			WHERE `dp_league`='MLB21' AND `dp_season`=30 AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);	

//Values
$_cp_ptype='Pit';
$_cp_skill1='Go';
$_cp_skill2='Go';
$_cp_skill3='Av';
$_cp_skill4='Av';
$_cp_pno=36;
//DB Updates
$_cp_sql="	UPDATE `bb_dplayers` 
			SET `dp_skill1`='$_cp_skill1',`dp_skill2`='$_cp_skill2',`dp_skill3`='$_cp_skill3',`dp_skill4`='$_cp_skill4', `scouted`='Y'
			WHERE `dp_league`='MLB21' AND `dp_season`=30 AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);	
$grate=draft_gpm($_cp_ptype, $_cp_skill1, $_cp_skill2, $_cp_skill3, $_cp_skill4);
$_cp_sql="	UPDATE `bb_dplayers` 
			SET `grate`=$grate
			WHERE `dp_league`='MLB21' AND `dp_season`=30 AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);

//Values
$_cp_ptype='Bat';
$_cp_skill1='Go';
$_cp_skill2='Fa';
$_cp_skill3='Av';
$_cp_skill4='Go';
$_cp_pno=37;
//DB Updates
$_cp_sql="	UPDATE `bb_dplayers` 
			SET `dp_skill1`='$_cp_skill1',`dp_skill2`='$_cp_skill2',`dp_skill3`='$_cp_skill3',`dp_skill4`='$_cp_skill4', `scouted`='Y'
			WHERE `dp_league`='MLB21' AND `dp_season`=30 AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);	
$grate=draft_gpm($_cp_ptype, $_cp_skill1, $_cp_skill2, $_cp_skill3, $_cp_skill4);
$_cp_sql="	UPDATE `bb_dplayers` 
			SET `grate`=$grate
			WHERE `dp_league`='MLB21' AND `dp_season`=30 AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);	

//Values
$_cp_ptype='Pit';
$_cp_skill1='Go';
$_cp_skill2='Av';
$_cp_skill3='Fa';
$_cp_skill4='Go';
$_cp_pno=38;
//DB Updates
$_cp_sql="	UPDATE `bb_dplayers` 
			SET `dp_skill1`='$_cp_skill1',`dp_skill2`='$_cp_skill2',`dp_skill3`='$_cp_skill3',`dp_skill4`='$_cp_skill4', `scouted`='Y'
			WHERE `dp_league`='MLB21' AND `dp_season`=30 AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);	
$grate=draft_gpm($_cp_ptype, $_cp_skill1, $_cp_skill2, $_cp_skill3, $_cp_skill4);
$_cp_sql="	UPDATE `bb_dplayers` 
			SET `grate`=$grate
			WHERE `dp_league`='MLB21' AND `dp_season`=30 AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);

//Values
$_cp_ptype='Pit';
$_cp_skill1='Fa';
$_cp_skill2='Av';
$_cp_skill3='Fa';
$_cp_skill4='Av';
$_cp_pno=39;
//DB Updates
$_cp_sql="	UPDATE `bb_dplayers` 
			SET `dp_skill1`='$_cp_skill1',`dp_skill2`='$_cp_skill2',`dp_skill3`='$_cp_skill3',`dp_skill4`='$_cp_skill4', `scouted`='Y'
			WHERE `dp_league`='MLB21' AND `dp_season`=30 AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);	
$grate=draft_gpm($_cp_ptype, $_cp_skill1, $_cp_skill2, $_cp_skill3, $_cp_skill4);
$_cp_sql="	UPDATE `bb_dplayers` 
			SET `grate`=$grate
			WHERE `dp_league`='MLB21' AND `dp_season`=30 AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);

//Values
$_cp_ptype='Bat';
$_cp_skill1='Fa';
$_cp_skill2='Av';
$_cp_skill3='Go';
$_cp_skill4='Fa';
$_cp_pno=40;
//DB Updates
$_cp_sql="	UPDATE `bb_dplayers` 
			SET `dp_skill1`='$_cp_skill1',`dp_skill2`='$_cp_skill2',`dp_skill3`='$_cp_skill3',`dp_skill4`='$_cp_skill4', `scouted`='Y'
			WHERE `dp_league`='MLB21' AND `dp_season`=30 AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);	
$grate=draft_gpm($_cp_ptype, $_cp_skill1, $_cp_skill2, $_cp_skill3, $_cp_skill4);
$_cp_sql="	UPDATE `bb_dplayers` 
			SET `grate`=$grate
			WHERE `dp_league`='MLB21' AND `dp_season`=30 AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);

//Values
$_cp_ptype='Cat';
$_cp_skill1='Fa';
$_cp_skill2='Av';
$_cp_skill3='Go';
$_cp_skill4='Fa';
$_cp_pno=41;
//DB Updates
$_cp_sql="	UPDATE `bb_dplayers` 
			SET `dp_skill1`='$_cp_skill1',`dp_skill2`='$_cp_skill2',`dp_skill3`='$_cp_skill3',`dp_skill4`='$_cp_skill4', `scouted`='Y'
			WHERE `dp_league`='MLB21' AND `dp_season`=30 AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);	
$grate=draft_gpm($_cp_ptype, $_cp_skill1, $_cp_skill2, $_cp_skill3, $_cp_skill4);
$_cp_sql="	UPDATE `bb_dplayers` 
			SET `grate`=$grate
			WHERE `dp_league`='MLB21' AND `dp_season`=30 AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);

$_cp_pno=21;
$_cp_sql="UPDATE `bb_dplayers` SET `scouted` = 'Y' WHERE `dp_league`='MLB21' AND `dp_season`=30 AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);

$_cp_pno=24;
$_cp_sql="UPDATE `bb_dplayers` SET `scouted` = 'Y' WHERE `dp_league`='MLB21' AND `dp_season`=30 AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);

$_cp_pno=53;
$_cp_sql="UPDATE `bb_dplayers` SET `scouted` = 'Y' WHERE `dp_league`='MLB21' AND `dp_season`=30 AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);

$_cp_pno=54;
$_cp_sql="UPDATE `bb_dplayers` SET `scouted` = 'Y' WHERE `dp_league`='MLB21' AND `dp_season`=30 AND `dp_no`=$_cp_pno;";
nz_pdo($_cp_sql,$conn);

$_cp_sql="UPDATE  `g_turnsummary` SET `processed`=6 WHERE `turn_id`=$_cp_turnid";
nz_pdo($_cp_sql,$conn);

require_once 'g_footer.php';
?>
