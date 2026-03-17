<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'g_functions.php';
require_once 'mydatabase.php';

$time_start = microtime(true);
$str="<br /><div class='nz-card'>";
$str.="<div class='w3-container $mycolour6'>";
$str.="<div class='w3-pale-green'>";
$str.="<h1>Gameplan Football Update College records</h1>";
$str.="</div>";
#$str.="</header>";
output($str);
$str= "<div class='w3-panel $mycolour4 nz-card w3-round-xxlarge'>";
output($str);

$str="<h2>Started update process</h2>\n";
//Write mid section
output($str);

//Check if manual adjustment is required
$_cp_sql = "SELECT COUNT(`gametype`) 
FROM `f_games` 
WHERE `league`='NCAA5' AND `season`=2029 AND `week`=13 AND `gametype`=10;";
$res = execute_db($_cp_sql, $conn);
$mycount=$res->fetchColumn();

if ($mycount>2){
	$str.="<h2 class='w3-red'>Error - manual allocation of Bowl Games not completed!!</h2>\n";
	$str.="</div>\n";
	output($str);
} else {
//Bowl games sorted so proceed
$str="<div class='w3-container w3-teal'>\n";
$str.="<h2>Resetting records.</h3>\n";
output($str);
//Reset all records
$_cp_sql = "UPDATE `fc_franchises` 
SET `WinnerYears`='', `RunnerupYears`='',`ConferenceYears`='',`RoseYears`='',`CottonYears`='',`OrangeYears`='',`HawaiiYears`='',`MotorYears`='',`ConfWins`=0,`G_Winner`=0, `G_Runnerup`=0, `GC_Winner`=0, `GC_Runnerup`=0, `S_Winner`=0, `S_Runnerup`=0, `SC_Winner`=0, `SC_Runnerup`=0, `B_Winner`=0, `B_Runnerup`=0, `BC_Winner`=0, `BC_Runnerup`=0 WHERE 1";
$res = execute_db($_cp_sql, $conn);

//Reset College Games
$_cp_sql = "TRUNCATE fc_vgames";
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



$_cp_sql = "INSERT INTO fc_vgames
			SELECT `a`.`id_game` AS `id_game`, `a`.`league` AS `league`, `a`.`season` AS `season`, `a`.`week` AS `week`, `a`.`team` AS `team`, `a`.`franchise` AS `franchise`, `a`.`coach` AS `coach`, `a`.`qb` AS `qb`, `a`.`safe` AS `safe`, `a`.`q1` AS `q1`, `a`.`q2` AS `q2`, `a`.`q3` AS `q3`, `a`.`q4` AS `q4`, `a`.`ot` AS `ot`, `a`.`score` AS `score`, `a`.`fga` AS `fga`, `a`.`fgg` AS `fgg`, `a`.`epa` AS `epa`, `a`.`epg` AS `epg`, `a`.`cva` AS `cva`, `a`.`cvg` AS `cvg`, `a`.`punts` AS `punts`, `a`.`thirdcon` AS `thirdcon`, `a`.`thirddowns` AS `thirddowns`, `a`.`fourthcon` AS `fourthcon`, `a`.`fourthdowns` AS `fourthdowns`, `a`.`firstd` AS `firstd`, `a`.`passcmp` AS `passcmp`, `a`.`passatt` AS `passatt`, `a`.`passyds` AS `passyds`, `a`.`passlng` AS `passlng`, `a`.`passlngtd` AS `passlngtd`, `a`.`passtd` AS `passtd`, `a`.`passpct` AS `passpct`, `a`.`interception` AS `interception`, `a`.`hrd` AS `hrd`, `a`.`skd` AS `skd`, `a`.`rush` AS `rush`, `a`.`rushyds` AS `rushyds`, `a`.`rushlng` AS `rushlng`, `a`.`rushlngtd` AS `rushlngtd`, `a`.`rushtd` AS `rushtd`, `a`.`fum` AS `fum`, `a`.`qbatt` AS `qbatt`, `a`.`qbyds` AS `qbyds`, `a`.`kr` AS `kr`, `a`.`kryds` AS `kryds`, `a`.`krtd` AS `krtd`, `a`.`pr` AS `pr`, `a`.`pryds` AS `pryds`, `a`.`prtd` AS `prtd`, `a`.`form1` AS `form1`, `a`.`form2` AS `form2`, `a`.`run1` AS `run1`, `a`.`run2` AS `run2`, `a`.`pass1` AS `pass1`, `a`.`pass2` AS `pass2`, `a`.`def1` AS `def1`, `a`.`def2` AS `def2`, `a`.`homeaway` AS `homeaway`, `a`.`gametype` AS `gametype`, `a`.`opp_team` AS `opp_team`, `a`.`opp_franchise` AS `opp_franchise`, `a`.`opp_coach` AS `opp_coach`, `a`.`opp_qb` AS `opp_qb`, `a`.`opp_safe` AS `opp_safe`, `a`.`opp_q1` AS `opp_q1`, `a`.`opp_q2` AS `opp_q2`, `a`.`opp_q3` AS `opp_q3`, `a`.`opp_q4` AS `opp_q4`, `a`.`opp_ot` AS `opp_ot`, `a`.`opp_score` AS `opp_score`, `a`.`opp_fga` AS `opp_fga`, `a`.`opp_fgg` AS `opp_fgg`, `a`.`opp_epa` AS `opp_epa`, `a`.`opp_epg` AS `opp_epg`, `a`.`opp_cva` AS `opp_cva`, `a`.`opp_cvg` AS `opp_cvg`, `a`.`opp_punts` AS `opp_punts`, `a`.`opp_thirdcon` AS `opp_thirdcon`, `a`.`opp_thirddowns` AS `opp_thirddowns`, `a`.`opp_fourthcon` AS `opp_fourthcon`, `a`.`opp_fourthdowns` AS `opp_fourthdowns`, `a`.`opp_firstd` AS `opp_firstd`, `a`.`opp_passcmp` AS `opp_passcmp`, `a`.`opp_passatt` AS `opp_passatt`, `a`.`opp_passyds` AS `opp_passyds`, `a`.`opp_passlng` AS `opp_passlng`, `a`.`opp_passlngtd` AS `opp_passlngtd`, `a`.`opp_passtd` AS `opp_passtd`, `a`.`opp_passpct` AS `opp_passpct`, `a`.`opp_interception` AS `opp_interception`, `a`.`opp_hrd` AS `opp_hrd`, `a`.`opp_skd` AS `opp_skd`, `a`.`opp_rush` AS `opp_rush`, `a`.`opp_rushyds` AS `opp_rushyds`, `a`.`opp_rushlng` AS `opp_rushlng`, `a`.`opp_rushlngtd` AS `opp_rushlngtd`, `a`.`opp_rushtd` AS `opp_rushtd`, `a`.`opp_fum` AS `opp_fum`, `a`.`opp_qbatt` AS `opp_qbatt`, `a`.`opp_qbyds` AS `opp_qbyds`, `a`.`opp_kr` AS `opp_kr`, `a`.`opp_kryds` AS `opp_kryds`, `a`.`opp_krtd` AS `opp_krtd`, `a`.`opp_pr` AS `opp_pr`, `a`.`opp_pryds` AS `opp_pryds`, `a`.`opp_prtd` AS `opp_prtd`, `a`.`opp_form1` AS `opp_form1`, `a`.`opp_form2` AS `opp_form2`, `a`.`opp_run1` AS `opp_run1`, `a`.`opp_run2` AS `opp_run2`, `a`.`opp_pass1` AS `opp_pass1`, `a`.`opp_pass2` AS `opp_pass2`, `a`.`opp_def1` AS `opp_def1`, `a`.`opp_def2` AS `opp_def2`, `a`.`win` AS `win`, `a`.`lose` AS `lose`, `a`.`tie` AS `tie`, `a`.`creation_time` AS `creation_time`, `a`.`modification_time` AS `modification_time`, `a`.`modification_by` AS `modification_by`, `a`.`modification_from` AS `modification_from` 
			FROM `f_games` AS `a` 
			WHERE `a`.`league` like '%NCAA%' AND `a`.`week` < 14 AND `a`.`score` <> 1 AND `a`.`opp_score` <> 1 AND `homeaway`=1
			ORDER BY `a`.`league` ASC, `a`.`season` DESC, `a`.`week` ASC, `a`.`franchise` ASC ;";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

$str="<h2>Processing Bowl wins and losses.</h3>\n";	
output($str);
//Retireve all College Leagues
$_cp_sql = "SELECT DISTINCT `league` 
FROM `fc_franchises` 
ORDER BY `league`";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
				$_cp_myleague=$row[0];
				$_cp_sql2 = "SELECT DISTINCT `season`
				FROM `f_games` 
				WHERE `league`= '$_cp_myleague'
				ORDER BY  `season` DESC";
				$res2 = execute_db($_cp_sql2, $conn);
				while($row2 = fetch_row_db($res2)){
					$_cp_myseason=$row2[0];
				
				//Looping through Seasons	
				//Find Gold Bowl wins
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=1 AND `gametype`=8 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbwinner=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET G_Winner=G_Winner+1  WHERE `franchise`=$_cp_mysbwinner";
						$res4 = execute_db($_cp_sql4, $conn);
						#echo "<p>Gold Bowl winner for $_cp_myleague - $_cp_myseason is $row3[1]</p>";
						$_cp_sql5 = "UPDATE `fc_franchises`  SET WinnerYears = CONCAT(WinnerYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mysbwinner";
						$res5 = execute_db($_cp_sql5, $conn);	
					}

				//Find Gold Bowl losses
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=0 AND `gametype`=8 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbloser=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET G_Runnerup=G_Runnerup+1  WHERE `franchise`=$_cp_mysbloser";
						$res4 = execute_db($_cp_sql4, $conn);
						$_cp_sql5 = "UPDATE `fc_franchises`  SET RunnerupYears = CONCAT(RunnerupYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mysbloser";
						$res5 = execute_db($_cp_sql5, $conn);	
					}

				//Find Rose Bowl wins
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=1 AND `gametype`=9 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbwinner=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET GC_Winner=GC_Winner+1  WHERE `franchise`=$_cp_mysbwinner";
						$res4 = execute_db($_cp_sql4, $conn);
						#echo "<p>Gold Bowl winner for $_cp_myleague - $_cp_myseason is $row3[1]</p>";
						$_cp_sql5 = "UPDATE `fc_franchises`  SET RoseYears = CONCAT(RoseYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mysbwinner";
						$res5 = execute_db($_cp_sql5, $conn);	
					}

				//Find Rose Bowl losses
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=0 AND `gametype`=9 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbloser=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET GC_Runnerup=GC_Runnerup+1  WHERE `franchise`=$_cp_mysbloser";
						$res4 = execute_db($_cp_sql4, $conn);
					}

				//Find Cotton Bowl wins
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=1 AND `gametype`=10 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbwinner=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET S_Winner=S_Winner+1  WHERE `franchise`=$_cp_mysbwinner";
						$res4 = execute_db($_cp_sql4, $conn);
						#echo "<p>Gold Bowl winner for $_cp_myleague - $_cp_myseason is $row3[1]</p>";
						$_cp_sql5 = "UPDATE `fc_franchises`  SET CottonYears = CONCAT(CottonYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mysbwinner";
						$res5 = execute_db($_cp_sql5, $conn);	
					}

				//Find Rose Bowl losses
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=0 AND `gametype`=10 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbloser=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET S_Runnerup=S_Runnerup+1  WHERE `franchise`=$_cp_mysbloser";
						$res4 = execute_db($_cp_sql4, $conn);
					}
					
				//Find Orange Bowl wins
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=1 AND `gametype`=11 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbwinner=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET SC_Winner=SC_Winner+1  WHERE `franchise`=$_cp_mysbwinner";
						$res4 = execute_db($_cp_sql4, $conn);
						#echo "<p>Gold Bowl winner for $_cp_myleague - $_cp_myseason is $row3[1]</p>";
						$_cp_sql5 = "UPDATE `fc_franchises`  SET OrangeYears = CONCAT(OrangeYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mysbwinner";
						$res5 = execute_db($_cp_sql5, $conn);	
					}

				//Find Orange Bowl losses
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=0 AND `gametype`=11 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbloser=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET SC_Runnerup=SC_Runnerup+1  WHERE `franchise`=$_cp_mysbloser";
						$res4 = execute_db($_cp_sql4, $conn);
					}

				//Find Hawaii Bowl wins
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=1 AND `gametype`=12 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbwinner=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET B_Winner=B_Winner+1  WHERE `franchise`=$_cp_mysbwinner";
						$res4 = execute_db($_cp_sql4, $conn);
						#echo "<p>Gold Bowl winner for $_cp_myleague - $_cp_myseason is $row3[1]</p>";
						$_cp_sql5 = "UPDATE `fc_franchises`  SET HawaiiYears = CONCAT(HawaiiYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mysbwinner";
						$res5 = execute_db($_cp_sql5, $conn);	
					}

				//Find Hawaii Bowl losses
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=0 AND `gametype`=12 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbloser=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET B_Runnerup=B_Runnerup+1  WHERE `franchise`=$_cp_mysbloser";
						$res4 = execute_db($_cp_sql4, $conn);
					}

				//Find MC Bowl wins
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=1 AND `gametype`=13 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbwinner=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET BC_Winner=BC_Winner+1  WHERE `franchise`=$_cp_mysbwinner";
						$res4 = execute_db($_cp_sql4, $conn);
						#echo "<p>Gold Bowl winner for $_cp_myleague - $_cp_myseason is $row3[1]</p>";
						$_cp_sql5 = "UPDATE `fc_franchises`  SET MotorYears = CONCAT(MotorYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mysbwinner";
						$res5 = execute_db($_cp_sql5, $conn);	
					}

				//Find MC Bowl losses
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=0 AND `gametype`=13 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbloser=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET BC_Runnerup=BC_Runnerup+1  WHERE `franchise`=$_cp_mysbloser";
						$res4 = execute_db($_cp_sql4, $conn);
					}					
		
				
				#Season loop ended	
				}  

        }  

		                
