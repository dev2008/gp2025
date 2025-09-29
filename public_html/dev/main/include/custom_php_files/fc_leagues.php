<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require_once 'g_functions.php';
$_cp_havewinners=0;
$_cp_leaguetype="NCAA%";

//Create basic layout
$str="";
$str.= "<div class='w3-panel w3-theme-d5 w3-text-white w3-round-xxlarge '>";
$str.="<h1><img src=\"images\NCAA_football.png\" alt=\"AAC logo\" width='160' height='95'> Gameplan College Football League Page</h1>";
$str.= "<div class='w3-panel w3-theme w3-round-large '>";
output($str);

#Main content
#Show League Selector
$str= "<br>\n";
$str.= "<div class='nz-form'>";
if (isset($_POST['_cp_mychoice']) && !empty($_POST['_cp_mychoice'])) {
	$_cp_myleague =  substr($_POST['_cp_mychoice'],0,5);
	$_cp_myseason =  substr($_POST['_cp_mychoice'],-4);
	$_cp_myselected = $_POST['_cp_mychoice'];
	$_cp_mychoice = $_POST['_cp_mychoice'];
} elseif (isset($_GET['_cp_mychoice']) && !empty($_GET['_cp_mychoice'])) { 
	$_cp_myleague =  substr($_GET['_cp_mychoice'],0,5);
	$_cp_myseason =  substr($_GET['_cp_mychoice'],-4);
	$_cp_myselected = $_GET['_cp_mychoice'];
	$_cp_mychoice = $_GET['_cp_mychoice'];
} else {
	$_cp_myleague = 'NCAA5';

	//Select maximum season for this league
	//PDO Example with row count
	$_cp_sql = "SELECT MAX(`season`) AS `myseason` FROM `f_games` WHERE `League` ='$_cp_myleague'  ";
	$result = $conn->prepare($_cp_sql); 
	$result->execute(); 
	//Loop through results
	while($row = fetch_row_db($result)){
		$_cp_myseason =  $row['myseason'];
	   }  
	$_cp_myselected = ' - ';
	$_cp_mychoice=$_cp_myleague;
	$_cp_mychoice.=$_cp_myselected;
	$_cp_mychoice.=$_cp_myseason;
}

	#Loop through all leagues and select
	$_cp_sql = "SELECT DISTINCT `league`, `season` FROM `f_games` WHERE `league` LIKE '$_cp_leaguetype' ORDER BY `league` ASC, `season`  DESC";        
	$res = execute_db($_cp_sql, $conn);
	#$str.= "<p>$_cp_sql</p>";
	//Start the form
	$str.= "<form action='index.php?function=show_static_page&id_static_page=18' method='post'>\n";	
	$str.= "<select name='_cp_mychoice'  onchange='this.form.submit()'>\n";
	//Populate the drop down list	
        while($row = fetch_row_db($res)){
			$str.= "<option value='$row[0]-$row[1]'";
			$mytext = $row[0];
			$mytext .= '-';
			$mytext .= $row[1];
			$x = ($mytext == $_cp_myselected) ? " selected ": "";
			$str.= "$x>$row[0] - $row[1]</option>\n";
        } 
	$str.= "</select>\n";	
	$str.= "</form>\n";
	#$str.= "</div>\n";	
#End League Selector	
output($str);

#Start Week Selector
$_cp_sql200 = "SELECT MAX(`week`) AS 'myweek' FROM `f_games` WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason";
$result200 = $conn->prepare($_cp_sql200); 
$result200->execute(); 	
$row = $result200->fetch(PDO::FETCH_ASSOC);
$myweek=$row['myweek'];

//Work out week to display here
if (isset($_POST['myweekchoice']) ) {
		$_cp_mydispweek =  $_POST['myweekchoice'];
		$_cp_myweekchoice =  $_POST['myweekchoice'];
	} elseif (isset($_GET['_cp_myweekchoice']))  { 
	$_cp_mydispweek =  $_GET['myweekchoice'];
	$_cp_myweekchoice =  $_GET['myweekchoice'];
}  else {
	$_cp_mydispweek = $myweek;
	$_cp_myweekchoice =  $myweek;
}


