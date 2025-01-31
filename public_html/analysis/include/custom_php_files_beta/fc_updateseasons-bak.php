<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');

$time_start = microtime(true);

echo "<h1>Gameplan Football Update College Seasons</h1>";

//Reset all records, this won't be needed once all done then update will only look for unprocessed seasons
$_cp_sql = "TRUNCATE `fc_seasons`";
$res = execute_db($_cp_sql, $conn);
$_cp_sql = "TRUNCATE `fc_cicgames`";
$res = execute_db($_cp_sql, $conn);
$_cp_sql = "TRUNCATE `fc_confgames`";
$res = execute_db($_cp_sql, $conn);
$_cp_sql = "TRUNCATE `fc_rivalrygames`";
$res = execute_db($_cp_sql, $conn);
$_cp_sql = "UPDATE `fc_franchises` 
SET PerfectYears='',Perfect=0, ConferenceYears='',ConfWins=0,CicYears='',CicWins=0,RivalryYears='',RivalryWins=0
WHERE 1";
$res = execute_db($_cp_sql, $conn);


//Retireve all College Leagues
$_cp_sql = "SELECT DISTINCT `league` 
FROM `fc_franchises` 
WHERE `ftype` = 'College'
ORDER BY `league`";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
				$_cp_myleague=$row[0];
				//Retrieve Seasons for that League
				$_cp_sql2 = "SELECT DISTINCT `season`
				FROM `f_games` 
				WHERE `league`= '$_cp_myleague'
				ORDER BY  `season` DESC";
				$res2 = execute_db($_cp_sql2, $conn);
				while($row2 = fetch_row_db($res2)){
					$_cp_myseason=$row2[0];
				
				//Looping through Seasons	
				//Find Season Records
				$_cp_sql3 = "SELECT `franchise`,sum(`win`),sum(`lose`),sum(`tie`),sum(`score`), sum(`opp_score`),`coach`
								FROM `f_games` 
								WHERE league='$_cp_myleague' AND season=$_cp_myseason AND `gametype`=1 
								GROUP BY `franchise`
								ORDER BY `franchise`";
				$res3 = execute_db($_cp_sql3, $conn);
				#echo "<p>$_cp_sql3</p>";
					while($row3 = fetch_row_db($res3)){
						$_cp_sql4 = "
						INSERT INTO `fc_seasons` (`id`, `franchise`, `season`, `coach`, `won`, `lost`, `tie`, `scored`, `conceded`) 
						VALUES (NULL, '$row3[0]', '$_cp_myseason', '$row3[6]', '$row3[1]', '$row3[2]', '$row3[3]', '$row3[4]', '$row3[5]');	
						";
						$res4 = execute_db($_cp_sql4, $conn);

					}


					
				#Season loop ended	
				}  
        }  



//Retrieve all College Leagues
$_cp_sql = "SELECT DISTINCT `franchise` 
FROM `fc_franchises` 
WHERE `ftype` = 'College'
ORDER BY `league`, `franchise` ";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$_cp_myfranchise=$row[0];
			#Loop through all teams and extract details
			$_cp_sql2 = "SELECT MAX(`won`), MAX(`lost`), MAX(`scored`), MAX(`conceded`)
					FROM `fc_seasons` 
					WHERE `franchise`=$_cp_myfranchise";
			$res2 = execute_db($_cp_sql2, $conn);
				while($row2 = fetch_row_db($res2)){
				$_cp_sql3 = "UPDATE `fc_franchises` SET `MaxWins`=$row2[0], `MaxLosses`=$row2[1], `MaxScored`=$row2[2], `MaxConceded`=$row2[3]
							WHERE `franchise`=$_cp_myfranchise";
				$res3 = execute_db($_cp_sql3, $conn);				
				}
}