//Populate NC Table
$_cp_sql = "TRUNCATE fc_ncgames";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

$_cp_sql = "INSERT INTO `fc_ncgames` 
SELECT CONCAT(a.`league`, '-',a.`season`) as `id`, a.`league`, a.`season`, a.`week`, CONCAT('<span style=\"font-weight: 900\">', b.`team`,'</span>') AS `Winners`, b.franchise ,b.`Conference` AS `WinConf`, a.`coach` ,a.`score`, a.`opp_score`,c.`team` AS `RunnersUp`, c.franchise, c.`Conference` AS `RUConf`,a.`opp_coach`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise`=b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise`=c.`franchise`
WHERE `gametype`=8 AND `win`=1 ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
//End of NC Table


//Populate Table
$_cp_sql = "TRUNCATE fc_rosegames";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

$_cp_sql = "INSERT INTO `fc_rosegames` 
SELECT CONCAT(a.`league`, '-',a.`season`) as `id`, a.`league`, a.`season`, a.`week`, CONCAT('<span style=\"font-weight: 900\">', b.`team`,'</span>') AS `Winners`,  a.`coach`  AS `Coach`,b.franchise ,b.`Conference` AS `Conference`,a.`score` AS `Score`, a.`opp_score` AS `oScore`,c.`team` AS `RunnersUp`,a.`opp_coach` as `OCoach`, c.franchise as `oppfranchise`, c.`Conference` AS `rConference`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise`=b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise`=c.`franchise`
WHERE `gametype`=9 AND `win`=1 ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
//End of Table