//Display latest results for this season with an option to go back 
	//Display week choice
	#Show League Selector
	$_cp_sql65 = "SELECT DISTINCT `week` FROM `fc_vgames` WHERE `league` LIKE '$_cp_myleague' AND `season` LIKE '$_cp_myseason' ORDER BY `week`  DESC";        
	$res65 = execute_db($_cp_sql65, $conn);
	//Start the form
	$str= "<form  action='index.php?function=show_static_page&id_static_page=18' method='post'>\n";	
	$str.= "<input type='hidden' id='_cp_mychoice' name='_cp_mychoice' value='$_cp_mychoice'>";
	$str.= "<select name='myweekchoice'  onchange='this.form.submit()'>\n";
	//Populate the drop down list	
        while($row = fetch_row_db($res65)){
			$str.= "<option value='$row[0]'";
			$row[0];
			$x = ($_cp_mydispweek == $row[0]) ? " selected ": "";
			$str.= "$x>Week $row[0]</option>\n";
        } 
	$str.= "</select>\n";	
	$str.= "</form>\n";
	$str.="</div>";	
	$str.= "<hr />\n";
#End Week Selector	
output($str);

#If we have week 13 then show winners
$str= "";
#$str= "<div class='w3-round-small w3-theme-l3 w3-center'>\n";
if (13==$myweek) {
$_cp_sql20 = "SELECT `winners`, `wfranchise`, `coach`, `runnersup`, `lfranchise`, `opp_coach` FROM `fc_ncgames` WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason";
#$str.= "<p>$_cp_sql20</p>";
$result20 = $conn->prepare($_cp_sql20); 
$result20->execute(); 	
$row = $result20->fetch(PDO::FETCH_ASSOC);
$win=$row['winners'];
$winc=$row['coach'];
$winf=$row['wfranchise'];
$rup=$row['runnersup'];
$rupf=$row['lfranchise'];
$rupc=$row['opp_coach'];

	$_cp_havewinners=1;
	$str.= "<h2 class='$gpcolour1'>$_cp_myleague National Championship Winner: $win ($winc)\n";

	$_cp_sql22 = "SELECT `won` FROM `fc_seasons` WHERE `franchise`='$winf' AND `season`=$_cp_myseason";
	$result22 = $conn->prepare($_cp_sql22); 
	$result22->execute(); 	
	$row = $result22->fetch(PDO::FETCH_ASSOC);
	$perfect=$row['won'];
	if (11==$perfect){
		$str.= " - <em>Perfect Season!!</em>";
		}
	$str.= "</h2>\n";
	$str.= "<h3 class='$gpcolour1'>Runners up: $rup ($rupc)";
	$str.= "</h3>\n";
	#$str.= "<p>Route 1 - $win</p>\n";
#Otherwise report season not finished
	} else {
	#$str.= "<p>Route 2 - $win</p>\n";
	$str.= "<h2 class='$gpcolour1'>$_cp_myleague National Championship Winner: TBD";
	$str.= "</h2>\n";
	$str.= "<h3 class='$gpcolour1'>Runners up: TBD";
	$str.= "</h3>\n";
	}

//End of season winners
#$str.= "</div>\n";
$str.= "<hr />";
output($str);

