<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');
include_once('include/custom_php_files/g_functions.php');
ini_set('display_errors', '1');

$_cp_havewinners=0;
echo "<br />";
#Open div 1
echo "<div class='w3-card-4' style='width:90%;'>";
//Header
echo "<header class='w3-container $mycolour1'>";
echo "<h1>";
echo "<img  class='w3-right-align' src=\"images\NFL_logo.png\" alt=\"NFL logo\" width=\"91\" height=\"125\" >";
echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbspGameplan Pro Football League Page";
echo "</h1>";
$_cp_leaguetype="NFL%";
echo "</header>";
//End of Header
//Open div 2
#pgetpost_print();
echo "<div class='w3-container $mycolour2'>";
echo "<br />";
echo "<br />";

#Show League Selector
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
	$_cp_myleague = 'NFLAR';

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
	#echo "<p>$_cp_sql</p>";
	//Start the form
	echo "<form action='index.php?function=show_static_page&id_static_page=15' method='post'>\n";	
	echo "<select name='_cp_mychoice'  onchange='this.form.submit()'>\n";
	//Populate the drop down list	
        while($row = fetch_row_db($res)){
			echo "<option value='$row[0]-$row[1]'";
			$mytext = $row[0];
			$mytext .= '-';
			$mytext .= $row[1];
			$x = ($mytext == $_cp_myselected) ? " selected ": "";
			echo "$x>$row[0] - $row[1]</option>\n";
        } 
	echo "</select>\n";	
	echo "</form>\n";	
#End League Selector	

#Start Week Selector
//NZLeagueChange Start
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
	echo "<br />\n";
	$_cp_sql65 = "SELECT DISTINCT `week` FROM `vg_progames` WHERE `league` LIKE '$_cp_myleague' AND `season` LIKE '$_cp_myseason' ORDER BY `week`  DESC";        
	$res65 = execute_db($_cp_sql65, $conn);
	//Start the form
	echo "<form action='index.php?function=show_static_page&id_static_page=15' method='post'>\n";	
	echo "<input type='hidden' id='_cp_mychoice' name='_cp_mychoice' value='$_cp_mychoice'>";
	echo "<select name='myweekchoice'  onchange='this.form.submit()'>\n";
	//Populate the drop down list	
        while($row = fetch_row_db($res65)){
			echo "<option value='$row[0]'";
			$row[0];
			$x = ($_cp_mydispweek == $row[0]) ? " selected ": "";
			echo "$x>Week $row[0]</option>\n";
        } 
	echo "</select>\n";	
	echo "</form>\n";	
	echo "<hr />\n";
#End Week Selector	

//start of content
//div 3 open and close
echo "<div class='w3-col' style='width:5%'><p></p></div>\n";
//div 4 open 
echo "<div class='w3-col w3-panel $mycolour3 w3-card-4 w3-round-xxlarge w3-centred'  style='width:90%;'>\n";
//Start of season winners
//div 5 open 
echo "<div class='w3-panel $mycolour4 w3-card-4 w3-round-xxlarge'>";

if (20==$myweek OR 99==$myweek) {
	$_cp_sql20 = "SELECT `AFC_Champions`,`AFC_Coach`,`franchise`,`NFC_Champions`,`NFC_Coach`,`opp_franchise`,`win` FROM `fp_bowlgames` WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason";
	$result20 = $conn->prepare($_cp_sql20); 
	$result20->execute(); 	
	$row = $result20->fetch(PDO::FETCH_ASSOC);
	$afc=$row['AFC_Champions'];
	$afcp=$row['AFC_Coach'];
	$afcf=$row['franchise'];
	$nfc=$row['NFC_Champions'];
	$nfcp=$row['NFC_Coach'];
	$nfcf=$row['opp_franchise'];
	$win=$row['win'];

/*
	if (1==$debug_mode){
		print_r($row);
	}
*/

	if ("AFC"==$win) {

		echo "<h3 class='$mycolour11'><b>$_cp_myleague Superbowl Winner: $afc ($afcp)";
		$_cp_sql22 = "SELECT `won` FROM `fp_seasons` WHERE `franchise`='$afcf' AND `season`=$_cp_myseason";
		$result22 = $conn->prepare($_cp_sql22); 
		$result22->execute(); 	
		$row = $result22->fetch(PDO::FETCH_ASSOC);
		$perfect=$row['won'];
		if (16==$perfect){
			echo " - <em>Perfect Season!!</em>";
		}
		echo "</b></h3>";
		echo "<h4 class='$mycolour11'><b>NFC Conference Champions: $nfc ($nfcp)";
		echo "</b></h4>";
		
	} else {
		echo "<h4 class='$mycolour11'><b>AFC Conference Champions: $afc ($afcp)</b></h4>";
		echo "<h3 class='$mycolour11'><b>$_cp_myleague Superbowl  Winner: $nfc ($nfcp)";
		$_cp_sql22 = "SELECT `won` FROM `fp_seasons` WHERE `franchise`='$nfcf' AND `season`=$_cp_myseason";
		$result22 = $conn->prepare($_cp_sql22); 
		$result22->execute(); 	
		$row = $result22->fetch(PDO::FETCH_ASSOC);
		$perfect=$row['won'];
		if (16==$perfect){
			echo " - <em>Perfect Season!!</em>";
		}
		echo "</b></h3>";
	}
	
} else {
	echo "<h3 class='$mycolour11'><b>AFC Champions: TBD";
	echo "</b></h3>";
	echo "<h3 class='$mycolour11'><b>NFC Champions: TBD";
	echo "</b></h3>";
}

