<?php
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

// Report all errors except E_NOTICE
error_reporting(E_ALL & ~E_NOTICE);

$str="<p>Finding players from $_cp_league roundup&nbsp;&nbsp;/&nbsp;&nbsp;";
output($str); 

//TODO  - remove after testing
$_cp_sql4="TRUNCATE `bb_playerstemp`";
nz_pdo($_cp_sql4,$conn);
$_cp_sql4="ALTER TABLE `bb_playerstemp` AUTO_INCREMENT = 9000000;";
nz_pdo($_cp_sql4,$conn);

$_cp_franchisebase=asm_franchisebase($_cp_league);
$_cp_franchiselinesexp=$_cp_franchiselines;
$_cp_addme=$_cp_franchiselinesexp[11]+35;
array_push($_cp_franchiselinesexp,$_cp_addme);
#nz_debug($_cp_franchiselinesexp);
$_cp_rosterdata=array();
#nz_debug($_cp_rosterdata);
//Counter for 1st Franchise
$m=0;
//Counter for 2nd Franchise
$n=1;
//Stores a number one higher than x for retrieving roster data number
$k=1;

for ($x = 0; $x <= 11; $x++) {
	$_cp_fid1=$_cp_franchisebase+$m;
	$_cp_fid2=$_cp_franchisebase+$n;
	$str="Found Rosters to process for $_cp_franchisearray[$m] ($_cp_fid1) and $_cp_franchisearray[$n] ($_cp_fid2) - ";
	output($str); 
	$_cp_rosterdata[$m]['abbr']=$_cp_franchisearray[$m];
	$_cp_rosterdata[$n]['abbr']=$_cp_franchisearray[$n];
	$_cp_rosterdata[$m]['fid']=$_cp_fid1;
	$_cp_rosterdata[$n]['fid']=$_cp_fid2;
	$_cp_difference=$_cp_franchiselines[$k]-$_cp_franchiselines[$x];
	$_cp_sql9="SELECT `tf_line` FROM `g_turnsfull` WHERE `ts_id`=$_cp_turnid AND `tf_seq`>= $_cp_franchiselinesexp[$x] AND `tf_seq`<= $_cp_franchiselinesexp[$k] ORDER BY `tf_seq` ASC ";
	#echo "$_cp_sql9";
	$_cp_rostertext=nz_pdo_array($_cp_sql9,$conn);	
	$_cp_pitchercount=0;
	//Begin loop through roster text
	//Track rows
	$q=0;
	foreach ($_cp_rostertext as $row3) {
		//split each line into an array
		$_cp_rostertext1 = preg_split("/[\s,]+/", $row3['tf_line']);
		//If the first element of the arry is numeric then we have a player
		if (is_numeric($_cp_rostertext1[0])) {
				#$str="<p>Found numeric value on line #$q</p>";
				#output($str); 	
				#nz_debug($_cp_rostertext1);
				//First 3 are always known  
				$_cp_rosterdata[$m]['sh']=$_cp_rostertext1[0];
				$_cp_rosterdata[$m]['fname']=$_cp_rostertext1[1];
				$_cp_rosterdata[$m]['sname']=$_cp_rostertext1[2];
				//Now loop until you have reached end of surname
				//Format is 1 first name and all other names part of surname
				//Assumed it will never be more than 5 parts to a surname!!
				//Loop increments every time; myno only if it is part of surname
				$myloop = 3;
				$myno=3;
					while($myloop <= 8) {
						if ('Pit'<> $_cp_rostertext1[$myno] AND 'C'<> $_cp_rostertext1[$myno]  AND '1B'<> $_cp_rostertext1[$myno]  AND '2B'<> $_cp_rostertext1[$myno] AND 'SS'<> $_cp_rostertext1[$myno] AND '3B'<> $_cp_rostertext1[$myno] AND 'IF'<> $_cp_rostertext1[$myno] AND 'OF'<> $_cp_rostertext1[$myno] AND 'LF'<> $_cp_rostertext1[$myno] AND 'CF'<> $_cp_rostertext1[$myno] AND 'RF'<> $_cp_rostertext1[$myno]){	
								$_cp_rosterdata[$m]['sname'].=' ';
								$_cp_rosterdata[$m]['sname'].=$_cp_rostertext1[$myno];
								$myno++;
						}
					$myloop++;
					}
					#$str="<p></p>Rest of roster values starts in position #$myno</p>";
					#output($str); 	
					if ('Pit'==$_cp_rostertext1[$myno]){
						$_cp_rosterdata[$m]['pos']="P";
						$_cp_rosterdata[$m]['type']=$_cp_rostertext1[$myno];
						$_cp_rosterdata[$m]['hand']=$_cp_rostertext1[$myno+1];
						$_cp_rosterdata[$m]['level']=$_cp_rostertext1[$myno+2];
						$_cp_rosterdata[$m]['best']=$_cp_rostertext1[$myno+3];
						$_cp_rosterdata[$m]['exp']=$_cp_rostertext1[$myno+4];
						$_cp_rosterdata[$m]['pot']=$_cp_rostertext1[$myno+5];
						$_cp_rosterdata[$m]['trade']=$_cp_rostertext1[$myno+6];
						$_cp_rosterdata[$m]['val']=$_cp_rostertext1[$myno+7];
						$_cp_rosterdata[$n]['sh']=$_cp_rostertext1[$myno+9];
						$_cp_rosterdata[$n]['fname']=$_cp_rostertext1[$myno+10];
						$_cp_rosterdata[$n]['sname']=$_cp_rostertext1[$myno+11];
						$mynoB=$myno+12;
					} else {
						$_cp_rosterdata[$m]['pos']=$_cp_rostertext1[$myno];
						$_cp_rosterdata[$m]['type']=$_cp_rostertext1[$myno+1];
						$_cp_rosterdata[$m]['hand']=$_cp_rostertext1[$myno+2];
						$_cp_rosterdata[$m]['level']=$_cp_rostertext1[$myno+3];
						$_cp_rosterdata[$m]['best']=$_cp_rostertext1[$myno+4];
						$_cp_rosterdata[$m]['exp']=$_cp_rostertext1[$myno+5];
						$_cp_rosterdata[$m]['pot']=$_cp_rostertext1[$myno+6];
						$_cp_rosterdata[$m]['trade']=$_cp_rostertext1[$myno+7];
						$_cp_rosterdata[$m]['val']=$_cp_rostertext1[$myno+8];
							$_cp_rosterdata[$n]['sh']=$_cp_rostertext1[$myno+10];
							$_cp_rosterdata[$n]['fname']=$_cp_rostertext1[$myno+11];
							$_cp_rosterdata[$n]['sname']=$_cp_rostertext1[$myno+12];

						$mynoB=$myno+13;						
					}
					$myloopB = 3;
					while($myloopB <= 8) {
							if ('Pit'<> $_cp_rostertext1[$mynoB] AND 'C'<> $_cp_rostertext1[$mynoB]  AND '1B'<> $_cp_rostertext1[$mynoB]  AND '2B'<> $_cp_rostertext1[$mynoB] AND 'SS'<> $_cp_rostertext1[$mynoB] AND '3B'<> $_cp_rostertext1[$mynoB] AND 'IF'<> $_cp_rostertext1[$mynoB] AND 'OF'<> $_cp_rostertext1[$mynoB] AND 'LF'<> $_cp_rostertext1[$mynoB] AND 'CF'<> $_cp_rostertext1[$mynoB] AND 'RF'<> $_cp_rostertext1[$mynoB]){	
								$_cp_rosterdata[$n]['sname'].=' ';
								$_cp_rosterdata[$n]['sname'].=$_cp_rostertext1[$mynoB];
								$mynoB++;
								}	
					$myloopB++;
					}					
					if ('Pit'==$_cp_rostertext1[$mynoB]){
						$_cp_rosterdata[$n]['pos']="P";
						$_cp_rosterdata[$n]['type']=$_cp_rostertext1[$mynoB];
						$_cp_rosterdata[$n]['hand']=$_cp_rostertext1[$mynoB+1];
						$_cp_rosterdata[$n]['level']=$_cp_rostertext1[$mynoB+2];
						$_cp_rosterdata[$n]['best']=$_cp_rostertext1[$mynoB+3];
						$_cp_rosterdata[$n]['exp']=$_cp_rostertext1[$mynoB+4];
						$_cp_rosterdata[$n]['pot']=$_cp_rostertext1[$mynoB+5];
						$_cp_rosterdata[$n]['trade']=$_cp_rostertext1[$mynoB+6];
						$_cp_rosterdata[$n]['val']=$_cp_rostertext1[$mynoB+7];
					} else {
						$_cp_rosterdata[$n]['pos']=$_cp_rostertext1[$mynoB];
						$_cp_rosterdata[$n]['type']=$_cp_rostertext1[$mynoB+1];
						$_cp_rosterdata[$n]['hand']=$_cp_rostertext1[$mynoB+2];
						$_cp_rosterdata[$n]['level']=$_cp_rostertext1[$mynoB+3];
						$_cp_rosterdata[$n]['best']=$_cp_rostertext1[$mynoB+4];
						$_cp_rosterdata[$n]['exp']=$_cp_rostertext1[$mynoB+5];
						$_cp_rosterdata[$n]['pot']=$_cp_rostertext1[$mynoB+6];
						$_cp_rosterdata[$n]['trade']=$_cp_rostertext1[$mynoB+7];
						$_cp_rosterdata[$n]['val']=$_cp_rostertext1[$mynoB+8];		
					}
		
		//End of line so insert into database
		#nz_debug($_cp_rosterdata);
		
		//Insert first player
		$myshirt=$_cp_rosterdata[$m]['sh'];
		$mycname=$_cp_rosterdata[$m]['fname'];
		$mysname=$_cp_rosterdata[$m]['sname'];
		$mypos=$_cp_rosterdata[$m]['pos'];
		$mytype=$_cp_rosterdata[$m]['type'];
		$myhand=$_cp_rosterdata[$m]['hand'];
		$mylevel=$_cp_rosterdata[$m]['level'];
		$mybest=$_cp_rosterdata[$m]['best'];
		$myexp=$_cp_rosterdata[$m]['exp'];
		//Change rookie to 0
		if ('R'==$myexp){$myexp=0;}
		$mypot=$_cp_rosterdata[$m]['pot'];
		$mytrade=$_cp_rosterdata[$m]['trade'];
		$myval=$_cp_rosterdata[$m]['val'];

		if (is_numeric($myshirt)) {		
			$_cp_sql40="INSERT INTO `bb_playerstemp` (`p_id`, `f_league`, `p_team`, `p_sh`, `p_cname`, `p_sname`, `p_pos`, `p_type`, `p_hand`, `p_level`, `p_best`, `p_exp`, `p_pot`, `p_trade`, `p_val`,`turn`) 
			VALUES (NULL, '$_cp_league', '$_cp_fid1', '$myshirt', '$mycname', '$mysname', '$mypos', '$mytype', '$myhand', '$mylevel', '$mybest', '$myexp', '$mypot', '$mytrade', '$myval','$_cp_turnid');";
			$player_id=nz_pdo($_cp_sql40,$conn);
			//Update Transactions table	
			#$str="<p>Updated transactions table for player #$player_id</p>";
			#output($str); 
			//When would it not be setup from this process??
			#INSERT INTO `bb_transactions` (`id`, `league`, `franchise`, `player`, `season`, `week`, `source`) VALUES (NULL, 'MLB08', '10800', '9000000', '5', '1', '2000');
			#$_cp_sql42="INSERT INTO `bb_transactions` (`id`, `league`, `franchise`, `player`, `season`, `week`, `source`) VALUES (NULL, '$_cp_league', '$_cp_fid1', '$player_id', '$_cp_season', '$_cp_week', '2000');";
			#nz_pdo($_cp_sql42,$conn);
		}
		
		//Insert second player
		$myshirt=$_cp_rosterdata[$n]['sh'];
		$mycname=$_cp_rosterdata[$n]['fname'];
		$mysname=$_cp_rosterdata[$n]['sname'];
		$mypos=$_cp_rosterdata[$n]['pos'];
		$mytype=$_cp_rosterdata[$n]['type'];
		$myhand=$_cp_rosterdata[$n]['hand'];
		$mylevel=$_cp_rosterdata[$n]['level'];
		$mybest=$_cp_rosterdata[$n]['best'];
		$myexp=$_cp_rosterdata[$n]['exp'];
		//Change rookie to 0
		if ('R'==$myexp){$myexp=0;}
		$mypot=$_cp_rosterdata[$n]['pot'];
		$mytrade=$_cp_rosterdata[$n]['trade'];
		$myval=$_cp_rosterdata[$n]['val'];
		
		if (is_numeric($myshirt)) {
			$_cp_sql41="INSERT INTO `bb_playerstemp` (`p_id`, `f_league`, `p_team`, `p_sh`, `p_cname`, `p_sname`, `p_pos`, `p_type`, `p_hand`, `p_level`, `p_best`, `p_exp`, `p_pot`, `p_trade`, `p_val`,`turn`) 
			VALUES (NULL, '$_cp_league', '$_cp_fid2', '$myshirt', '$mycname', '$mysname', '$mypos', '$mytype', '$myhand', '$mylevel', '$mybest', '$myexp', '$mypot', '$mytrade', '$myval','$_cp_turnid');";
			$player_id2=nz_pdo($_cp_sql41,$conn);
			//Update Transactions table	
			#$str="<p>Updated transactions table for player #$player_id</p>";
			#output($str); 
			//When would it not be setup from this process??
			#INSERT INTO `bb_transactions` (`id`, `league`, `franchise`, `player`, `season`, `week`, `source`) VALUES (NULL, 'MLB08', '10800', '9000000', '5', '1', '2000');
			#$_cp_sql42="INSERT INTO `bb_transactions` (`id`, `league`, `franchise`, `player`, `season`, `week`, `source`) VALUES (NULL, '$_cp_league', '$_cp_fid2', '$player_id2', '$_cp_season', '$_cp_week', '2000');";
			#nz_pdo($_cp_sql42,$conn);
		}
		
		
		}	

	$q++;
	}

$str="processed $q rows of roster data&nbsp;&nbsp;/&nbsp;&nbsp;";
output($str); 	
	 
	

	$k++;
	$m=$m+2;
	$n=$n+2; 	


	//end of main loop
	}


$count = $conn->query("SELECT count(1) FROM bb_playerstemp")->fetchColumn();
$str="</p><h3>$count entries in temp table ready to check!</h3>";
output($str); 	

$_cp_sql = "UPDATE `g_turnsummary` SET `processed`= 3 WHERE `turn_id`=$_cp_turnid";
myDB::query("$_cp_sql");


?>
