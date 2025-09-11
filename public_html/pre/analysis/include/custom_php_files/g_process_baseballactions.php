<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

$time_start = microtime(true);
#$array=get_defined_vars();
#nz_debug($array,"All variables:");

#Find Draft no
$_cp_sql="SELECT `d_id` FROM `bb_drafts` WHERE `d_league`='$_cp_league' AND `d_season`=$_cp_season AND 1=?;";
#echo "<p>$_cp_sql</p>";
$_cp_val=1;
$draft_id=nz_pdo_single($_cp_sql,$_cp_val,$conn);
#echo "<p>Draft is #$draft_id</p>";

$_cp_sql55="SELECT `tf_seq` as `lineno`, `tf_line` as `linetext` FROM `g_turnsfull` WHERE `up_id`=$_cp_turnid;";
$_cp_myarray=nz_pdo_array($_cp_sql55,$conn);	
#nz_debug($_cp_myarray,"Array:");
foreach ($_cp_myarray as $row) {
    $mylineno=$row['lineno'];
    $mylinetext=trim($row['linetext']);
    if ('Special Actions'==substr($mylinetext,0,15)){
			$str="<h2>Found special actions on line $mylineno.</h2>";
			output($str);		
		}	
	
	//Check if it's a Scouting action
	$pattern = "/SCOUT /";
	if(1==preg_match($pattern, $mylinetext)) {
		$str="<h3>Found Scouting action on line $mylineno</h3>";
		output($str);
		$_cp_etext = preg_split("/[\s,:]+/", $mylinetext);
		#nz_debug($_cp_etext,"Text:");
		$_cp_dno=$_cp_etext[1];
		if ($_cp_dno<100) {
			if ('Pit'==$_cp_etext[3]) {
				echo "<p>#$_cp_etext[1] is a Pitcher with skills $_cp_etext[18]/$_cp_etext[20]/$_cp_etext[22]/$_cp_etext[24]<br>";
				$_cp_ptype=$_cp_etext[3];
				$_cp_skill1=$_cp_etext[18];
				$_cp_skill2=$_cp_etext[20];
				$_cp_skill3=$_cp_etext[22];
				$_cp_skill4=$_cp_etext[24];
				$_cp_pot=$_cp_etext[7];
				$grate=draft_gpm($_cp_ptype, $_cp_skill1, $_cp_skill2, $_cp_skill3, $_cp_skill4, $_cp_pot);
				//Update skills      
				$_cp_sql="UPDATE `bb_dplayers` 
				SET `dp_skill1`='$_cp_skill1',`dp_skill2`='$_cp_skill2',`dp_skill3`='$_cp_skill3',`dp_skill4`='$_cp_skill4', `scouted`='Y', `grate`=$grate
				WHERE `d_id`=$draft_id AND `dp_no`=$_cp_dno;";
				#echo "<p>$_cp_sql</p>";
				nz_pdo($_cp_sql,$conn);	
				echo "</p>";
			} else {
				echo "<p>#$_cp_etext[1] is a <b>not</b> a Pitcher with skills $_cp_etext[21]/$_cp_etext[23]/$_cp_etext[25]/$_cp_etext[27]<br>";
				$_cp_ptype=$_cp_etext[3];
				$_cp_skill1=$_cp_etext[21];
				$_cp_skill2=$_cp_etext[23];
				$_cp_skill3=$_cp_etext[25];
				$_cp_skill4=$_cp_etext[27];
				$_cp_pot=$_cp_etext[8];
				$grate=draft_gpm($_cp_ptype, $_cp_skill1, $_cp_skill2, $_cp_skill3, $_cp_skill4, $_cp_pot);
				//Update skills      
				$_cp_sql="	UPDATE `bb_dplayers` 
				SET `dp_skill1`='$_cp_skill1',`dp_skill2`='$_cp_skill2',`dp_skill3`='$_cp_skill3',`dp_skill4`='$_cp_skill4', `scouted`='Y', `grate`=$grate
				WHERE `d_id`=$draft_id AND `dp_no`=$_cp_dno;";
				nz_pdo($_cp_sql,$conn);	
				echo "</p>";
				#echo "<p>SQL: $_cp_sql</p>";
				#WHERE `dp_league`='$_cp_league' AND `dp_season`=$_cp_season AND `dp_no`=$_cp_dno;";
			}
		}  else {
		#TODO Code for scouting Free Agents
		echo "<p>Ignoring Free Agent scouting action</p>";
	}
	}	 
}

	$_cp_sql="UPDATE `g_uploads` SET `processed`=`processed`+16 WHERE `upload_id`=$_cp_turnid";
	#echo $_cp_sql; 
	nz_pdo($_cp_sql,$conn);

require_once 'g_footer.php';

?>
