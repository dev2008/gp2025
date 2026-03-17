<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require_once 'g_functions.php';
require_once 'mydatabase.php';

$str="<br /><div class='w3-card-4'>";
$str.="<header class='w3-container w3-blue-gray'>";
$str.="<h1>Creating Play by Play Season totals</h1>";
$str.="</header>";
output($str);

$str="<div class='w3-container w3-pale-blue'>";
output($str);
//Text for middle box
$str="<h2>Calculating relevant seasons</h3>";
output($str);
$str="<div class='w3-container w3-teal'>\n";
$str.="<h2>Finding Team Seasons</h3>";
output($str);
//Truncate PBP Tables - in future check for new only
$_cp_sql = "TRUNCATE `n_s_mv_off`; ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$_cp_sql = "TRUNCATE `n_s_mv_def`; ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$_cp_sql = "TRUNCATE `n_s_pe_off`; ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$_cp_sql = "TRUNCATE `n_s_pe_def`; ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$_cp_sql = "TRUNCATE `n_s_pi_off`; ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$_cp_sql = "TRUNCATE `n_s_pi_def`; ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$_cp_sql = "TRUNCATE `n_s_mv_off_f`; ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$_cp_sql = "TRUNCATE `n_s_mv_def_f`; ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$_cp_sql = "TRUNCATE `n_s_pe_off_f`; ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$_cp_sql = "TRUNCATE `n_s_pe_def_f`; ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$_cp_sql = "TRUNCATE `n_s_pi_off_f`; ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$_cp_sql = "TRUNCATE `n_s_pi_def_f`; ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$_cp_sql = "TRUNCATE `n_OffDef`; ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$_cp_sql = "TRUNCATE `n_OffDefForm`; ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$_cp_sql = "TRUNCATE `n_OffForm`; ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$_cp_sql = "TRUNCATE `n_roundup`; ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
//Start League Loop
$_cp_sql = "SELECT `myleague`, `myteam`, `mycode` FROM `n_relevant` WHERE 1 ORDER BY `myleague` ASC, `myteam` ASC";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$number_of_rows = $result->rowCount() ; 

$j=0;
$mycount=0;

