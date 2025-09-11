<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'g_functions.php';
require_once 'mydatabase.php';

$time_start = microtime(true);
$str="<br /><div class='nz-card'>";
$str.="<div class='w3-container $mycolour6'>";
$str.="<br />";
$str.="<div class='w3-pale-green'>";
$str.="<h1>Gameplan Football Update College seasons</h1>";
$str.="</div>";
output($str);
$str= "<div class='w3-panel $mycolour4 nz-card w3-round-xxlarge'>";
output($str);
$str="<h2>About to truncate existing tables</h3>";
output($str);
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
$number_of_rows = number_format($res->rowCount() ); 
$str="<h2>Calculating College Seasons</h3>";
output($str);
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
				$_cp_sql3 = "SELECT `franchise`,sum(`win`),sum(`lose`),sum(`tie`),sum(`score`), sum(`opp_score`)
								FROM `f_games` 
								WHERE league='$_cp_myleague' AND season=$_cp_myseason AND `gametype`=1 
								GROUP BY `franchise`
								ORDER BY `franchise`";
				$res3 = execute_db($_cp_sql3, $conn);
				#echo "<p>$_cp_sql3</p>";
					while($row3 = fetch_row_db($res3)){
						$_cp_sql4 = "
						INSERT INTO `fc_seasons` (`id`, `franchise`, `season`, `coach`, `won`, `lost`, `tie`, `scored`, `conceded`) 
						VALUES (NULL, '$row3[0]', '$_cp_myseason', '', '$row3[1]', '$row3[2]', '$row3[3]', '$row3[4]', '$row3[5]');	
						";
						$res4 = execute_db($_cp_sql4, $conn);
					}
				#Season loop ended	
				}  
        }  

//Find Season Coaches
$_cp_sql77 = "SELECT b.`league`,a.`franchise`, a.`season` 
				FROM `fc_seasons` a
				INNER JOIN `fc_franchises` b ON a.franchise=b.franchise
				WHERE 1 
				ORDER BY b.`league` ASC, a.`franchise` ASC, a.`season` ASC;";
$res77 = execute_db($_cp_sql77, $conn);
				$number_of_rows77 = number_format($res77->rowCount() ); 
				$str="<h2>Found $number_of_rows77 Coach seasons</h2>";
				output($str);
					$j=0;	
					while($row77 = fetch_row_db($res77)){
					if ($j % 25 == 0) {
						$str='Processed ';
						$str.=$j;
						$str.=' Coach Seasons / ';
						output($str);
					}
					$j++;
						$mycoach='';
						#Find Week 16 coach
						$myleague=$row77[0];
						$myfranchise=$row77[1];
						$myseason=$row77[2];
						$_cp_sql78 = "SELECT `coach` FROM `f_games` WHERE `league`='$myleague' AND `season`=$myseason AND `week`=11 AND `franchise`=$myfranchise";
						#echo "<br />$_cp_sql78";
							$res78 = execute_db($_cp_sql78, $conn);
							while($row78 = fetch_row_db($res78)){
								$mycoach=$row78[0];
							}
						$_cp_sql4 = "UPDATE `fc_seasons` SET `coach`='$mycoach' WHERE `franchise`=$myfranchise AND `season`=$myseason";
						$res4 = execute_db($_cp_sql4, $conn);
						#echo "<br />$_cp_sql4";
					}
							//final total
					$str='Processed ';
					$str.=$j;
					$str.=' Coach Seasons.';
					output($str);
					
//Find missing Coaches
$_cp_sql80 = "SELECT `franchise`, `season` 
				FROM `fc_seasons` 
				WHERE `coach` IS NULL OR `coach` = ''
				ORDER BY `franchise`, `season` ;";