//Print results for this week
//Find gametypes (e.g. Regular Season) in this week
$str= "";
#$str= "<div class='w3-panel w3-round-small w3-theme-l3 '>\n";
$_cp_sql56="SELECT `id`, `gametype` FROM `f_gametypes` WHERE `id`<19 ORDER BY `id` ASC";
$result56 = $conn->prepare($_cp_sql56); 
$result56->execute(); 
$number_of_rows56 = $result56->rowCount() ; 
//Loop through gametypes
$str.= "<h3 class='$gpcolour2'><b>$_cp_myleague - Season $_cp_myseason - Week $_cp_mydispweek Results</b></h3>\n";
while ($row56 = fetch_row_db($result56)) {
	#$str.= "<p>Game Type - $row56[1]</p>\n";	
			$_cp_sql55="SELECT c.`team` AS `Team2`, CONCAT(`opp_score`, ' - ',`score`) as `myscore`,  b.`team` AS `Team1` 
						FROM `f_games` a 
										INNER JOIN `fc_franchises` b ON a.`franchise`=b.`franchise`
										INNER JOIN `fc_franchises` c ON a.`opp_franchise`=c.`franchise`
						WHERE a.`league`='$_cp_myleague' AND a.`season`=$_cp_myseason AND  a.`week`=$_cp_mydispweek AND `homeaway`=1 AND `gametype`=$row56[0]
						ORDER BY `gametype` DESC, `Team1` ASC";
		$result55 = $conn->prepare($_cp_sql55); 
		$result55->execute(); 
		$number_of_rows55 = $result55->rowCount() ; 
		#$str.= "<p>Rows for this game type - $number_of_rows55</p>\n";		
		//loop through results
		$i=1;
		#Loop A
		if ($number_of_rows55>0) {
				
				//Put special marker down
				if (13==$_cp_mydispweek AND 8==$row56[0]) {
					$str.= "<div class='w3-container w3-cyan'>";
					$str.= "<h3><b style='font-size: x-large'>New Year's Day</b></h3>";
				}
				if (13==$_cp_mydispweek AND 9==$row56[0]) {
					$str.= "<div class='w3-container w3-cyan'>";
					$str.= "<h3><b style='font-size: x-large'>New Year's Eve</b></h3>";
				}
				if (13==$_cp_mydispweek AND 12==$row56[0]) {
					$str.= "<div class='w3-container w3-cyan'>";
					$str.= "<h3><b style='font-size: x-large'>Christmas Day</b></h3>";
				}
				if (13==$_cp_mydispweek AND 13==$row56[0]) {
					$str.= "<div class='w3-container w3-cyan'>";
					$str.= "<h3><b style='font-size: x-large'>Christmas Eve</b></h3>";
				}				
				//Title

				//Start table
				$str.= "<table class='w3-table w3-striped w3-bordered w3-mobile'>\n";
				if (13<>$_cp_mydispweek){
							$str.= "<tr class='w3-theme-d5'><th class='w3-cell w3-mobile' style='width:15%' >Road&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</th><th style='width:10%' >$row56[1]&nbsp&nbsp&nbsp</th><th style='width:15%' >Home&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</th></tr>\n";
					} else {
							$str.= "<tr><th style='text-align: center;'>$row56[1]</th></tr>\n";
					}
				#Loop B
				while ($row55 = fetch_row_db($result55)) {
					#debug_print('Row55', $row55);
					$i++;
					#$team1=str_pad($row55[0],25,"&nbsp;");
					#$score=str_pad($row55[1],25,"&nbsp;");
					#$team2=str_pad($row55[2],25,"&nbsp;");
					$team1=$row55[0];
					$score=$row55[1];
					$team2=$row55[2];
							#Loop C
							if (($i % 2) == 1) {
									$str.= "<tr class='$gpcolour5'>\n";	
									$str.= "<td class='w3-cell w3-mobile'>$team1</td>\n";
									$str.= "<td class='w3-cell w3-mobile'>$score</td>\n";
									$str.= "<td class='w3-cell w3-mobile'>$team2</td>\n";
									$str.= "</tr>\n";	
							} else {
									$str.= "<tr class='$gpcolour6'>\n";	
									$str.= "<td class='w3-cell w3-mobile'>$team1</td>\n";
									$str.= "<td class='w3-cell w3-mobile'>$score</td>\n";
									$str.= "<td class='w3-cell w3-mobile'>$team2</td>\n";
									$str.= "</tr>\n";	
							}
			//End of Loop B
			}		
			$str.= "</table>\n";
		//End special marker
		if (13==$_cp_mydispweek AND 8==$row56[0]) {
			$str.= "<br>";
			$str.= "</div>";
			$str.= "<br>";
		}
		if (13==$_cp_mydispweek AND 11==$row56[0]) {
			$str.= "<br>";
			$str.= "</div>";
			$str.= "<br>";
		}
		if (13==$_cp_mydispweek AND 12==$row56[0]) {
			$str.= "<br>";
			$str.= "</div>";
			$str.= "<br>";
		}
		if (13==$_cp_mydispweek AND 13==$row56[0]) {
			$str.= "<br>";
			$str.= "</div>";
			$str.= "<br>";
		}					
		//End of Loop A	
		}

//End of weekly results
}
#$str.="</div>";
#$str.= "<br/>";
$str.= "<hr/>";
output($str);