//This is the main League loop
while($row = fetch_row_db($result)){
	$_cp_myleague = $row[0];
	$_cp_myteam = $row[1];
	$_cp_mycode = $row[2];
	//For each League 
	//Now we need to work out relevant seasons for each of these
	$_cp_sql2 = "SELECT `a_season` FROM `n_playbyplay` WHERE `a_league`=? AND `a_poss`=?  GROUP BY `a_season`  ORDER BY `a_season` DESC ;";
	$result2 = $conn->prepare($_cp_sql2); 
	$result2->execute([$_cp_myleague,$_cp_myteam]); 
			$number_of_rows = $result2->rowCount() ; 
			$str="<h2>Processing $number_of_rows seasons for $_cp_myteam ($_cp_myleague)</h3>";
			output($str);	

	while($row2 = fetch_row_db($result2)){

			$_cp_myseason = $row2[0];
			#$str="<p>Found season $_cp_myseason for $_cp_myteam($_cp_myleague)</p>";
			#output($str);	
			
			//Now we need to calculate offensive season totals without formation
			$_cp_sql3 = "SELECT `a_ocall` as 'Play' ,COUNT(`a_ocall`) as 'Number', ROUND(AVG(`a_yards`),2) as 'Average' FROM `n_playbyplay` WHERE `a_league`=? AND `a_off`=? AND `a_season`=? AND `a_form` NOT IN ('P','X','F') AND `a_penalty`<>'1' AND `a_intercept`<>1 AND `a_fumble`<>1 GROUP BY `a_ocall` ORDER BY AVG(`a_yards`) DESC";
			$result3 = $conn->prepare($_cp_sql3); 		
			$result3->execute([$_cp_myleague,$_cp_myteam,$_cp_myseason]);

			while($row3 = fetch_row_db($result3)){
				$_cp_myplay = $row3[0];
				$_cp_mynumber = $row3[1];
				$_cp_myaverage = $row3[2];
					#$str="<p>INSERT INTO Found season $_cp_myseason for $_cp_myteam($_cp_myleague) - $_cp_myplay / $_cp_mynumber / $_cp_myaverage";
					#output($str);	
					$_cp_sql4 = "INSERT INTO `n_s_";
					$_cp_sql4 .= strtolower($_cp_myteam);
					$_cp_sql4 .= "_off` (`playID`, `league`, `season`, `playcall`, `number`, `average`) VALUES (NULL, '$_cp_myleague', '$_cp_myseason', '$_cp_myplay', '$_cp_mynumber', '$_cp_myaverage');";
					$result4 = $conn->prepare($_cp_sql4);
					$result4->execute(); 
				}
			//End of offensive season totals without formation	

			//Now we need to calculate defensive season totals without formation
			$_cp_sql3 = "SELECT `a_dcall` as 'Play' ,COUNT(`a_ocall`) as 'Number', ROUND(AVG(`a_yards`),2) as 'Average' FROM `n_playbyplay` WHERE `a_league`=? AND `a_def`=? AND `a_season`=? AND `a_form` NOT IN ('P','X','F') AND `a_penalty`<>'1' AND `a_intercept`<>1 AND `a_fumble`<>1 GROUP BY `a_dcall` ORDER BY AVG(`a_yards`) DESC";
			$result3 = $conn->prepare($_cp_sql3); 		
			$result3->execute([$_cp_myleague,$_cp_myteam,$_cp_myseason]);

			while($row3 = fetch_row_db($result3)){
				$_cp_myplay = $row3[0];
				$_cp_mynumber = $row3[1];
				$_cp_myaverage = $row3[2];
					#$str="<p>INSERT INTO Found season $_cp_myseason for $_cp_myteam($_cp_myleague) - $_cp_myplay / $_cp_mynumber / $_cp_myaverage";
					#output($str);	
					$_cp_sql4 = "INSERT INTO `n_s_";
					$_cp_sql4 .= strtolower($_cp_myteam);
					$_cp_sql4 .= "_def` (`playID`, `league`, `season`, `playcall`, `number`, `average`) VALUES (NULL, '$_cp_myleague', '$_cp_myseason', '$_cp_myplay', '$_cp_mynumber', '$_cp_myaverage');";
					$result4 = $conn->prepare($_cp_sql4);
					$result4->execute(); 
				}
			//End of defensive season totals without formation	

			//Now we need to calculate offensive season totals with formation
			$_cp_sql3 = "SELECT `a_ocall` as 'Play' ,COUNT(`a_ocall`) as 'Number', ROUND(AVG(`a_yards`),2) as 'Average', `a_form` as `Formation` FROM `n_playbyplay` WHERE `a_league`=? AND `a_off`=? AND `a_season`=? AND `a_form` NOT IN ('P','X','F') AND `a_penalty`<>'1' AND `a_intercept`<>1 AND `a_fumble`<>1 GROUP BY `a_form`,`a_ocall` ORDER BY AVG(`a_yards`) DESC";
			$result3 = $conn->prepare($_cp_sql3); 		
			$result3->execute([$_cp_myleague,$_cp_myteam,$_cp_myseason]);

			while($row3 = fetch_row_db($result3)){
				$_cp_myplay = $row3[0];
				$_cp_mynumber = $row3[1];
				$_cp_myaverage = $row3[2];
				$_cp_myformation = $row3[3];
					#$str="<p>INSERT INTO Found season $_cp_myseason for $_cp_myteam($_cp_myleague) - $_cp_myplay / $_cp_mynumber / $_cp_myaverage";
					#output($str);	
					$_cp_sql4 = "INSERT INTO `n_s_";
					$_cp_sql4 .= strtolower($_cp_myteam);
					$_cp_sql4 .= "_off_f` (`playID`, `league`, `season`, `formation`,`playcall`, `number`, `average`) VALUES (NULL, '$_cp_myleague', '$_cp_myseason','$_cp_myformation', '$_cp_myplay', '$_cp_mynumber', '$_cp_myaverage');";
					$result4 = $conn->prepare($_cp_sql4);
					$result4->execute(); 
				}
			//End of offensive season totals with formation		
			
			//Now we need to calculate defensive season totals without formation
			$_cp_sql3 = "SELECT `a_dcall` as 'Play' ,COUNT(`a_ocall`) as 'Number', ROUND(AVG(`a_yards`),2) as 'Average'
				FROM `n_playbyplay` 
				WHERE `a_league`=? AND `a_def`=? AND `a_season`=? AND `a_form` NOT IN ('P','X','F') AND `a_penalty`<>'1' AND `a_intercept`<>1 AND `a_fumble`<>1 
				GROUP BY `a_dcall` 
				ORDER BY AVG(`a_yards`) DESC";
			$result3 = $conn->prepare($_cp_sql3); 		
			$result3->execute([$_cp_myleague,$_cp_myteam,$_cp_myseason]);

			while($row3 = fetch_row_db($result3)){
				$_cp_myplay = $row3[0];
				$_cp_mynumber = $row3[1];
				$_cp_myaverage = $row3[2];
					#$str="<p>INSERT INTO Found season $_cp_myseason for $_cp_myteam($_cp_myleague) - $_cp_myplay / $_cp_mynumber / $_cp_myaverage";
					#output($str);	
					$_cp_sql4 = "INSERT INTO `n_s_";
					$_cp_sql4 .= strtolower($_cp_myteam);
					$_cp_sql4 .= "_def_f` (`playID`, `league`, `season`, `formation`, `playcall`, `number`, `average`) VALUES (NULL, '$_cp_myleague', '$_cp_myseason','$_cp_myformation', '$_cp_myplay', '$_cp_mynumber', '$_cp_myaverage');";
					$result4 = $conn->prepare($_cp_sql4);
					$result4->execute(); 
				}
			//End of defensive season totals without formation			
			
		$mycount++;		
		}
	}

