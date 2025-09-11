<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');
include_once('include/custom_php_files/g_functions.php');
ini_set('display_errors', '1');
echo "<br />";
#Div Card Class - 1
echo "<div class='w3-card-4' style='width:90%;'>";
//Header
echo "<header class='w3-container $mycolour1'>";
echo "<h1>";
echo "<img  class='w3-right-align' src=\"images\NFL_logo.png\" alt=\"NFL logo\" width=\"91\" height=\"125\" >";
echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbspGameplan Pro Football Team Summary Page";
echo "</h1>";
$_cp_leaguetype="NCAA%";
echo "</header>";
//Main section - Div 2
echo "<div class='w3-container $mycolour2'>";
echo "<br />";
if (isset($_POST['_cp_myfranchise']) && !empty($_POST['_cp_myfranchise'])) {
	$_cp_myfranchise =  $_POST['_cp_myfranchise'];
} elseif (isset($_GET['_cp_myfranchise']) && !empty($_GET['_cp_myfranchise'])) { 
	$_cp_myfranchise =  $_GET['_cp_myfranchise'];
} else {
	if ('localhost' == $host) {
	$_cp_myfranchise = '2015';
} else {
	$_cp_myfranchise = '2001';
	}
}

#Show Team Selector
	$i=0;
	$_cp_sql = "SELECT `franchise`,`league`,`team` FROM `fp_franchises` WHERE `ftype`='Pro' ORDER BY `league`,`city`,`nickname`";        
	$res = execute_db($_cp_sql, $conn);
	echo "<form action='index.php?function=show_static_page&id_static_page=16' method='post'>";	
	echo "<select name='_cp_myfranchise'  onchange='this.form.submit()'>";
        while($row = fetch_row_db($res)){
			echo "<option value='$row[0]'";
			$x = ($_cp_myfranchise == $row[0]) ? " selected ": "";
			echo "$x>$row[1] - $row[2]</option>";
			$i++;
        } 
	echo "</select>";	
	echo "</form>";	
	echo "<br />";



//start of content
echo "<div class='w3-col' style='width:5%'><p></p></div>\n";
echo "<div class='w3-col w3-panel $mycolour3 w3-card-4 w3-round-xxlarge w3-centred'  style='width:90%;'>\n";


#If Franchise is set show table
if ('Not Set'<>$_cp_myfranchise){
	#Loop through all teams and extract details
	$_cp_sql = "SELECT `franchise`,`league`,`conference`,`division`,`team`,`coach`,`Winner`,`Runnerup`,`ChampionshipW`,`ChampionshipL`,`DivisionW`,`Wildcard`,`MaxWins`,`MaxLosses`,
	`MaxScored`,`MaxConceded`,`WinnerYears`,`DivisionYears`,`ConferenceYears`,`WildcardYears` 
	FROM `fp_franchises` 
	WHERE (`ftype`='Pro' OR `ftype`='College') AND `franchise`= $_cp_myfranchise";     
#	echo "<p>$_cp_sql</p>";   
	$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$_cp_myleague = $row[1];
			$_cp_myconference = $row[2];
			$_cp_mydivision = $row[3];
			$_cp_myteam = $row[4];
			$_cp_mycoach = $row[5];
			$_cp_mybowlwins = $row[6];
			$_cp_mybowllosses = $row[7];
			$_cp_mychampwins = $row[8];
			$_cp_mychamplosses = $row[9];
			$_cp_mydivisionwins = $row[10];
			$_cp_mywildcards = $row[11];
			$_cp_mywins = $row[12];
			$_cp_mylosses = $row[13];
			$_cp_myscored = $row[14];
			$_cp_myconceded = $row[15];
			$_cp_mywinneryears = $row[16];						
			$_cp_mydivisionyears = $row[17];						
			$_cp_myconfyears = $row[18];						
			$_cp_mywildcardyears = $row[19];						
        } 
        
echo "<br />";

//Title
echo "<div class='w3-panel w3-blue'>";
echo "<h1 class='w3-text-orange' style='text-shadow:1px 1px 0 #444'>";
echo "<b>$_cp_myleague $_cp_myteam (Coach:$_cp_mycoach)</b></h1>";
echo "</div>";


echo "<table class='w3-table w3-striped w3-bordered'>";

