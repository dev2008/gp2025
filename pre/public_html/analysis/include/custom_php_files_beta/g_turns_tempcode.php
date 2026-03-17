<?php
$_cp_league="MLB7"; 
$_cp_season=31;
$_cp_week=15;
echo "<h2>$_cp_league Season $_cp_season Week $_cp_week is a draft turn!!</h3>"; 
$numberofdraftees=0;
$numberofbatters=0;
$numberofcatchers=0;
$numberofpitchers=0;


switch ($_cp_week) {
  case "15":

    echo "<h2>Initial draft list released!</h3>";
	$_cp_sql="INSERT INTO `bb_drafts` (`d_id`, `d_league`, `d_season`,`d_week`,`d_stage`) VALUES (NULL, '$_cp_league', '$_cp_season',$_cp_week,0);";
	$last_id=nz_pdo($_cp_sql,$conn);
	echo "<h2>Created record with id of $last_id</h3>";


//Temp*****
        #Loop through directory and find files
		global $file_info; // All the file paths will be pushed here
		$file_info = array();

		#Load file names into array
		recursive_scan('/home/alan/WebRoot/gpspn/uploads/ToProcess/');
		#Find size of array
		$myarraysize = count($file_info); 
		$str="<div class='w3-container w3-teal'>\n";
		output($str);
		$j=0;
		if ($myarraysize>1) {
			$str="<h2>Sorry but the system cannot handle multiple files at present</h2>";
			output($str);
		} else {
		$str="<h2>Found ".number_format($myarraysize)." turn to analyse</h2>";
		output($str);
		foreach ($file_info as $file){ 
			$myfilearray=file($file);
			$myfilearray=remove_sp_chr($myfilearray);
//Temp*****			


    $k=0;
    foreach ($myfilearray as $myline){ 
		$k++;
		if (preg_match_all('~\b(BK.Draft|List)\b~i', $myline, $matches)==2) {
			echo "<h2>Found draft list starting on line $k.</h3>";	
			$mydraftline=$k+5;
		}
	}
		$l=1;
		for ($x=$mydraftline; $x <= $mydraftline+26; $x++) {
			$_cp_dtext[$l] = preg_split("/[\s,]+/", $myfilearray[$x]);
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
				$draftees[$numberofdraftees]['dval']=$_cp_dtext[$l][$require_once 'g_footer.php';t];	
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
			}
				}
			$l++;
		}
		

    break;
  case "18":
    echo "<h2>Draft order set!</h3>";
    break;
  default:
    echo "<h2>Normal draft turn!</h3>";
}

//Temp*****	
}

#nz_debug_mini($draftees);

if (80<>$numberofdraftees) {
	echo "<h2>Error - $numberofdraftees draftees found</h3>";
} else {
    echo "<h2>$numberofdraftees draftees ready to insert into database, $numberofbatters batters, $numberofcatchers catchers and $numberofpitchers pitchers.</h3>";
try {
	foreach ($draftees as $draftee) {
		#echo "$draftee[dno] <br>";
		  $_cp_sql="INSERT INTO `bb_playerstemp` (`dp_type`, `dp_hand`, `dp_exp`, `c_sh`, `c_name`, `c_team`, `c_pos`, `c_level`, `c_best`, `c_pot`, `c_val`, `c_trade`, `d_sh`, `d_name`, `d_team`, `d_pos`, `d_level`, `d_best`, `d_pot`, `d_val`, `d_trade`, `d_draft`, `d_no`) 
			VALUES ('$draftee[ptype]', '$draftee[phand]', '0', NULL, NULL, NULL, '$draftee[dpos]', '$draftee[dlevel]', '$draftee[dbest]', '$draftee[dpot]', '$draftee[dval]', NULL, NULL, NULL, NULL, '$draftee[dpos]', '$draftee[dlevel]', '$draftee[dbest]', '$draftee[dpot]', '$draftee[dval]', NULL,'$last_id','$draftee[dno]' );";
			#echo "<p>$_cp_sql</p>";
			$result = $conn->prepare($_cp_sql); 
			$result->execute(); 
} 
} catch (PDOException $e) {
		echo "DataBase Error:<br>".$e->getMessage();
		exit ("<h1>****Warning - processing stopped on database error****</h1>");
} catch (Exception $e) {
		echo "General Error:<br>".$e->getMessage();
		exit ("<h1>****Warning - processing stopped on general error****</h1>");
}

}


?>