//Standings
$str="";
if ($_cp_mydispweek>11){
	$_cp_mydispweek2=11;
} else {
	$_cp_mydispweek2=$_cp_mydispweek;
}
//Display table of all teams
$_cp_sql2 = "SELECT a.`franchise`,SUM(a.`win`) AS  `wins`, SUM(a.`lose`) AS `losses`, SUM(a.`tie`) AS `ties`, SUM(a.`score`) AS `pfor`, SUM(a.`opp_score`) AS `pagn` , b.`team` 
			 FROM `f_games` a 
			     INNER JOIN `fc_franchises` b on a.`franchise` = b.`franchise` 
			 WHERE a.`league` = '$_cp_myleague' AND a.`season` = $_cp_myseason AND a.`week` <>0 AND a.`week` <=$_cp_mydispweek AND a.`week` <>12  AND a.`week` <> 13    
			 GROUP BY a.`franchise` 
			 ORDER BY `wins` DESC, `ties` DESC, (SUM(a.`score`) -SUM(a.`opp_score`) ) DESC";
			
		#$str.="<p>$_cp_sql2</p>";
		$j=0;
		$result2 = $conn->prepare($_cp_sql2); 
		$result2->execute(); 	
		
		//Title
		$str.= "<h3 class='$gpcolour2'><b>$_cp_myleague - Season $_cp_myseason - Week $_cp_mydispweek Standings</b></h3>\n";
					
				$str.="<table class='w3-table w3-striped w3-bordered w3-mobile'>\n";
				$str.="<tr class='w3-theme-d5'><th>School&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</th><th>Record</th><th class='w3-right-align'>For</th><th class='w3-right-align'>Against</th><th class='w3-right-align'>Difference</th></tr>";
				while($row2 = fetch_row_db($result2)){
				if (0==$row2[3]){
					$_cp_myrecord="$row2[1] - $row2[2]";
				} else {
					$_cp_myrecord="$row2[1] - $row2[2] ($row2[3]t)";
				}
				$mydiff=$row2[4]-$row2[5];
		$_cp_myteam=str_pad($row2[6],30," ");
									if (($j % 2) == 1) {
												$str.="<tr class='w3-theme-l3'><td>$_cp_myteam</td><td>$_cp_myrecord</td><td class='w3-right-align'>$row2[4]</td><td class='w3-right-align'>$row2[5]</td><td class='w3-right-align'>$mydiff</td></tr>";
									} else {
												$str.="<tr class='w3-theme-l4'><td>$_cp_myteam</td><td>$_cp_myrecord</td><td class='w3-right-align'>$row2[4]</td><td class='w3-right-align'>$row2[5]</td><td class='w3-right-align'>$mydiff</td></tr>";
									}
									$j++;

			
		}  
		$str.="</table>";
		$str.="<br />";
		$str.="<br />";

//End of Standings
output($str);


//End of page
$str="</div>";
output($str);

//Start of footer
require_once 'g_footer.php';
?>