//End of season winners
//Div 5 close
echo "</div>\n";
#echo "<hr />";
	
//Add loop for gametypes
$_cp_sql56="SELECT `id`, `gametype` FROM `f_gametypes` WHERE `id`>19 ORDER BY `id` DESC";
$result56 = $conn->prepare($_cp_sql56); 
$result56->execute(); 
$number_of_rows56 = $result56->rowCount() ; 
//Loop through ametypes
while ($row56 = fetch_row_db($result56)) {
$_cp_sql55="SELECT c.`team` AS `Team2`, CONCAT(`opp_score`, ' - ',`score`) as `myscore`,  b.`team` AS `Team1` 
FROM `f_games` a 
	INNER JOIN `fp_franchises` b ON a.`franchise`=b.`franchise`
   	INNER JOIN `fp_franchises` c ON a.`opp_franchise`=c.`franchise`
WHERE a.`league`='$_cp_myleague' AND a.`season`=$_cp_myseason AND  a.`week`=$_cp_mydispweek AND `homeaway`=1 AND `gametype`=$row56[0]
ORDER BY `gametype` DESC, a.`franchise` ASC";
#echo "<p>$_cp_sql55</p>";
$result55 = $conn->prepare($_cp_sql55); 
$result55->execute(); 
$number_of_rows55 = $result55->rowCount() ; 
if ($number_of_rows55>0) {
$mygametype=$row56[1];
if ($_cp_mydispweek==17 AND $row56[0]==35){
$mygametype='Wild Card Round';
}
if ($_cp_mydispweek==18 AND $row56[0]==35){
$mygametype='Divisional Round';
}
if ($_cp_mydispweek==19 AND $row56[0]==35){
$mygametype='Championship Games';
}
$i=0;
//Open up table
echo "<table class='w3-table w3-striped w3-bordered w3-mobile'>\n";
echo "<caption class='$mycolour11' text-align: left;><b>$_cp_myleague - Season $_cp_myseason - Week $_cp_mydispweek Results</b></caption>\n";
echo "<caption class='$mycolour11'  text-align: left;><b>$mygametype</caption></b>\n";
//check for Superbowl week
				if (20<>$_cp_mydispweek){
							echo "<tr class='$mycolour7'><th class='w3-cell w3-mobile' style='width:15%' >Road&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</th><th style='width:10%' >Score&nbsp&nbsp&nbsp</th><th style='width:15%' >Home&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</th></tr>\n";
					} else {
							echo "<tr class='$mycolour7'><th class='w3-cell w3-mobile' style='width:15%' >&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</th><th style='width:10%' >Score&nbsp&nbsp&nbsp</th><th style='width:15%' >&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</th></tr>\n";
					}
//Loop through results for this week for this specific gamtype
while ($row = fetch_row_db($result55)) {
$team1=$row[0];
$score=$row[1];
$team2=$row[2];
							
							if (($i % 2) == 1) {
									echo "<tr class='$mycolour5'>\n";	
									echo "<td class='w3-cell w3-mobile'>$team1</td>\n";
									echo "<td class='w3-cell w3-mobile'>$score</td>\n";
									echo "<td class='w3-cell w3-mobile'>$team2</td>\n";
									echo "</tr>\n";	
							} else {
									echo "<tr class='$mycolour6'>\n";	
									echo "<td class='w3-cell w3-mobile'>$team1</td>\n";
									echo "<td class='w3-cell w3-mobile'>$score</td>\n";
									echo "<td class='w3-cell w3-mobile'>$team2</td>\n";
									echo "</tr>\n";	
							}
$i++;
}
echo "</table>\n";
echo "<br />\n";
echo "<hr />\n";
}

//End loop for gametypes
}

#Standings
if ($_cp_mydispweek>16){ 
					$_cp_mystandweek=16;
 } else {
				$_cp_mystandweek=$_cp_mydispweek;
}