//Populate Table
$_cp_sql = "TRUNCATE fc_cottongames";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

$_cp_sql = "INSERT INTO `fc_cottongames` 
SELECT CONCAT(a.`league`, '-',a.`season`) as `id`, a.`league`, a.`season`, a.`week`, CONCAT('<span style=\"font-weight: 900\">', b.`team`,'</span>') AS `Winners`,  a.`coach`  AS `Coach`,b.franchise ,b.`Conference` AS `Conference`,a.`score` AS `Score`, a.`opp_score` AS `oScore`,c.`team` AS `RunnersUp`,a.`opp_coach` as `OCoach`, c.franchise as `oppfranchise`, c.`Conference` AS `rConference`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise`=b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise`=c.`franchise`
WHERE `gametype`=10 AND `win`=1";
#echo "$_cp_sql";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
//End of Table

//Populate Table
$_cp_sql = "TRUNCATE fc_orangegames";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

$_cp_sql = "INSERT INTO `fc_orangegames` 
SELECT CONCAT(a.`league`, '-',a.`season`) as `id`, a.`league`, a.`season`, a.`week`, CONCAT('<span style=\"font-weight: 900\">', b.`team`,'</span>') AS `Winners`,  a.`coach`  AS `Coach`,b.franchise ,b.`Conference` AS `Conference`,a.`score` AS `Score`, a.`opp_score` AS `oScore`,c.`team` AS `RunnersUp`,a.`opp_coach` as `OCoach`, c.franchise as `oppfranchise`, c.`Conference` AS `rConference`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise`=b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise`=c.`franchise`
WHERE `gametype`=11 AND `win`=1";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
//End of Table