$res80 = execute_db($_cp_sql80, $conn);
						$number_of_rows80 = number_format($res80->rowCount() ); 
						$str="<h2>Found $number_of_rows80 missing coach seasons</h2>";
						output($str);
						$j=0;
					while($row80 = fetch_row_db($res80)){
					if ($j % 25 == 0) {
						$str='Processed ';
						$str.=$j;
						$str.=' missing Coach Seasons / ';
						output($str);
					}
					$j++;
						$mycoach='';
						#Find Week 99 coach
						$myfranchise=$row80[0];
						$myseason=$row80[1];
						$_cp_sql81 = "SELECT `coach` FROM `f_games` WHERE `season`=$myseason AND `week`=99 AND `franchise`=$myfranchise";
						#echo "<br />$_cp_sql81";
							$res81 = execute_db($_cp_sql81, $conn);
							while($row81 = fetch_row_db($res81)){
								$mycoach=$row81[0];
							}
						$_cp_sql82 = "UPDATE `fc_seasons` SET `coach`='$mycoach' WHERE `franchise`=$myfranchise AND `season`=$myseason";
						$res82 = execute_db($_cp_sql82, $conn);
						#echo "<br />$_cp_sql82";
					}
					$str='Processed ';
					$str.=$j;
					$str.=' missing Coach Seasons.';
					output($str);

//Find missing Coaches
$_cp_sql80 = "SELECT `franchise`, `season` 
				FROM `fc_seasons` 
				WHERE `coach` IS NULL OR `coach` = ''
				ORDER BY `franchise`, `season` ;";
$res80 = execute_db($_cp_sql80, $conn);
						$number_of_rows80 = number_format($res80->rowCount() ); 
						$str="<h2>Found $number_of_rows80 missing coach seasons</h2>";
						output($str);
						$j=0;
					while($row80 = fetch_row_db($res80)){
					if ($j % 25 == 0) {
						$str='Processed ';
						$str.=$j;
						$str.=' missing Coach Seasons / ';
						output($str);
					}
					$j++;
						$mycoach='';
						#Find Week 95 coach
						$myfranchise=$row80[0];
						$myseason=$row80[1];
						$_cp_sql81 = "SELECT `coach` FROM `f_games` WHERE `season`=$myseason AND `week`=95 AND `franchise`=$myfranchise";
						#echo "<br />$_cp_sql81";
							$res81 = execute_db($_cp_sql81, $conn);
							while($row81 = fetch_row_db($res81)){
								$mycoach=$row81[0];
							}
						$_cp_sql82 = "UPDATE `fc_seasons` SET `coach`='$mycoach' WHERE `franchise`=$myfranchise AND `season`=$myseason";
						$res82 = execute_db($_cp_sql82, $conn);
						#echo "<br />$_cp_sql82";
					}
					$str='Processed ';
					$str.=$j;
					$str.=' missing Coach Seasons.';
					output($str);



$_cp_sql80 = "SELECT `franchise`, `season` 
				FROM `fc_seasons` 
				WHERE `coach` IS NULL OR `coach` = ''
				ORDER BY `franchise`, `season` ;";
$res80 = execute_db($_cp_sql80, $conn);
						$number_of_rows80b = number_format($res80->rowCount() ); 
						$str="<h2>Found $number_of_rows80b missing coach seasons</h2>";
						echo "<br />$str";
						$j=0;
					while($row80 = fetch_row_db($res80)){
						$j++;
						if ($j % 5 == 0) {
							$str=' / Processed ';
							$str.=$j;
							$str.=' missing Coach Seasons ';
							output($str);
						}

						$mycoach='';
						#Find Week 1 coach
						$myfranchise=$row80[0];
						$myseason=$row80[1];
						$_cp_sql81 = "SELECT `coach` FROM `f_games` WHERE `season`=$myseason AND `week`=1 AND `franchise`=$myfranchise";
						#echo "<br />$_cp_sql81";
							$res81 = execute_db($_cp_sql81, $conn);
							while($row81 = fetch_row_db($res81)){
								$mycoach=$row81[0];
							}
						$_cp_sql82 = "UPDATE `fc_seasons` SET `coach`='$mycoach' WHERE `franchise`=$myfranchise AND `season`=$myseason";
						$res82 = execute_db($_cp_sql82, $conn);
						#echo "<br />$_cp_sql82";
					}	
					$str='Processed ';
					$str.=$j;
					$str.=' missing Coach Seasons.';
					output($str);				

