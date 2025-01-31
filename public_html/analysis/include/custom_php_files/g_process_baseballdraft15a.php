<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';
#$a = get_defined_vars();
#nz_debug($a);

if (1==1) {
	$str="<h2>Processing draft stage 1.</h3>";
	output($str); 
	$numberofdraftees=0;
	$numberofbatters=0;
	$numberofcatchers=0;
	$numberofpitchers=0;

		//This file is only called in week 15
		$str= "<h2>Initial draft list released</h2>";
		output($str);
		$_cp_sql="INSERT INTO `bb_drafts` (`d_id`, `d_league`, `d_season`,`d_week`,`d_stage`,`uploadID`) VALUES (NULL, '$_cp_league', '$_cp_season',$_cp_week,0,$_cp_turnid);";
		$draft_id=nz_pdo($_cp_sql,$conn);
		$str= "<h3>Created record with id of $draft_id</h3>";
		output($str);



//Start of draft processing
$k=0;
$mydraftline=0;
$_cp_sql="SELECT `tf_id`, `tf_seq`, `tf_line` FROM `g_turnsfull` WHERE `up_id`=$_cp_turnid;";
$myfilearray=nz_pdo_array($_cp_sql,$conn);
#nz_debug($myfilearray);
//now loop through turn line by line
foreach ($myfilearray as $mylines){ 
$myline=$mylines['tf_line'];
$k++;
if (preg_match_all('~\b(Draft|List)\b~i', $myline, $matches)==2) {
		if (0==$mydraftline){
				echo "<h2>Found draft list starting on line $k.</h3>";	
				$mydraftline=$k+5;
		}
}
//end of loop through lines
}
$l=1;
for ($x=$mydraftline; $x <= $mydraftline+34; $x++) {
				$_cp_dtext[$l] = preg_split("/[\s,]+/", $myfilearray[$x]['tf_line']);
				$arraysize=count($_cp_dtext[$l]);
				#echo "<p>Size of array is $arraysize</p>";
				for ($m = 0; $m < $arraysize; $m++) {
				#echo "The number is: $m <br>";
				#Once you know the position of the position (!) you can allocate other details
				switch ($_cp_dtext[$l][$m]) {
					case "Bat":
					#echo "<h2>Batter found at position $m!</h3>";
					$numberofdraftees++;
					$numberofbatters++;
					$n=$m-1;
					$o=$m+1;
					$p=$m+2;
					$q=$m+3;
					$r=$m+4;
					$s=$m+5;
					$t=$m+6;
					$draftees[$numberofdraftees]['dno']=$_cp_dtext[$l][$n];
					#$draftees[$numberofdraftees]['dno']=str_replace("<T>","",$draftees[$numberofdraftees]['dno']);
					$draftees[$numberofdraftees]['ptype']=$_cp_dtext[$l][$m];
					$draftees[$numberofdraftees]['phand']=$_cp_dtext[$l][$o];
					$draftees[$numberofdraftees]['dlevel']=$_cp_dtext[$l][$p];
					$draftees[$numberofdraftees]['dbest']=$_cp_dtext[$l][$q];
					$draftees[$numberofdraftees]['dpos']=$_cp_dtext[$l][$r];
					$draftees[$numberofdraftees]['dpot']=$_cp_dtext[$l][$s];	
					$draftees[$numberofdraftees]['dval']=$_cp_dtext[$l][$t];	
					break;	

					case "Cat":
					#echo "<h2>Catcher found!</h3>";
					$numberofdraftees++;
					$numberofcatchers++;
					$n=$m-1;
					$o=$m+1;
					$p=$m+2;
					$q=$m+3;
					$r=$m+4;
					$s=$m+5;
					$t=$m+6;				
					$draftees[$numberofdraftees]['dno']=$_cp_dtext[$l][$n];
					#$draftees[$numberofdraftees]['dno']=str_replace("<T>","",$draftees[$numberofdraftees]['dno']);				
					$draftees[$numberofdraftees]['ptype']=$_cp_dtext[$l][$m];
					$draftees[$numberofdraftees]['phand']=$_cp_dtext[$l][$o];
					$draftees[$numberofdraftees]['dlevel']=$_cp_dtext[$l][$p];	
					$draftees[$numberofdraftees]['dbest']=$_cp_dtext[$l][$q];	
					$draftees[$numberofdraftees]['dpos']=$_cp_dtext[$l][$r];	
					$draftees[$numberofdraftees]['dpot']=$_cp_dtext[$l][$s];	
					$draftees[$numberofdraftees]['dval']=$_cp_dtext[$l][$t];	
					break;	

					case "Pit":
					#echo "<h2>Pitcher found!</h3>";
					$numberofdraftees++;
					$numberofpitchers++;
					$n=$m-1;
					$o=$m+1;
					$p=$m+2;
					$q=$m+3;
					$r=$m+4;
					$s=$m+5;	
					$draftees[$numberofdraftees]['dno']=$_cp_dtext[$l][$n];
					#$draftees[$numberofdraftees]['dno']=str_replace("<T>","",$draftees[$numberofdraftees]['dno']);				
					$draftees[$numberofdraftees]['ptype']=$_cp_dtext[$l][$m];
					$draftees[$numberofdraftees]['phand']=$_cp_dtext[$l][$o];
					$draftees[$numberofdraftees]['dlevel']=$_cp_dtext[$l][$p];	
					$draftees[$numberofdraftees]['dbest']=$_cp_dtext[$l][$q];		
					$draftees[$numberofdraftees]['dpos']='P';
					$draftees[$numberofdraftees]['dpot']=$_cp_dtext[$l][$r];	
					$draftees[$numberofdraftees]['dval']=$_cp_dtext[$l][$s];
					break;	

					default:
							//Switch statement for position				
							}
						
						//End of loop through arraysize 
						}
				$l++;
				//End of loop through drafttext
}

#nz_debug($draftees);
	if (90<>$numberofdraftees) {
		echo "<h2>Error - $numberofdraftees draftees found</h3>";
	} else {
		echo "<h2>$numberofdraftees draftees ready to insert into database, $numberofbatters batters, $numberofcatchers catchers and $numberofpitchers pitchers.</h3>";
}

foreach ($draftees as $draftee) {
			#Insert into draft table
			$_cp_sql="	INSERT INTO `bb_dplayers` (`dp_id`, `d_id`, `dp_no`, `dp_type`, `dp_hand`, `dp_pos`, `dp_level`, `dp_best`, `dp_pot`, `dp_val`, `dp_skill1`, `dp_skill2`, `dp_skill3`, `dp_skill4`, `srate`, `arate`, `grate`, `scouted`, `LHP`) 
			VALUES (NULL, $draft_id, '$draftee[dno]', '$draftee[ptype]', '$draftee[phand]', '$draftee[dpos]', '$draftee[dlevel]', '$draftee[dbest]', '$draftee[dpot]', '$draftee[dval]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'N', 'N');";
			nz_pdo($_cp_sql,$conn);

		}

	$_cp_sql="UPDATE `bb_dplayers` SET `LHP`='Y' WHERE `dp_type`='Pit' AND `dp_hand`='L'";
	nz_pdo($_cp_sql,$conn);
	
	$_cp_sql="UPDATE `a_uploads` SET `processed`=`processed`+16 WHERE `upload_id`=$_cp_turnid";
	#echo $_cp_sql; 
	nz_pdo($_cp_sql,$conn);

	$str="<h2>Draft stage 1 processed without errors.</h3>";
	output($str); 
	
	
} else {
	$str="<h2>Draft stage 1 already processed!!</h3>";
	output($str); 	
}
?>
