<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'g_functions.php';
require_once 'mydatabase.php';

$time_start = microtime(true);
$str="<br /><div class='nz-card'>";
$str.="<div class='w3-container $mycolour6'>";
$str.="<div class='w3-pale-green'>";
$str.="<h1>Gameplan Football - Update Pro Team Records</h1>";
$str.="</div>";
$str.="</header>";
output($str);
$str= "<div class='w3-panel $mycolour4 nz-card w3-round-xxlarge'>";
output($str);

//Text for middle box
$str="<h2>Started update process</h3>";
output($str);
//Reset all records
$str="<h2>Resetting all Pro records</h3>";
output($str);
$_cp_sql = "UPDATE `fp_franchises` SET `Winner`=0,`Runnerup`=0,`ChampionshipW`=0,`ChampionshipL`=0,`DivisionW`=0,`Wildcard`=0, `WinnerYears`='', `DivisionYears`='',ConferenceYears='',WildcardYears=''  WHERE `ftype`='Pro'";
$res = execute_db($_cp_sql, $conn);
//Reset Pro Games
$_cp_sql = "TRUNCATE fp_vgames";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

// Disable strict mode for the session
$conn->exec("SET SESSION sql_mode = '';");
//Fix invalid datetimes
$_cp_sql = "UPDATE `f_games`
			SET `creation_time` = NOW() 
			WHERE `creation_time` = '0000-00-00 00:00:00';";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$_cp_sql = "UPDATE `f_games`
			SET `modification_time` = NOW() 
			WHERE `creation_time` = '0000-00-00 00:00:00';";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
// Re-enable strict mode after the update
$conn->exec("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE';");

