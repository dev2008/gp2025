<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'g_functions.php';
require_once 'mydatabase.php';

$time_start = microtime(true);
$str="<br /><div class='w3-card-4'>";
$str.="<div class='w3-container $mycolour6'>";
$str.="<div class='w3-pale-green'>";
$str.="<h1>Gameplan Football Update Pro Seasons</h1>";
$str.="</div>";
$str.="</header>";
output($str);
$str= "<div class='w3-panel $mycolour4 w3-card-4 w3-round-xxlarge'>";
output($str);


$str="<h2>About to process some records</h3>";
output($str);
//Reset all records, this won't be needed once all done then update will only look for unprocessed seasons
$_cp_sql = "TRUNCATE `fp_seasons`";
$res = execute_db($_cp_sql, $conn);

//Retireve all Pro Leagues
$_cp_sql = "SELECT DISTINCT `league` 
FROM `fp_franchises` 
WHERE `ftype` = 'Pro'
ORDER BY `league`";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
				$_cp_myleague=$row[0];
				//Retrieve Seasons for that League
				$_cp_sql2 = "SELECT DISTINCT `season`
				FROM `f_games` 
				WHERE `league`= '$_cp_myleague'
				ORDER BY  `season`";
				$res2 = execute_db($_cp_sql2, $conn);
				$number_of_rows = number_format($res2->rowCount() ); 
				$str="<h2>Found $number_of_rows seasons</h2>";
				output($str);
				$j=0;
				while($row2 = fetch_row_db($res2)){
					$_cp_myseason=$row2[0];
					$j++;
					if ($j % 5 == 0) {
						$str='Processed ';
						$str.=$j;
						$str.=' Pro Seasons / ';
						output($str);
					}
				
				//Looping through Seasons	
				//Find Season Records
				$_cp_sql3 = "SELECT `franchise`,sum(`win`),sum(`lose`),sum(`tie`),sum(`score`), sum(`opp_score`)
							 FROM `f_games` 
							 WHERE league='$_cp_myleague' AND season=$_cp_myseason AND week <>0 AND `week` <>17  AND `week` <> 18  AND `week` <> 19 AND `week` <> 20
							 GROUP BY `franchise`
							 ORDER BY `franchise`";
				#echo "$_cp_sql3";				
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_sql4 = "
						INSERT INTO `fp_seasons` (`id`, `franchise`, `season`, `coach`, `won`, `lost`, `tie`, `scored`, `conceded`) 
						VALUES (NULL, '$row3[0]', '$_cp_myseason', '', '$row3[1]', '$row3[2]', '$row3[3]', '$row3[4]', '$row3[5]');	
						";
						$res4 = execute_db($_cp_sql4, $conn);

					}

				#Season loop ended	
				}  
        }  
		//final total
		$str=' Processed ';
		$str.=$j;
		$str.=' Pro Seasons.';
		output($str);
		
//Find Season Coaches
$_cp_sql77 = "SELECT b.`league`,a.`franchise`, a.`season` 
				FROM `fp_seasons` a
				INNER JOIN `fp_franchises` b ON a.franchise=b.franchise
				WHERE 1 
				ORDER BY b.`league` ASC, a.`franchise` ASC, a.`season` ASC;";