//Populate Table
$_cp_sql = "TRUNCATE fc_hawaiigames";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

$_cp_sql = "INSERT INTO `fc_hawaiigames` 
SELECT CONCAT(a.`league`, '-',a.`season`) as `id`, a.`league`, a.`season`, a.`week`, CONCAT('<span style=\"font-weight: 900\">', b.`team`,'</span>') AS `Winners`,  a.`coach`  AS `Coach`,b.franchise ,b.`Conference` AS `Conference`,a.`score` AS `Score`, a.`opp_score` AS `oScore`,c.`team` AS `RunnersUp`,a.`opp_coach` as `OCoach`, c.franchise as `oppfranchise`, c.`Conference` AS `rConference`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise`=b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise`=c.`franchise`
WHERE `gametype`=12 AND `win`=1";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
//End of Table

//Populate Table
$_cp_sql = "TRUNCATE fc_motorgames";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

$_cp_sql = "INSERT INTO `fc_motorgames` 
SELECT CONCAT(a.`league`, '-',a.`season`) as `id`, a.`league`, a.`season`, a.`week`, CONCAT('<span style=\"font-weight: 900\">', b.`team`,'</span>') AS `Winners`,  a.`coach`  AS `Coach`,b.franchise ,b.`Conference` AS `Conference`,a.`score` AS `Score`, a.`opp_score` AS `oScore`,c.`team` AS `RunnersUp`,a.`opp_coach` as `OCoach`, c.franchise as `oppfranchise`, c.`Conference` AS `rConference`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise`=b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise`=c.`franchise`
WHERE `gametype`=13 AND `win`=1";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
//End of Table