$_cp_sql = "INSERT INTO fp_vgames 
			SELECT `a`.`id_game` AS `id_game`,`a`.`league` AS `league`,`a`.`season` AS `season`,`a`.`week` AS `week`,`a`.`team` AS `team`,`a`.`franchise` AS `franchise`,`a`.`coach` AS `coach`,`a`.`qb` AS `qb`,`a`.`safe` AS `safe`,`a`.`q1` AS `q1`,`a`.`q2` AS `q2`,`a`.`q3` AS `q3`,`a`.`q4` AS `q4`,`a`.`ot` AS `ot`,`a`.`score` AS `score`,`a`.`fga` AS `fga`,`a`.`fgg` AS `fgg`,`a`.`epa` AS `epa`,`a`.`epg` AS `epg`,`a`.`cva` AS `cva`,`a`.`cvg` AS `cvg`,`a`.`punts` AS `punts`,`a`.`thirdcon` AS `thirdcon`,`a`.`thirddowns` AS `thirddowns`,`a`.`fourthcon` AS `fourthcon`,`a`.`fourthdowns` AS `fourthdowns`,`a`.`firstd` AS `firstd`,`a`.`passcmp` AS `passcmp`,`a`.`passatt` AS `passatt`,`a`.`passyds` AS `passyds`,`a`.`passlng` AS `passlng`,`a`.`passlngtd` AS `passlngtd`,`a`.`passtd` AS `passtd`,`a`.`passpct` AS `passpct`,`a`.`interception` AS `interception`,`a`.`hrd` AS `hrd`,`a`.`skd` AS `skd`,`a`.`rush` AS `rush`,`a`.`rushyds` AS `rushyds`,`a`.`rushlng` AS `rushlng`,`a`.`rushlngtd` AS `rushlngtd`,`a`.`rushtd` AS `rushtd`,`a`.`fum` AS `fum`,`a`.`qbatt` AS `qbatt`,`a`.`qbyds` AS `qbyds`,`a`.`kr` AS `kr`,`a`.`kryds` AS `kryds`,`a`.`krtd` AS `krtd`,`a`.`pr` AS `pr`,`a`.`pryds` AS `pryds`,`a`.`prtd` AS `prtd`,`a`.`form1` AS `form1`,`a`.`form2` AS `form2`,`a`.`run1` AS `run1`,`a`.`run2` AS `run2`,`a`.`pass1` AS `pass1`,`a`.`pass2` AS `pass2`,`a`.`def1` AS `def1`,`a`.`def2` AS `def2`,`a`.`homeaway` AS `homeaway`,`a`.`gametype` AS `gametype`,`a`.`opp_team` AS `opp_team`,`a`.`opp_franchise` AS `opp_franchise`,`a`.`opp_coach` AS `opp_coach`,`a`.`opp_qb` AS `opp_qb`,`a`.`opp_safe` AS `opp_safe`,`a`.`opp_q1` AS `opp_q1`,`a`.`opp_q2` AS `opp_q2`,`a`.`opp_q3` AS `opp_q3`,`a`.`opp_q4` AS `opp_q4`,`a`.`opp_ot` AS `opp_ot`,`a`.`opp_score` AS `opp_score`,`a`.`opp_fga` AS `opp_fga`,`a`.`opp_fgg` AS `opp_fgg`,`a`.`opp_epa` AS `opp_epa`,`a`.`opp_epg` AS `opp_epg`,`a`.`opp_cva` AS `opp_cva`,`a`.`opp_cvg` AS `opp_cvg`,`a`.`opp_punts` AS `opp_punts`,`a`.`opp_thirdcon` AS `opp_thirdcon`,`a`.`opp_thirddowns` AS `opp_thirddowns`,`a`.`opp_fourthcon` AS `opp_fourthcon`,`a`.`opp_fourthdowns` AS `opp_fourthdowns`,`a`.`opp_firstd` AS `opp_firstd`,`a`.`opp_passcmp` AS `opp_passcmp`,`a`.`opp_passatt` AS `opp_passatt`,`a`.`opp_passyds` AS `opp_passyds`,`a`.`opp_passlng` AS `opp_passlng`,`a`.`opp_passlngtd` AS `opp_passlngtd`,`a`.`opp_passtd` AS `opp_passtd`,`a`.`opp_passpct` AS `opp_passpct`,`a`.`opp_interception` AS `opp_interception`,`a`.`opp_hrd` AS `opp_hrd`,`a`.`opp_skd` AS `opp_skd`,`a`.`opp_rush` AS `opp_rush`,`a`.`opp_rushyds` AS `opp_rushyds`,`a`.`opp_rushlng` AS `opp_rushlng`,`a`.`opp_rushlngtd` AS `opp_rushlngtd`,`a`.`opp_rushtd` AS `opp_rushtd`,`a`.`opp_fum` AS `opp_fum`,`a`.`opp_qbatt` AS `opp_qbatt`,`a`.`opp_qbyds` AS `opp_qbyds`,`a`.`opp_kr` AS `opp_kr`,`a`.`opp_kryds` AS `opp_kryds`,`a`.`opp_krtd` AS `opp_krtd`,`a`.`opp_pr` AS `opp_pr`,`a`.`opp_pryds` AS `opp_pryds`,`a`.`opp_prtd` AS `opp_prtd`,`a`.`opp_form1` AS `opp_form1`,`a`.`opp_form2` AS `opp_form2`,`a`.`opp_run1` AS `opp_run1`,`a`.`opp_run2` AS `opp_run2`,`a`.`opp_pass1` AS `opp_pass1`,`a`.`opp_pass2` AS `opp_pass2`,`a`.`opp_def1` AS `opp_def1`,`a`.`opp_def2` AS `opp_def2`,`a`.`win` AS `win`,`a`.`lose` AS `lose`,`a`.`tie` AS `tie`,`a`.`creation_time` AS `creation_time`,`a`.`modification_time` AS `modification_time`,`a`.`modification_by` AS `modification_by`,`a`.`modification_from` AS `modification_from` 
			FROM `f_games` `a` 
			WHERE `a`.`league` like '%NFL%' and `a`.`week` < 21 and `a`.`score` <> 1 and `a`.`opp_score` <> 1 and `a`.`homeaway` = 1 
			ORDER BY `a`.`league`,`a`.`season` desc,`a`.`week`,`a`.`franchise`";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 