//Looking for Perfect Seasons
$_cp_sql = "SELECT DISTINCT `franchise`,`season` 
FROM `fc_seasons` 
ORDER BY `season` DESC, `franchise` ";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			#echo "<p>$row[0] - $row[1]</p>";
			$_cp_myfranchise=$row[0];
			$_cp_myseason=$row[1];
			$_cp_sql2 = "SELECT `season` FROM `fc_seasons` WHERE `season`=$_cp_myseason AND `franchise` = $_cp_myfranchise AND `won`=11";
			#echo "<p>$_cp_sql2</p>";
			$res2 = execute_db($_cp_sql2, $conn);
				while($row2 = fetch_row_db($res2)){
				$_cp_myperfectseason=$row2[0];
				#echo "<p>$_cp_myfranchise - $_cp_myseason - $_cp_myperfectseason</p>";
				//NZ 20200618 This is supposed to add emphasis if perfect regular season ended with NC win but bugged
				//NZToFix
				$_cp_sql3 = "SELECT 'Yes' FROM `fc_franchises` WHERE `franchise` = 5008 AND  `WinnerYears` LIKE '%2022%'";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_myperfecto=$row3[0];
					}
				
				if ('Yes'==$_cp_myperfecto){
					#echo "<p>$_cp_myfranchise - $_cp_myperfectseason - $_cp_myperfecto</p>";
					$_cp_sql4 = "UPDATE `fc_franchises` SET `PerfectYears`=CONCAT (`PerfectYears`,' <span style=\"font-weight: 500\">$_cp_myperfectseason</span>') WHERE `franchise`=$_cp_myfranchise";
					$_cp_sql4 = "UPDATE `fc_franchises` SET `PerfectYears`=CONCAT (`PerfectYears`,' <em>$_cp_myperfectseason</em>') WHERE `franchise`=$_cp_myfranchise";
				} else {
					$_cp_sql4 = "UPDATE `fc_franchises` SET `PerfectYears`=CONCAT (`PerfectYears`,'$_cp_myperfectseason') WHERE `franchise`=$_cp_myfranchise";
				}
				$res4 = execute_db($_cp_sql4, $conn);
				$_cp_sql5 = "UPDATE `fc_franchises` SET `Perfect`=`Perfect` +1 WHERE `franchise`=$_cp_myfranchise";
				$res5 = execute_db($_cp_sql5, $conn);
				
				}
		}		






//College Conferences
$_cp_sql = "INSERT INTO `fc_confgames`
			SELECT CONCAT (a.`league`,'-',a.`season`,'-',a.`franchise`) AS `id`,a.`league`,a.`season`,a.`franchise`, b.`conference` as `myconf`,
			sum(a.`win`) AS `wins`, SUM(a.`lose`) AS `losses`, SUM(a.`tie`) as `ties` , SUM(a.`score`) as `pfor`, SUM(a.`opp_score`) as `pagn`,
			sum(a.`win`)*10 +  SUM(a.`tie`)*5, SUM(a.`score`)-SUM(a.`opp_score`)
			FROM `f_games` a
				INNER JOIN `fc_franchises` b ON a.`franchise` = b.`franchise`
				INNER JOIN `fc_franchises` c ON a.`opp_franchise` = c.`franchise`    
			WHERE `week` > 0 AND `week` <12  AND  b.`conference` =  c.`conference`
			GROUP BY a.`league`,a.`season`,a.`franchise`
			ORDER BY a.`league`,a.`season`,a.`franchise`";
$res = execute_db($_cp_sql, $conn);


//Now work out the winners
$_cp_sql = "SELECT DISTINCT `league`,`season`,`myconf` 
FROM `fc_confgames` 
WHERE 1
ORDER BY `league`,`season` DESC,`myconf`  ";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$_cp_myleague=$row[0];
			$_cp_myseason=$row[1];
			$_cp_myconf=$row[2];
				//Check regular season is over before assigning Conference title
				$_cp_sql4 = "SELECT MAX(`week`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res4 = execute_db($_cp_sql4, $conn);			
				while($row4 = fetch_row_db($res4)){
				if ($row4[0]>10) {
					#echo "<p>$_cp_myleague $_cp_myseason $_cp_myconf</p>";
					$_cp_sql2 = "SELECT `franchise` 
							FROM `fc_confgames`  
							WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason AND myconf='$_cp_myconf'
							ORDER BY `pts` DESC, `diff` DESC, `wins` DESC, `franchise` DESC
							LIMIT 1";
					$res2 = execute_db($_cp_sql2, $conn);			
					while($row2 = fetch_row_db($res2)){
						$_cp_sql3 = "UPDATE `fc_franchises` 
								SET `ConfWins`=`ConfWins`+1, `ConferenceYears`=CONCAT(`ConferenceYears`,'$_cp_myseason ')
								WHERE `franchise`=$row2[0]";
						$res3 = execute_db($_cp_sql3, $conn);			
						}		
				}
			}			 
}