$res77 = execute_db($_cp_sql77, $conn);
				$number_of_rows77 = number_format($res77->rowCount() ); 
				$str="<h2>Found $number_of_rows77 Coach seasons</h2>";
				output($str);
					$j=0;	
					while($row77 = fetch_row_db($res77)){
					if ($j % 250 == 0) {
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
						$_cp_sql78 = "SELECT `coach` FROM `f_games` WHERE `league`='$myleague' AND `season`=$myseason AND `week`=16 AND `franchise`=$myfranchise";
						#echo "<br />$_cp_sql78";
							$res78 = execute_db($_cp_sql78, $conn);
							while($row78 = fetch_row_db($res78)){
								$mycoach=$row78[0];
							}
						$_cp_sql4 = "UPDATE `fp_seasons` SET `coach`='$mycoach' WHERE `franchise`=$myfranchise AND `season`=$myseason";
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
				FROM `fp_seasons` 
				WHERE `coach` IS NULL OR `coach` = ''
				ORDER BY `franchise`, `season` ;";
$res80 = execute_db($_cp_sql80, $conn);
						$number_of_rows80 = number_format($res80->rowCount() ); 
						$str="<h2>Found $number_of_rows80 missing coach seasons</h2>";
						output($str);
						$j=0;
					while($row80 = fetch_row_db($res80)){
					if ($j % 50 == 0) {
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
						$_cp_sql82 = "UPDATE `fp_seasons` SET `coach`='$mycoach' WHERE `franchise`=$myfranchise AND `season`=$myseason";
						$res82 = execute_db($_cp_sql82, $conn);
						#echo "<br />$_cp_sql82";
					}
					$str='Processed ';
					$str.=$j;
					$str.=' missing Coach Seasons.';
					output($str);

$_cp_sql80 = "SELECT `franchise`, `season` 
				FROM `fp_seasons` 
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
							$str='Processed ';
							$str.=$j;
							$str.=' missing Coach Seasons / ';
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
						$_cp_sql82 = "UPDATE `fp_seasons` SET `coach`='$mycoach' WHERE `franchise`=$myfranchise AND `season`=$myseason";
						$res82 = execute_db($_cp_sql82, $conn);
						#echo "<br />$_cp_sql82";
					}	
					$str='Processed ';
					$str.=$j;
					$str.=' missing Coach Seasons.';
					output($str);				

//Retrieve all Pro Leagues
$_cp_sql = "SELECT DISTINCT `franchise` 
FROM `fp_franchises` 
WHERE `ftype` = 'Pro'
ORDER BY `franchise` ";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$_cp_myfranchise=$row[0];
	#Loop through all teams and extract details
	$_cp_sql2 = "SELECT MAX(`won`), MAX(`lost`), MAX(`scored`), MAX(`conceded`)
					FROM `fp_seasons` 
					WHERE `franchise`=$_cp_myfranchise";
	$res2 = execute_db($_cp_sql2, $conn);
        while($row2 = fetch_row_db($res2)){
			$_cp_sql3 = "UPDATE `fp_franchises` SET `MaxWins`=$row2[0], `MaxLosses`=$row2[1], `MaxScored`=$row2[2], `MaxConceded`=$row2[3]
							WHERE `franchise`=$_cp_myfranchise";
			$res3 = execute_db($_cp_sql3, $conn);				
	}
}


//Retrieve all Pro Coaches
$_cp_sql = "TRUNCATE fp_coaches";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 


$_cp_sql = "SELECT DISTINCT `league`, `coach` 
FROM `f_games` 
WHERE `league` LIKE 'NF%'
ORDER BY `league` ASC, `coach` ASC";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
				$number_of_rowsX = number_format($result->rowCount() ); 
				$str="<h2>Found $number_of_rowsX coaches</h3>";
				output($str);
		$i=1;
		$k=0;
        while($row = fetch_row_db($result)){
					$k++;
					if ($k % 10 == 0) {
						$str='Processed ';
						$str.=$k;
						$str.=' Coaches / ';
						output($str);
					}
				$_cp_myleague=$row[0];
				$_cp_mycoach=$row[1];
				
				#If active coach show current team and asterik
				$_cp_sql2 = "SELECT IFNULL((SELECT `team` FROM `fp_franchises` WHERE `coach`='$_cp_mycoach'), 'Retired') ";
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
									INNER JOIN  `fp_franchises` b ON a.`franchise`=b.`franchise`  
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
				
				#Determine wins and losses in Playoffs
				$_cp_sql4 = "	SELECT SUM(a.`win`),SUM(a.`lose`)
								FROM `f_games` a
								WHERE a.`league` ='$_cp_myleague' AND a.`coach`='$_cp_mycoach'  AND (a.`gametype` =35 OR a.`gametype` =36)";
				#echo "<p>$_cp_sql4</p>";
				$result4 = $conn->prepare($_cp_sql4); 
				$result4->execute(); 
				while($row4 = fetch_row_db($result4)){
					$_cp_mypowins=$row4[0];	
					$_cp_mypolosses=$row4[1];		
				}
				
				if(is_null($_cp_mypowins)){
					$_cp_mypowins=0;
				} 					
				if(is_null($_cp_mypolosses)){
					$_cp_mypolosses=0;
				}				
				
				
				#Determine wins and losses in Regular Season
				$_cp_sql4 = "	SELECT SUM(a.`win`),SUM(a.`lose`), SUM(a.`tie`)
								FROM `f_games` a
								WHERE a.`league` ='$_cp_myleague' AND a.`coach`='$_cp_mycoach'  AND a.`gametype` = 24  ";
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
				
				#Determine Superbowls
				$_cp_sql2 = "SELECT IFNULL((SELECT SUM(`win`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `gametype`=36 AND `coach`='$_cp_mycoach'), 0) AS 'NCWins'";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				while($row2 = fetch_row_db($result2)){
						$_cp_mysb=$row2[0];
					}

				#Determine Superbowl Runners Up
				$_cp_sql2 = "SELECT IFNULL((SELECT SUM(`lose`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `gametype`=36 AND `coach`='$_cp_mycoach'), 0) AS 'NCLosses'";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				while($row2 = fetch_row_db($result2)){
						$_cp_myru=$row2[0];
					}

				#Add up total Regular Sreason games
				$_cp_myrsgames=$_cp_myrswins+$_cp_myrslosses+$_cp_myrsties;
				
				#Write data to table
				if (1==$_cp_myretirement){
					
						$_cp_sql4 = "INSERT INTO `fp_coaches` (`id`, `league`, `coach`, `team`, `retired`, `Winner`, `Runnerup`, `powins`, `polosses`, `rsgames`, `rswins`, `rslosses`, `rsties`, `Link`) 
									VALUES ('$_cp_myleague-$_cp_mycoach','$_cp_myleague', '$_cp_mycoach', '$_cp_myoldteams', 'Y', $_cp_mysb, $_cp_myru,  '$_cp_mypowins', '$_cp_mypolosses', '$_cp_myrsgames', '$_cp_myrswins', '$_cp_myrslosses', $_cp_myrsties, NULL)";
				} else {
					
								$_cp_sql4 = "INSERT INTO `fp_coaches` (`id`,`league`, `coach`, `team`, `retired`, `Winner`, `Runnerup`, `powins`, `polosses`, `rsgames`,  `rswins`, `rslosses`, `rsties`, `Link`) 
								VALUES ('$_cp_myleague-$_cp_mycoach','$_cp_myleague', '$_cp_mycoach', '$_cp_mycurrentteam', '', $_cp_mysb, $_cp_myru,  '$_cp_mypowins', '$_cp_mypolosses',  '$_cp_myrsgames', '$_cp_myrswins', '$_cp_myrslosses', $_cp_myrsties, NULL)";
				}
				$result4 = $conn->prepare($_cp_sql4); 
				$result4->execute(); 

				
				

				$i++;


}
$str='Processed ';
$str.=$k;
$str.=' Coaches.';
output($str);

//Update Team and Coach Names
$_cp_sql = "SELECT DISTINCT `league` 
FROM `fp_franchises` 
WHERE `ftype` = 'Pro'
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
						$number_of_rows4 = number_format($res4->rowCount() ); 
						$str="<h4>Found $number_of_rows4 teams</h4>";
						output($str);
						$l=0;
						while($row4 = fetch_row_db($res4)){
										if ($l % 4 == 0) {
										$str='Processed ';
										$str.=$l;
										$str.=' Teams / ';
										output($str);
									}
							$l++;
							$_cp_myteam=$row4[0];
							$_cp_myfranchise=$row4[1];
							$_cp_mycoach=$row4[2];
							echo "<p>Coach of $_cp_myteam ($_cp_myfranchise) in $_cp_myleague s$_cp_myseason w$_cp_myweek is $_cp_mycoach</p>";
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
							
							
							$_cp_sql5 = "	UPDATE `fp_franchises` 
											SET `team`='$_cp_myteam', `city`='$_cp_mycity', `nickname`='$_cp_mynickname', `coach`='$_cp_mycoach'
											WHERE `league`= '$_cp_myleague' and `franchise`=$_cp_myfranchise ";
							$res5 = execute_db($_cp_sql5, $conn);	
						}
					}
					}
				}
$str='Processed ';
$str.=$l;
$str.=' Teams.';
$str.="<br>";
output($str);

require_once 'g_footer.php';