//Retrieve all Pro Leagues
$_cp_sql = "SELECT DISTINCT `league` 
FROM `fp_franchises` 
WHERE `ftype` = 'Pro'
ORDER BY `league`";
$res = execute_db($_cp_sql, $conn);
		$mycount=0;	
        while($row = fetch_row_db($res)){
				$_cp_myleague=$row[0];
				$_cp_sql2 = "SELECT DISTINCT `season`
				FROM `f_games` 
				WHERE `league`= '$_cp_myleague'
				ORDER BY  `season` DESC";
				
				$res2 = execute_db($_cp_sql2, $conn);
				$number_of_rows = number_format($res2->rowCount() ); 
				$str="<h2>Looping through $number_of_rows seasons</h3>";
				output($str);
				while($row2 = fetch_row_db($res2)){
					$_cp_myseason=$row2[0];
					if ($mycount % 10 == 0 AND $mycount>0 ) {
						$str='Processed ';
						$str.=$mycount;
						$str.=' Seasons / ';
						output($str);
					}
					$mycount++;	

				//Looping through Seasons	
				//Find Superbowl wins
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=1 AND `gametype`=36 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbwinner=$row3[0];
						$_cp_sql4 = "UPDATE `fp_franchises` SET Winner=Winner+1  WHERE `franchise`=$_cp_mysbwinner";
						$res4 = execute_db($_cp_sql4, $conn);
						#echo "<p>Superbowl winner for $_cp_myleague - $_cp_myseason is $row3[1]</p>";
						$_cp_sql5 = "UPDATE `fp_franchises`  SET WinnerYears = CONCAT(WinnerYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mysbwinner";
						$res5 = execute_db($_cp_sql5, $conn);	
					$_cp_sql15 = "UPDATE `fp_franchises`  SET ConferenceYears = CONCAT(ConferenceYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mysbwinner";
					$res15 = execute_db($_cp_sql15, $conn);			
					$_cp_sql6 = "UPDATE `fp_franchises` SET ChampionshipW=ChampionshipW+1  WHERE `franchise`=$_cp_mysbwinner";
					$res6 = execute_db($_cp_sql6, $conn);	

					}

				//Find Superbowl losses
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=0 AND `gametype`=36 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbloser=$row3[0];
						$_cp_sql4 = "UPDATE `fp_franchises` SET Runnerup=Runnerup+1  WHERE `franchise`=$_cp_mysbloser";
						$res4 = execute_db($_cp_sql4, $conn);
					$_cp_sql15 = "UPDATE `fp_franchises`  SET ConferenceYears = CONCAT(ConferenceYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mysbloser";
					$res15 = execute_db($_cp_sql15, $conn);									
					$_cp_sql6 = "UPDATE `fp_franchises` SET ChampionshipW=ChampionshipW+1  WHERE `franchise`=$_cp_mysbloser";
					$res6 = execute_db($_cp_sql6, $conn);	

					}

				//Find Championship Game wins
				$_cp_sql5 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=1 AND `gametype`=35 AND `week`=19 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res5 = execute_db($_cp_sql5, $conn);
					while($row5 = fetch_row_db($res5)){
						$_cp_mycgwinner=$row5[0];
					}

				//Find Championship Game losses
				$_cp_sql7 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=0 AND `gametype`=35 AND `week`=19 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res7 = execute_db($_cp_sql7, $conn);
					while($row7 = fetch_row_db($res7)){
						$_cp_mysbwinner=$row7[0];
						$_cp_sql8 = "UPDATE `fp_franchises` SET ChampionshipL=ChampionshipL+1  WHERE `franchise`=$_cp_mysbwinner";
						$res8 = execute_db($_cp_sql8, $conn);	
					}

				//Find Wildcard Appearances
				$_cp_sql9 = "SELECT `franchise`,`team` FROM `f_games` WHERE `gametype`=35 AND `week`=17 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res9 = execute_db($_cp_sql9, $conn);
					while($row9 = fetch_row_db($res9)){
						$_cp_mywildcard=$row9[0];
						$_cp_sql10 = "UPDATE `fp_franchises` SET Wildcard=Wildcard+1  WHERE `franchise`=$_cp_mywildcard";
						$res10 = execute_db($_cp_sql10, $conn);	
					$_cp_sql15 = "UPDATE `fp_franchises`  SET WildcardYears = CONCAT(WildcardYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mywildcard";
					$res15 = execute_db($_cp_sql15, $conn);	
	
					}
				

				//Find Division wins
				$_cp_sql3 = "	SELECT `franchise` FROM `f_games` WHERE `week` =18 AND `season` = $_cp_myseason AND `gametype`=35
								AND `franchise` NOT IN (SELECT `franchise` FROM `f_games` WHERE `gametype`=35 AND `week`=17 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason)";
				#echo "<p>$_cp_sql3</p>";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mydivwinner=$row3[0];
						$_cp_sql4 = "UPDATE `fp_franchises` SET DivisionW=DivisionW+1  WHERE `franchise`=$_cp_mydivwinner";
						$res4 = execute_db($_cp_sql4, $conn);
						$_cp_sql5 = "UPDATE `fp_franchises`  SET DivisionYears = CONCAT(DivisionYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mydivwinner";
						$res5 = execute_db($_cp_sql5, $conn);	
					}


					
				#Season loop ended	
				}  
        }  