//End of Coaches for Seasons

$_cp_sql10 = "UPDATE `fc_seasons` SET `complete`=1 WHERE `season` IN (SELECT `season`FROM `f_games` WHERE `week`=13)";
$res10 = execute_db($_cp_sql10, $conn);

$str="<h2>Calculating Team records</>";
output($str);
$_cp_sql = "SELECT DISTINCT `franchise` 
FROM `fc_franchises` 
WHERE `ftype` = 'College'
ORDER BY `franchise` ASC";
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

$str="<h2>Finding perfect Seasons</h3>";
output($str);
$_cp_sql = "SELECT DISTINCT `franchise`,`season` 
FROM `fc_seasons` 
WHERE `won`=11 AND `complete`=1
ORDER BY `season` DESC, `franchise` ";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			#echo "<p>$row[0] - $row[1]</p>";
			$_cp_myfranchise=$row[0];
			$_cp_myseason=$row[1];


			//Now we have 11-0 teams check if they won NC
			$_cp_sql3 = "SELECT 'Yes' FROM `fc_franchises` WHERE `franchise` = $_cp_myfranchise AND  `WinnerYears` LIKE '%$_cp_myseason%'";
			#echo "<p>$_cp_sql3</p>";
			$res3 = execute_db($_cp_sql3, $conn);
			while($row3 = fetch_row_db($res3)){
						$_cp_myperfecto=$row3[0];
			}
				
				if ('Yes'==$_cp_myperfecto){
						#echo "<p>$_cp_myfranchise - $_cp_myseason - $_cp_myperfecto</p>";
						$_cp_sql4 = "UPDATE `fc_franchises` SET `PerfectYears`=CONCAT (`PerfectYears`,' <em>$_cp_myseason</em>') WHERE `franchise`=$_cp_myfranchise";
					} 
				$res4 = execute_db($_cp_sql4, $conn);
				$_cp_sql5 = "UPDATE `fc_franchises` SET `Perfect`=`Perfect` +1 WHERE `franchise`=$_cp_myfranchise";
				$res5 = execute_db($_cp_sql5, $conn);
		}	


$str="<h2>Calculating Conference records</h3>";
output($str);
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

$str="<h2>Updating Team and Coach names</h3>";
output($str);
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

$str="<h2>Calculating Commander in Chief records</h3>";
output($str);
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

$str="<h2>Calculating Rivalry records</h3>";
output($str);
$_cp_sql = "INSERT INTO `fc_rivalrygames`
			SELECT CONCAT (a.`league`,'-',a.`season`,'-',a.`franchise`) AS `id`,a.`league`,a.`season`,a.`franchise`, 
			sum(a.`win`) AS `wins`, SUM(a.`lose`) AS `losses`, SUM(a.`tie`) as `ties` , SUM(a.`score`) as `pfor`, SUM(a.`opp_score`) as `pagn`,
			(sum(a.`win`)*10 +  SUM(a.`tie`)*5) AS `pts`, SUM(a.`score`)-SUM(a.`opp_score`) AS `diff`,b.`Rivalry`, c.`franchise`
			FROM `f_games` a
				INNER JOIN `fc_franchises` b ON a.`franchise` = b.`franchise`
				INNER JOIN `fc_franchises` c ON a.`opp_franchise` = c.`franchise`    
			WHERE `week` > 0 AND `week` <12  AND  (b.`rivalry` = c.`rivalry` ) 
			GROUP BY a.`league`,a.`season`,a.`franchise`,c.`franchise`";
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

$str="</div>";
output($str);


require_once 'g_footer.php';
?>