$str="<h2>Processing Coaches.</h3>\n";
output($str);

$totgwins=0;
$totglosses=0;
$totbwins=0;
$totblosses=0;
$totrswins=0;
$totrslosses=0;

$_cp_sql = "TRUNCATE fc_coaches";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 


$_cp_sql = "SELECT DISTINCT `league`, `coach` 
FROM `f_games` 
WHERE `league` LIKE 'NC%'
ORDER BY `league` ASC, `coach` ASC";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$i=1;
        while($row = fetch_row_db($result)){
				$_cp_myleague=$row[0];
				$_cp_mycoach=$row[1];
				
				#If active coach show current team and asterik
				$_cp_sql2 = "SELECT IFNULL((SELECT `team` FROM `fc_franchises` WHERE `coach`='$_cp_mycoach'), 'Retired') ";
				#echo "<p>$_cp_sql2</p>";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				while($row2 = fetch_row_db($result2)){
						#echo "<p>$row2[0]</p>";
						if ('Retired'==$row2[0]) {
							$_cp_myretirement=1;
							} else {
							$_cp_myretirement=0;
							$_cp_mycurrentteam=$row2[0];
							}

						}		
 								
				#If retired coach show old team(s)
				if (1==$_cp_myretirement){
					#echo "<p>Looking for old teams</p>";
					$_cp_myoldteams="";
					$_cp_sql3 = "SELECT DISTINCT b.`team` 
								 FROM `f_games` a
									INNER JOIN  `fc_franchises` b ON a.`franchise`=b.`franchise`  
								 WHERE a.`league` ='$_cp_myleague' AND a.`coach`='$_cp_mycoach' 
								 ORDER BY b.`team` ASC";
								#echo "<p>$_cp_sql3</p>";
								$result3 = $conn->prepare($_cp_sql3); 
								$result3->execute(); 
								while($row3 = fetch_row_db($result3)){
									$_cp_myoldteams.=$row3[0];	
									$_cp_myoldteams.=", ";
								}						

				} else {
					$_cp_myoldteams="";
				}
				$_cp_myoldteams=substr($_cp_myoldteams, 0, -2);
				
				#Determine wins and losses in Gold Bowl Playoffs
				$_cp_sql4 = "	SELECT SUM(a.`win`),SUM(a.`lose`)
								FROM `f_games` a
								WHERE a.`league` ='$_cp_myleague' AND a.`coach`='$_cp_mycoach'  AND (a.`gametype` =8 OR a.`gametype`=14)";
				#echo "<p>$_cp_sql4</p>";
				$result4 = $conn->prepare($_cp_sql4); 
				$result4->execute(); 
				while($row4 = fetch_row_db($result4)){
					$_cp_mywins=$row4[0];	
					$_cp_mylosses=$row4[1];		
				}
				
				if(is_null($_cp_mywins)){
					$_cp_mywins=0;
				} 					
				if(is_null($_cp_mylosses)){
					$_cp_mylosses=0;
				}				
				
				
				#Determine wins and losses in all Bowl Playoffs
				$_cp_sql4 = "	SELECT SUM(a.`win`),SUM(a.`lose`)
								FROM `f_games` a
								WHERE a.`league` ='$_cp_myleague' AND a.`coach`='$_cp_mycoach'  AND a.`gametype` >7 AND a.`gametype`<17 ";
				#echo "<p>$_cp_sql4</p>";
				$result4 = $conn->prepare($_cp_sql4); 
				$result4->execute(); 
				while($row4 = fetch_row_db($result4)){
					$_cp_mybwins=$row4[0];	
					$_cp_myblosses=$row4[1];		
				}
				
				if(is_null($_cp_mybwins)){
					$_cp_mybwins=0;
				} 					
				if(is_null($_cp_myblosses)){
					$_cp_myblosses=0;
				}					
				
				#Determine wins and losses in Regular Season
				$_cp_sql4 = "	SELECT SUM(a.`win`),SUM(a.`lose`), SUM(a.`tie`)
								FROM `f_games` a
								WHERE a.`league` ='$_cp_myleague' AND a.`coach`='$_cp_mycoach'  AND a.`gametype` =1  ";
				#echo "<p>$_cp_sql4</p>";
				$result4 = $conn->prepare($_cp_sql4); 
				$result4->execute(); 
				while($row4 = fetch_row_db($result4)){
					$_cp_myrswins=$row4[0];	
					$_cp_myrslosses=$row4[1];
					$_cp_myrsties=$row4[2];		
				}
				
				if(is_null($_cp_myrswins)){
					$_cp_myrswins=0;
				} 					
				if(is_null($_cp_myrslosses)){
					$_cp_myrslosses=0;
				}					
				if(is_null($_cp_myrsties)){
					$_cp_myrsties=0;
				}													
				
				#Determine National Championships
				$_cp_sql2 = "SELECT IFNULL((SELECT SUM(`win`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `gametype`=8 AND `coach`='$_cp_mycoach'), 0) AS 'NCWins'";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				while($row2 = fetch_row_db($result2)){
						$_cp_mync=$row2[0];
					}

				#Determine National Championship Runners Up
				$_cp_sql2 = "SELECT IFNULL((SELECT SUM(`lose`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `gametype`=8 AND `coach`='$_cp_mycoach'), 0) AS 'NCLosses'";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				while($row2 = fetch_row_db($result2)){
						$_cp_myru=$row2[0];
					}


				#Determine Rose Bowls
				$_cp_sql2 = "SELECT IFNULL((SELECT SUM(`win`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `gametype`=9 AND `coach`='$_cp_mycoach'), 0) AS 'RBWins'";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				while($row2 = fetch_row_db($result2)){
						$_cp_myrb=$row2[0];
					}				

				#Determine Cotton Bowls
				$_cp_sql2 = "SELECT IFNULL((SELECT SUM(`win`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `gametype`=10 AND `coach`='$_cp_mycoach'), 0) AS 'RBWins'";
				#echo "<p>$_cp_sql2</p>";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				while($row2 = fetch_row_db($result2)){
						$_cp_mycb=$row2[0];
					}	

				#Determine Orange Bowls
				$_cp_sql2 = "SELECT IFNULL((SELECT SUM(`win`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `gametype`=11 AND `coach`='$_cp_mycoach'), 0) AS 'RBWins'";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				while($row2 = fetch_row_db($result2)){
						$_cp_myob=$row2[0];
					}	

				#Determine Hawaii Bowls
				$_cp_sql2 = "SELECT IFNULL((SELECT SUM(`win`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `gametype`=12 AND `coach`='$_cp_mycoach'), 0) AS 'RBWins'";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				while($row2 = fetch_row_db($result2)){
						$_cp_myhb=$row2[0];
					}	

				#Determine Motor Bowls
				$_cp_sql2 = "SELECT IFNULL((SELECT SUM(`win`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `gametype`=13 AND `coach`='$_cp_mycoach'), 0) AS 'RBWins'";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				while($row2 = fetch_row_db($result2)){
						$_cp_mymb=$row2[0];
					}	
				
				#Write data to table
				if (1==$_cp_myretirement){
					#echo "<p>$i - $_cp_myleague - $_cp_mycoach$_cp_myretirement ($_cp_myoldteams). NC Record $_cp_mywins-$_cp_mylosses , Bowl Record $_cp_mybwins-$_cp_myblosses, Regular Season Record $_cp_myrswins-$_cp_myrslosses (t$_cp_myrsties)  </p>";
						$_cp_sql4 = "INSERT INTO `fc_coaches` (`id`, `league`, `coach`, `team`, `retired`, `Winner`, `Runnerup`, `RoseW`, `CottonW`, `OrangeW`, `HawaiiW`, `MotorW`, `ncwins`, `nclosses`, `bwins`, `blosses`, `rswins`, `rslosses`, `rsties`, `Link`) 
									VALUES ('$_cp_myleague-$_cp_mycoach','$_cp_myleague', '$_cp_mycoach', '$_cp_myoldteams', 'Y', $_cp_mync, $_cp_myru, $_cp_myrb, $_cp_mycb, $_cp_myob, $_cp_myhb, $_cp_mymb, '$_cp_mywins', '$_cp_mylosses', '$_cp_mybwins', '$_cp_myblosses', '$_cp_myrswins', '$_cp_myrslosses', $_cp_myrsties, NULL)";
				} else {
					#echo "<p>$i - $_cp_myleague - $_cp_mycoach. NC Record $_cp_mywins-$_cp_mylosses, Bowl Record $_cp_mybwins-$_cp_myblosses, , Regular Season Record $_cp_myrswins-$_cp_myrslosses   (t$_cp_myrsties)  </p>";	
								$_cp_sql4 = "INSERT INTO `fc_coaches` (`id`,`league`, `coach`, `team`, `retired`, `Winner`, `Runnerup`, `RoseW`, `CottonW`, `OrangeW`, `HawaiiW`, `MotorW`, `ncwins`, `nclosses`, `bwins`, `blosses`, `rswins`, `rslosses`, `rsties`, `Link`) 
								VALUES ('$_cp_myleague-$_cp_mycoach','$_cp_myleague', '$_cp_mycoach', '$_cp_mycurrentteam', '', $_cp_mync, $_cp_myru, $_cp_myrb, $_cp_mycb, $_cp_myob, $_cp_myhb, $_cp_mymb, '$_cp_mywins', '$_cp_mylosses', '$_cp_mybwins', '$_cp_myblosses', '$_cp_myrswins', '$_cp_myrslosses', $_cp_myrsties, NULL)";
				}
				#echo "<p>$_cp_sql4</p>";
				$result4 = $conn->prepare($_cp_sql4); 
				$result4->execute(); 

				
				$totgwins=$totgwins+$_cp_mywins;
				$totglosses=$totglosses+$_cp_mylosses;
				$totbwins=$totbwins+$_cp_mybwins;
				$totblosses=$totblosses+$_cp_myblosses;
				$totrswins=$totrswins+$_cp_myrswins;
				$totrslosses=$totrslosses+$_cp_myrslosses;
				

				$i++;
				
				#echo "<h2>Finished Coaches</h3>";

}


$totgwins=number_format($totgwins);
$totglosses=number_format($totglosses);
$totbwins=number_format($totbwins);
$totblosses=number_format($totblosses);
$totrswins=number_format($totrswins);
$totrslosses=number_format($totrslosses);


$str="<h2>National Championship Games (including playoffs): $totgwins - $totglosses</h3>\n";
$str.="<h2>All Bowls (including playoffs): $totbwins - $totblosses</h3>\n";
$str.="<h2>Regular Season: $totrswins - $totrslosses</h3>\n";
$str.="</div>\n";
$str.="<br />";
$str.="</div>\n";
output($str);
}



require_once 'g_footer.php';

?>