$str='Processed ';
$str.=$mycount;
$str.=' Seasons.';
output($str);        
 
 
//Populate Bowls Table
$_cp_sql14 = "TRUNCATE fp_bowlgames";

$result14 = $conn->prepare($_cp_sql14); 
$result14->execute(); 

$_cp_sql15 = "INSERT INTO `fp_bowlgames` 
SELECT CONCAT(`a`.`league`,'-',`a`.`season`),`a`.`league` AS `league`,`a`.`season` AS `season`,`a`.`franchise` AS `franchise`,`b`.`team` AS `AFC_Champions`,`a`.`coach` AS `AFC_Coach`, 
concat(`a`.`score`,' - ',`a`.`opp_score`) AS `Score`,`a`.`opp_franchise` AS `opp_franchise`,`c`.`team` AS `NFC_Champions`,`a`.`opp_coach` AS `NFC_Coach`,`a`.`win` AS `win`, `a`.`score` AS `afc_score`,`a`.`opp_score`  AS `nfc_score`
from `f_games` `a` 
	inner join `fp_franchises` `b` on(`a`.`franchise` = `b`.`franchise`)
	inner join `fp_franchises` `c` on(`a`.`opp_franchise` = `c`.`franchise`)
where `a`.`week` = 20 and `a`.`gametype` = 36 and `a`.`homeaway` = 0 order by `a`.`season`";        
#echo "<p>$_cp_sql15</p>";
$result15 = $conn->prepare($_cp_sql15); 
$result15->execute(); 

$_cp_sql = "UPDATE `fp_bowlgames` SET `win`='AFC' WHERE `afc_score`>`nfc_score`";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

$_cp_sql = "UPDATE `fp_bowlgames` SET `win`='NFC' WHERE `afc_score`<`nfc_score`";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

$_cp_sql16 = "SELECT `season`,`AFC_Champions`,`NFC_Champions`,`win` FROM `fp_bowlgames` WHERE 1 ORDER BY `season` ASC";
$res16 = execute_db($_cp_sql16, $conn);
	while($row16 = fetch_row_db($res16)){
		$_cp_myseason=$row16[0];
		$_cp_myafc=$row16[1];
		$_cp_mynfc=$row16[2];
		$_cp_mywin=$row16[3];
		#echo "<p>$_cp_myseason - $_cp_myafc - $_cp_mynfc - $_cp_mywin</p>";
		if ("NFC"==$_cp_mywin){
			$_cp_sql17 = "UPDATE `fp_bowlgames` SET `NFC_Champions`  = CONCAT('<span style=\"font-weight: 900\">', `NFC_Champions`,'</span>') WHERE `season`=$_cp_myseason";
		} else {
			$_cp_sql17 = "UPDATE `fp_bowlgames` SET `AFC_Champions`  = CONCAT('<span style=\"font-weight: 900\">', `AFC_Champions`,'</span>') WHERE `season`=$_cp_myseason";
		}
		#echo "$_cp_sql17<br>";
		$res17 = execute_db($_cp_sql17, $conn);

}        


$str="</div>";
output($str);


require_once 'g_footer.php';