//Update Team and Coach Names
$_cp_sql = "SELECT DISTINCT `league` 
FROM `fc_franchises` 
WHERE `ftype` = 'College'
ORDER BY `league`";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
				$_cp_myleague=$row[0];
				$_cp_sql2 = "SELECT MAX(`season`)
				FROM `f_games` 
				WHERE `league`= '$_cp_myleague'
				ORDER BY  `season`";
				$res2 = execute_db($_cp_sql2, $conn);
				while($row2 = fetch_row_db($res2)){
					$_cp_myseason=$row2[0];
					$_cp_sql3 = "SELECT MAX(`week`)
						FROM `f_games` 
						WHERE `league`= '$_cp_myleague' and `season`= '$_cp_myseason'
						ORDER BY  `week`";
					$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
					$_cp_myweek=$row3[0];
						$_cp_sql4 = "SELECT `team`, `franchise`, `coach`  
						FROM `f_games` 
						WHERE `league`= '$_cp_myleague' and `season`= '$_cp_myseason' and `week`= '$_cp_myweek'
						ORDER BY `franchise`
						";
						$res4 = execute_db($_cp_sql4, $conn);
						while($row4 = fetch_row_db($res4)){
							$_cp_myteam=$row4[0];
							$_cp_myfranchise=$row4[1];
							$_cp_mycoach=$row4[2];
							#echo "<p>Coach of $_cp_myteam ($_cp_myfranchise) in $_cp_myleague s$_cp_myseason w$_cp_myweek is $_cp_mycoach</p>";
							#Split out team name
							$_cp_myteamsplit=explode(' ',$_cp_myteam,2);
							$_cp_mycity=$_cp_myteamsplit[0];
							$_cp_mynickname=$_cp_myteamsplit[1];
							#echo "<p>City is $_cp_mycity, nickname is $_cp_mynickname</p>";
							if (strpos($_cp_mynickname,' ')) {
								#Nickname has muliple words so need to split
								$_cp_mynicknamesplit=explode(' ',$_cp_mynickname,2);
								$_cp_mycity.=' ';
								$_cp_mycity.=$_cp_mynicknamesplit[0];
								$_cp_mynickname=$_cp_mynicknamesplit[1];
								#echo "<p>City is now $_cp_mycity, nickname is now $_cp_mynickname</p>";
							}
							
							
							$_cp_sql5 = "	UPDATE `fc_franchises` 
											SET `team`='$_cp_myteam', `city`='$_cp_mycity', `nickname`='$_cp_mynickname', `coach`='$_cp_mycoach'
											WHERE `league`= '$_cp_myleague' and `franchise`=$_cp_myfranchise ";
							$res5 = execute_db($_cp_sql5, $conn);	
						}
					}
					}
				}

//Commander in Chief
$_cp_sql = "INSERT INTO `fc_cicgames`
			SELECT CONCAT (a.`league`,'-',a.`season`,'-',a.`franchise`) AS `id`,a.`league`,a.`season`,a.`franchise`, 
			sum(a.`win`) AS `wins`, SUM(a.`lose`) AS `losses`, SUM(a.`tie`) as `ties` , SUM(a.`score`) as `pfor`, SUM(a.`opp_score`) as `pagn`,
			(sum(a.`win`)*10 +  SUM(a.`tie`)*5) AS `pts`, SUM(a.`score`)-SUM(a.`opp_score`) AS `diff`
			FROM `f_games` a
				INNER JOIN `fc_franchises` b ON a.`franchise` = b.`franchise`
				INNER JOIN `fc_franchises` c ON a.`opp_franchise` = c.`franchise`    
			WHERE `week` > 0 AND `week` <12  AND  b.`academy` = 1 AND  c.`academy` = 1 
			GROUP BY a.`league`,a.`season`,a.`franchise`
			ORDER BY a.`league`,a.`season`,a.`franchise`";