echo "<tr class='$mycolour6'>";
echo "<th>Superbowl Champions: ($_cp_mybowlwins)</th>";
echo "<td>$_cp_mywinneryears</td>";
echo "</tr>";
echo "<tr class='$mycolour5'>";
echo "<th>$_cp_myconference Championships: ($_cp_mychampwins)</th>";
echo "<td>$_cp_myconfyears</td>";
echo "</tr>";
echo "<tr class='$mycolour6'>";
echo "<th>$_cp_mydivision Winners: ($_cp_mydivisionwins)</th>";
echo "<td>$_cp_mydivisionyears</td>";
echo "</tr>";
echo "<tr class='$mycolour5'>";
echo "<th>Wildcards: ($_cp_mywildcards)</th>";
echo "<td>$_cp_mywildcardyears</td>";
echo "</tr>";
echo "<tr class='$mycolour6'>";
//TODO Fix this as it will not pick up leavers and rejoiners
$_cp_sql2="SELECT MIN(`season`) 
FROM `f_games` WHERE `league`='$_cp_myleague' AND `franchise`=$_cp_myfranchise AND `coach`='$_cp_mycoach'";
$res2 = execute_db($_cp_sql2, $conn);
while($row2 = fetch_row_db($res2)){
	$_cp_mycoachyear = $row2[0];
}
echo "<th>Coach:</th>";
echo "<td>$_cp_mycoach (Since $_cp_mycoachyear)</td>";
echo "</tr>";
echo "<tr class='$mycolour5'>";
echo "<th>Superbowl record:</th>";
echo "<td>$_cp_mybowlwins - $_cp_mybowllosses</td>";
echo "</tr>";
echo "<tr class='$mycolour6'>";
echo "<th>$_cp_myconference Championship Game Record:";
echo "</th>";
echo "<td>$_cp_mychampwins - $_cp_mychamplosses</td>";
echo "</tr>";
echo "<tr class='$mycolour5'>";
echo "<th>Playoff Appearances:</th>";
$_cp_myplayoffs=$_cp_mydivisionwins+$_cp_mywildcards;
echo "<td>$_cp_myplayoffs ($_cp_mydivisionwins Divisions and $_cp_mywildcards Wildcards)</td>";
#TODO Add years team appeared
#TODO Add total Seasons
echo "</tr>";
#echo "<tr>";
#echo "<th>Most games won in a Season:</th>";
#echo "<td>$_cp_mywins</td>";
#echo "</tr>";
#echo "<tr>";
#echo "<th>Most games lost in a Season:</th>";
#echo "<td>$_cp_mylosses</td>";
#echo "</tr>";
echo "<tr class='$mycolour6'>";
//Check for multiples
$_cp_sql22="SELECT COUNT(*) FROM `fp_seasons` WHERE `franchise`=$_cp_myfranchise AND `won`='$_cp_mywins'";
$res22 = execute_db($_cp_sql22, $conn);
while($row22 = fetch_row_db($res22)){
	$_cp_mywinyears = '';
	if ($row22[0]>1) {
		$_cp_mywinyears = " (x$row22[0])"; 
	}
}
echo "<th>Most games won in a Season:</th>";
echo "<td>$_cp_mywins $_cp_mywinyears</td>";
echo "</tr>";
echo "<tr class='$mycolour5'>";
//Check for multiples
$_cp_sql23="SELECT COUNT(*) FROM `fp_seasons` WHERE `franchise`=$_cp_myfranchise AND `lost`='$_cp_mylosses'";
$res23 = execute_db($_cp_sql23, $conn);
while($row23 = fetch_row_db($res23)){
	$_cp_mylossyears = '';
	if ($row23[0]>1) {
		$_cp_mylossyears = " (x$row23[0])"; 
	}
}
echo "<th>Most games lost in a Season:</th>";
echo "<td>$_cp_mylosses $_cp_mylossyears</td>";
echo "</tr>";
echo "<tr class='$mycolour6'>";
echo "<th>Most points scored in a Season:</th>";
echo "<td>$_cp_myscored</td>";
echo "</tr>";
echo "<tr class='$mycolour5'>";
echo "<th>Most points conceded in a Season:</th>";
echo "<td>$_cp_myconceded</td>";
echo "</tr>";
echo "</table>";
echo "</p>";
echo "<hr />";

$_cp_sql40="SELECT MAX(`season`) FROM `f_games` WHERE `league`='$_cp_myleague'";
$result40 = $conn->prepare($_cp_sql40); 
$result40->execute();
while($row40 = fetch_row_db($result40)){
	$_cp_mycurrentseason = $row40[0];
	}

//Title
echo "<div class='w3-panel w3-blue'>";
echo "<h1 class='w3-text-orange' style='text-shadow:1px 1px 0 #444'>";
echo "<b>$_cp_myteam $_cp_mycurrentseason Results</b></h1>";
echo "</div>";

echo "<table class='w3-table w3-striped w3-bordered'>";
echo "<tr class='$mycolour7'><th>Week</th><th>Venue</th><th>Opponent</th><th>Score</th><th>Result</th></tr>";

//Find Season results
$_cp_sql3 = "SELECT a.`week`, b.`team`, a.`coach`, a.`score`,a.`homeaway`, c.`team`, a.`opp_coach`, a.`opp_score` 
	FROM `f_games` a
    INNER JOIN `fp_franchises` b ON a.`franchise`=b.franchise
    INNER JOIN `fp_franchises` c ON a.`opp_franchise`=c.franchise