#Get divisions in order
$_cp_sql7="SELECT `conf_name`, `conf_div`, `conf_logo`, `conf_alt` FROM `fp_conferences` WHERE `conf_league`='$_cp_myleague' ORDER BY `conf_id` ASC";
$result7 = $conn->prepare($_cp_sql7); 
$result7->execute(); 
$i=0;
while($divisions = fetch_row_db($result7)){
#Loop through Divisions
		$_cp_myconf = $divisions[0];
		$_cp_myconf .= " ";
		$_cp_myconf .= $divisions[1];
		$_cp_myconflogo = $divisions[2];
		$_cp_myconflogoalt = $divisions[3];
		echo "<img  class='w3-right-align' src=\"images\\$_cp_myconflogo\" alt=\"		$_cp_myconflogoalt\"  width=\"160\" height=\"95\">";
		echo "<br />";
		echo "<table class='w3-table w3-striped w3-bordered'>\n";
		echo "<caption class='$mycolour11'><b>$_cp_myleague Season $_cp_myseason - Week $_cp_mystandweek Standings</caption></b>\n";
		//don't like doing this but it seems to be the only way!
		echo "<col style=\"width: 350px;\" />";
		echo "<col style=\"width: 75px;\" />";
		echo "<col style=\"width: 20px;\" />";
		echo "<col style=\"width: 20px;\" />";
		echo "<col style=\"width: 20px;\" />";
		
		echo "<tr class='$mycolour7'><th>Team</th><th class='w3-left-align'>Record</th><th class='w3-right-align'>For</th><th class='w3-right-align'>Against</th><th class='w3-right-align'>Difference</th></tr>\n";
		#Print standings for each
				$_cp_sql2 = "SELECT b.`division` AS `myconf`,a.`franchise`,SUM(a.`win`) AS  `wins`, SUM(a.`lose`) AS `losses`, SUM(a.`tie`) AS `ties`, SUM(a.`score`) AS `pfor`, SUM(a.`opp_score`) AS `pagn` , b.`team` 
						FROM `f_games` a 
							INNER JOIN `fp_franchises` b on a.`franchise` = b.`franchise` 
						WHERE a.`league` = '$_cp_myleague' AND a.`season` = $_cp_myseason AND b.`division` = '$_cp_myconf'  AND a.`week` <>0 AND a.`week` <>17  AND a.`week` <> 18  AND a.`week` <> 19 AND a.`week` <> 20  
						GROUP BY `division`,a.`franchise` 
						ORDER BY `division`,`wins` DESC, `ties` DESC, (SUM(a.`score`) -SUM(a.`opp_score`) ) DESC";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 	
				while($row2 = fetch_row_db($result2)){
					$myteam=$row2[7];
					/*
					$mylength=strlen($myteam);
					$mynewlength=25-$mylength;
					$mynewtotal=$mylength+$mynewlength;
					#echo "<p>Adding $mynewlength to $mylength to make $mynewtotal</p>";
					for($y = 1; $y < $mynewlength; $y++) {
							$myteam.="&nbsp";
					}
					*/
					$mydiff=$row2[5]-$row2[6];
							if (0==$row2[4]){
									$_cp_myrecord="$row2[2] - $row2[3]";
								} else {
									$_cp_myrecord="$row2[2] - $row2[3] ($row2[4]t)";
							}	
												if (($i % 2) == 1) {
													echo "<tr class='$mycolour5'><td>$myteam</td><td>$_cp_myrecord</td><td class='w3-right-align'>$row2[5]</td><td class='w3-right-align'>$row2[6]</td><td class='w3-right-align'>$mydiff</td></tr>\n";
												} else {
													echo "<tr class='$mycolour6'><td>$myteam</td><td>$_cp_myrecord</td><td class='w3-right-align'>$row2[5]</td><td class='w3-right-align'>$row2[6]</td><td class='w3-right-align'>$mydiff</td></tr>\n";
												}	
												$i++;
			}	
		
		#Print all time winners
		#End of Divisions loop
		echo "</table>\n";
		echo "<br />\n";
}



#End of standings div

echo "</div>\n";



#Close yellow box - 2
echo "<br />\n";
echo "</div>\n";
echo "<div class='w3-col' style='width:5%'><p></p></div>\n";
echo "<footer class='w3-container $mycolour1'>";
$_cp_sql1 = "SELECT `modification_time` FROM `f_games` WHERE 1 ORDER BY `modification_time` DESC LIMIT 1";$res1 = execute_db($_cp_sql1, $conn);
        while($row = fetch_row_db($res1)){
			$_cp_updated = $row[0];
        }

#Footer div - Div 4
echo "<div class='w3-right-align'>System was last updated on ";
$changeDate = date("d-m-Y H:i", strtotime($_cp_updated));
echo "$changeDate";
#End footer div - Div 4
echo "</div>";

echo "</footer>";
#End of card class - Div 1
echo "</div>";

?>