//Populate some adhoc tables
$str="<h2>Populating adhoc tables</h3>\n";
output($str);
$_cp_sql = "INSERT INTO `n_OffDef` SELECT NULL, concat(`a`.`a_ocall`,`a`.`a_dcall`) , `b`.`playtype` , `a`.`a_ocall` , `a`.`a_dcall` , count(`a`.`a_id`) , avg(`a`.`a_yards`)  FROM (`n_playbyplay` `a` join `n_playtypes` `b` on(`a`.`a_ocall` = `b`.`play`)) WHERE `b`.`playtype`<>'ST' GROUP BY `b`.`playtype`, `a`.`a_ocall`, `a`.`a_dcall` ORDER BY `a`.`a_ocall` ASC, `a`.`a_dcall` ASC, avg(`a`.`a_yards`) ASC ; ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

$_cp_sql = "INSERT INTO `n_OffDefForm` SELECT NULL, concat(`a`.`a_form` ,`a`.`a_ocall`,`a`.`a_dcall`) , `b`.`playtype` , `a`.`a_form` , `a`.`a_ocall` , `a`.`a_dcall` , count(`a`.`a_id`) , avg(`a`.`a_yards`)  FROM (`n_playbyplay` `a` join `n_playtypes` `b` on(`a`.`a_ocall` = `b`.`play`)) WHERE `b`.`playtype`<>'ST' GROUP BY `a`.`a_form`, `b`.`playtype`, `a`.`a_ocall`, `a`.`a_dcall` ORDER BY `a`.`a_form` ASC, `a`.`a_ocall` ASC, `a`.`a_dcall` ASC, avg(`a`.`a_yards`) ASC  ; ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

$_cp_sql = "INSERT INTO `n_OffForm` SELECT NULL, concat(`a`.`a_form` ,`a`.`a_ocall`) , `b`.`playtype` , `a`.`a_form` , `a`.`a_ocall` ,  count(`a`.`a_id`) , avg(`a`.`a_yards`)  
FROM (`n_playbyplay` `a` join `n_playtypes` `b` on(`a`.`a_ocall` = `b`.`play`)) 
WHERE `b`.`playtype`<>'ST' 
GROUP BY `a`.`a_form`, `a`.`a_ocall`, `b`.`playtype`
ORDER BY `a`.`a_form` ASC, `a`.`a_ocall` ASC, avg(`a`.`a_yards`) ASC  ; ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

$_cp_sql = "INSERT INTO `n_roundup` SELECT NULL, concat(`f_games`.`league`,`f_games`.`season`,`f_games`.`week`,`f_games`.`team`) AS `roundup_id`,`f_games`.`league` AS `league`,`f_games`.`season` AS `season`,`f_games`.`week` AS `week`,`f_games`.`team` AS `team`,`f_games`.`passatt` AS `passatt`,`f_games`.`rush` AS `rush`,`f_games`.`form1` AS `form1`,`f_games`.`form2` AS `form2`,`f_games`.`run1` AS `run1`,`f_games`.`run2` AS `run2`,`f_games`.`pass1` AS `pass1`,`f_games`.`pass2` AS `pass2`,`f_games`.`def1` AS `def1`,`f_games`.`def2` AS `def2`, concat(`f_games`.`score`,'-',`f_games`.`opp_score`) AS `score` from `f_games` ORDER BY `league` DESC, `season` DESC, `week` DESC; ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

//Start of footer
require_once 'g_footer.php';
?>