WHERE a.`franchise`=$_cp_myfranchise AND a.`season`=$_cp_mycurrentseason
ORDER BY `week` DESC;";
#echo "<pre>$_cp_sql3</pre>";
				$res3 = execute_db($_cp_sql3, $conn);
					$i=1;
					while($row3 = fetch_row_db($res3)){
						//Allocate venue
						if (20==$row3[0]) {
							$venue='Neutral';
						} else {
							if (1==$row3[4]){
								$venue='Home';
							} else {
								$venue='Road';
							}
						}	
						//Allocate result
						if ($row3[3]>$row3[7]){
							$myresult='Win';
						} else {
							if ($row3[3]<$row3[7]){
								$myresult='Loss';
							} else {
							$myresult='Tie';
						}
						}	
						if (($i % 2) <> 1) {		
							echo "<tr class='$mycolour5'><td>$row3[0]</td><td>$venue</td><td>$row3[5] ($row3[6])</td><td>$row3[3]-$row3[7]</td><td>$myresult</td></tr>";
						} else {
							echo "<tr class='$mycolour6'><td>$row3[0]</td><td>$venue</td><td>$row3[5] ($row3[6])</td><td>$row3[3]-$row3[7]</td><td>$myresult</td></tr>";
						}
						$i++;
					}
echo "</table>";
echo "</p>";
echo "<hr />";


#Season by Season records
//Title
echo "<div class='w3-panel w3-blue'>";
echo "<h1 class='w3-text-orange' style='text-shadow:1px 1px 0 #444'>";
echo "<b>Season by Season Records</b></h1>";
echo "</div>";

echo "<table class='w3-table w3-striped w3-bordered'>";
echo "<tr class='$mycolour7'><th>Season</th><th>Coach</th><th>Record</th><th>Points</th></tr>";

//Find season records 
				//Find Season by season records
				$_cp_sql3 = "SELECT `season`, `coach`, `won`, `lost`, `tie`, `scored`, `conceded`
											FROM `fp_seasons` 
											WHERE `franchise`= $_cp_myfranchise
											ORDER BY `season` DESC";
#echo "<pre>$_cp_sql3</pre>";
				$res3 = execute_db($_cp_sql3, $conn);
					$i=1;
					while($row3 = fetch_row_db($res3)){
								
								//Check for Conference Wins
								$_cp_sql4 = "SELECT ConferenceYears FROM fp_franchises WHERE franchise=$_cp_myfranchise AND ConferenceYears LIKE '%$row3[0]%'";
								$res4 = execute_db($_cp_sql4, $conn);
								$row_cnt = $res4->rowCount();
								#echo "<p>Count - $row_cnt</p>";
								if  (1==$row_cnt){
									$_cp_em="<em>";
									$_cp_em2="</em>";
								} else {
									$_cp_em="";
									$_cp_em2="";						
								}

								//Check for NC Wins
								$_cp_sql4 = "SELECT WinnerYears FROM fp_franchises WHERE franchise=$_cp_myfranchise AND WinnerYears LIKE '%$row3[0]%'";
								$res4 = execute_db($_cp_sql4, $conn);
								$row_cnt = $res4->rowCount();
								#echo "<p>Count - $row_cnt</p>";
								if  (1==$row_cnt){
									$_cp_em3=" <span style=\"font-weight: 900\">*</span> ";
								} else {
									$_cp_em3="";
								}
						if (($i % 2) <> 1) {		
							echo "<tr class='$mycolour5'><td>$_cp_em$row3[0]$_cp_em3$_cp_em2</td><td>$_cp_em$row3[1]$_cp_em2</td><td>$_cp_em$row3[2]-$row3[3]$_cp_em2";
						} else {
							echo "<tr class='$mycolour6'><td>$_cp_em$row3[0]$_cp_em3$_cp_em2</td><td>$_cp_em$row3[1]$_cp_em2</td><td>$_cp_em$row3[2]-$row3[3]$_cp_em2";							
						}
						$i++;
						
						if ($row3[4]==1){
							echo " ($_cp_em$row3[4] tie$_cp_em2)";
							} elseif ($row3[4]>1){ 
									echo " ($_cp_em$row3[4] ties$_cp_em2)";
								}
						echo "</td><td>$_cp_em$row3[5]$_cp_em2-$_cp_em$row3[6]$_cp_em2</td></tr>";

					}

echo "</table>";
echo "</p>";
echo "<p>* indicates won Superbowl, italics indicate won Conference.</p>";










//no Franchise
}
//End of content
echo "</div>";

#Close yellow box - 2
echo "<br />";
echo "</div>";
echo "<div class='w3-col' style='width:5%'><p></p></div>";
//Footer
echo "<footer class='w3-container $mycolour1'>";
$_cp_sql1 = "SELECT `modification_time` FROM `f_games` WHERE 1 ORDER BY `modification_time` DESC LIMIT 1";
$res1 = execute_db($_cp_sql1, $conn);
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