$res = execute_db($_cp_sql, $conn);


//Now work out the winners
$_cp_sql = "SELECT DISTINCT `league`,`season`
FROM `fc_cicgames` 
WHERE 1
ORDER BY `league`,`season` DESC ";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$_cp_myleague=$row[0];
			$_cp_myseason=$row[1];
				//Check regular season is over before assigning Conference title
				$_cp_sql4 = "SELECT MAX(`week`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res4 = execute_db($_cp_sql4, $conn);			
				while($row4 = fetch_row_db($res4)){
				if ($row4[0]>10) {
					#echo "<p>$_cp_myleague $_cp_myseason $_cp_myconf</p>";
					$_cp_sql2 = "SELECT `franchise` 
							FROM `fc_cicgames`  
							WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason 
							ORDER BY `pts` DESC, `diff` DESC, `wins` DESC, `franchise` DESC
							LIMIT 1";
					$res2 = execute_db($_cp_sql2, $conn);			
					while($row2 = fetch_row_db($res2)){
						$_cp_sql3 = "UPDATE `fc_franchises` 
								SET `CicWins`=`CicWins`+1, `CicYears`=CONCAT(`CicYears`,'$_cp_myseason ')
								WHERE `franchise`=$row2[0]";
						$res3 = execute_db($_cp_sql3, $conn);			
						}		
				}
			}			 
}

//College Rivalries
$_cp_sql = "INSERT INTO `fc_rivalrygames`
			SELECT CONCAT (a.`league`,'-',a.`season`,'-',a.`franchise`) AS `id`,a.`league`,a.`season`,a.`franchise`, 
			sum(a.`win`) AS `wins`, SUM(a.`lose`) AS `losses`, SUM(a.`tie`) as `ties` , SUM(a.`score`) as `pfor`, SUM(a.`opp_score`) as `pagn`,
			(sum(a.`win`)*10 +  SUM(a.`tie`)*5) AS `pts`, SUM(a.`score`)-SUM(a.`opp_score`) AS `diff`,b.`Rivalry`, c.`franchise`
			FROM `f_games` a
				INNER JOIN `fc_franchises` b ON a.`franchise` = b.`franchise`
				INNER JOIN `fc_franchises` c ON a.`opp_franchise` = c.`franchise`    
			WHERE `week` > 0 AND `week` <12  AND  (b.`rivalry` = c.`rivalry` ) 
			GROUP BY a.`league`,a.`season`,a.`franchise`";
$res = execute_db($_cp_sql, $conn);

//Now work out the winners
$_cp_sql = "SELECT DISTINCT `league`,`season`, `Rivalry`
FROM `fc_rivalrygames` 
WHERE 1
ORDER BY `league`,`season` DESC ";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$_cp_myleague=$row[0];
			$_cp_myseason=$row[1];
			$_cp_myrivalry=$row[2];
				//Check regular season is over before assigning Conference title
				$_cp_sql4 = "SELECT MAX(`week`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res4 = execute_db($_cp_sql4, $conn);			
				while($row4 = fetch_row_db($res4)){
				if ($row4[0]>10) {
					#echo "<p>$_cp_myleague $_cp_myseason $_cp_myconf</p>";
					$_cp_sql2 = "SELECT `franchise` 
							FROM `fc_rivalrygames`  
							WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason AND `Rivalry` = $_cp_myrivalry
							ORDER BY `pts` DESC, `diff` DESC, `wins` DESC, `franchise` DESC
							LIMIT 1";
					#echo "<p>$_cp_sql2</p>";		
					$res2 = execute_db($_cp_sql2, $conn);			
					while($row2 = fetch_row_db($res2)){
						$_cp_sql3 = "UPDATE `fc_franchises` 
								SET `RivalryWins`=`RivalryWins`+1, `RivalryYears`=CONCAT(`RivalryYears`,'$_cp_myseason ')
								WHERE `franchise`=$row2[0]";
						$res3 = execute_db($_cp_sql3, $conn);			
						}		
				}
			}			 
}


$time_end = microtime(true);
$time = $time_end - $time_start;
echo "<p>Processed updates in ";
echo  round($time,2) . " s";
echo "</p>";

?>
