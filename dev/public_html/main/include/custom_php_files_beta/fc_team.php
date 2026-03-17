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
echo "<img  class='w3-right-align' src=\"images\NCAA_logo.png\" alt=\"AAC logo\" width=\"208\" height=\"59\" >";
echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbspGameplan College Football Team Summary Page";
echo "</h1>";
$_cp_leaguetype="NCAA%";
echo "</header>";
//Main yellow section - Div 2
echo "<div class='w3-container $mycolour2'>";
echo "<br />";
//check what has been selected
if (isset($_POST['_cp_myfranchise']) && !empty($_POST['_cp_myfranchise'])) {
	$_cp_myfranchise =  $_POST['_cp_myfranchise'];
	#include_once('f_teamdetails.php');
} elseif (isset($_GET['_cp_myfranchise']) && !empty($_GET['_cp_myfranchise'])) { 
	$_cp_myfranchise =  $_GET['_cp_myfranchise'];
	#include_once('f_teamdetails.php');	
} else {
	if ('localhost' == $host) {
	$_cp_myfranchise = '5008';
	} else {
	$_cp_myfranchise = '5001';	
	}
}
#Show Team Selector
	#Loop through all teams and select
	$i=0;
	$_cp_sql = "SELECT `franchise`,`league`,`team` FROM `fc_franchises` WHERE `ftype`='College' ORDER BY `league`,`city`,`nickname`";        
	$res = execute_db($_cp_sql, $conn);
	echo "<form action='index.php?function=show_static_page&id_static_page=26' method='post'>";	
	echo "<select name='_cp_myfranchise'  onchange='this.form.submit()'>";
	//Populate the drop down list	
        while($row = fetch_row_db($res)){
			echo "<option value='$row[0]'";
			$x = ($_cp_myfranchise == $row[0]) ? " selected ": "";
			echo "$x>$row[1] - $row[2]</option>";
			$i++;
        } 
	echo "</select>";	
	echo "</form>";	
	echo "<br />";

//Start of content
echo "<div class='w3-col' style='width:5%'><p></p></div>";
echo "<div class='w3-col w3-panel $mycolour3 w3-card-4 w3-round-xxlarge w3-centred'  style='width:90%;'>";


#If Franchise is set show table
if ('Not Set'<>$_cp_myfranchise){

	#Loop through all teams and extract details
	$_cp_sql = "SELECT 
	`franchise`, `league`, `conference`, `team`, `coach`, 
	`WinnerYears`, `ConferenceYears`, `G_Winner`, `G_Runnerup`, `GC_Winner`,
	`GC_Runnerup`, `S_Winner`, `S_Runnerup`, `SC_Winner`, `SC_Runnerup`, 
	`B_Winner`, `B_Runnerup`, `BC_Winner`, `BC_Runnerup`, `Perfect`, 
	`PerfectYears`, `MaxWins`,`MaxLosses`,`MaxScored`,`MaxConceded`,
	`ConfWins`,`Rivalry`, `RivalryWins`, `Academy`,`CICWins`,
	`CICYears` FROM `fc_franchises` WHERE  `franchise`= $_cp_myfranchise";     
	#echo "<p>$_cp_sql</p>";   
	$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$_cp_myleague = $row[1];
			$_cp_myconference = $row[2];
			$_cp_myteam = $row[3];
			$_cp_mycoach = $row[4];
			if (''<>$row[5]){
				$_cp_myncyears = $row[5];						
			} else {
				$_cp_myncyears = '-';
			}
			if (''<>$row[6]){
				$_cp_myconfyears = $row[6];						
			} else {
				$_cp_myconfyears = '-';
			}
			
			$_cp_myconfwins = $row[25];		
			$_cp_myncwins = $row[7];
			$_cp_mynclosses = $row[8];
			$_cp_myrosewins = $row[9];
			$_cp_myroselosses = $row[10];
			$_cp_mycottonwins = $row[11];
			$_cp_mycottonlosses = $row[12];
			$_cp_myorangewins = $row[13];
			$_cp_myorangelosses = $row[14];
			$_cp_myhawaiiwins = $row[15];
			$_cp_myhawaiilosses = $row[16];
			$_cp_mymotorwins = $row[17];
			$_cp_mymotorlosses = $row[18];
			$_cp_myperfect = $row[19];
			if (''<>$row[20]){
				$_cp_myperfectyears = $row[20];						
			} else {
				$_cp_myperfectyears = '-';
			}
			$_cp_mywins = $row[21];
			$_cp_mylosses = $row[22];
			$_cp_myscored = $row[23];
			$_cp_myconceded = $row[24];	
			$_cp_myplayoffyears='';	
			$_cp_myrivals = $row[26];
			$_cp_myrivalwins = $row[27];	
			$_cp_myacademy = $row[28];
			$_cp_mycicwins = $row[29];		
			$_cp_myyears = $row[30];				
        } 


$_cp_sql40="SELECT DISTINCT c.`name`, b.`RivalryWins`, b.`team` 
FROM `fc_rivalrygames` a
	INNER JOIN `fc_franchises` b ON a.`opp_franchise`=b.`franchise`
    INNER JOIN `fc_rivalries` c ON a.`Rivalry`=c.`id`
WHERE a.`franchise`=$_cp_myfranchise";
$result40 = $conn->prepare($_cp_sql40); 
$result40->execute();
while($row40 = fetch_row_db($result40)){
	$_cp_myrivalgame = $row40[0];
	$_cp_myrivallosses = $row40[1];
	$_cp_myrivalname = $row40[2];
	}

//Overall cyan box - Div 3
#echo "<div class='w3-panel $mycolour4 w3-round-xxlarge'>";
#echo "<h3></h3>";
//end of Teal box  - Div 3
#echo "</div>";

echo "<br />";
echo "<br />";
echo "<table class='w3-table w3-striped w3-bordered'>";
echo "<caption text-align: left;>$_cp_myleague $_cp_myteam (Coach:$_cp_mycoach)</caption>\n";
echo "<tr class='$mycolour6'>";
echo "<th>National Championships: ($_cp_myncwins)</th>";
echo "<td><span style=\"font-weight: 900\">$_cp_myncyears</span></td>";
echo "</tr>";
echo "<tr class='$mycolour5'>";
echo "<th>Perfect Seasons: ($_cp_myperfect)</th>";
echo "<td>$_cp_myperfectyears";
if (5008==$_cp_myfranchise) {
	echo "<br /><i>Undefeated national championship season in 2015 including one tie.</i>";
}
echo "</td>";
echo "</tr>";
echo "<tr class='$mycolour6'>";
$_cp_myplayoffs=$_cp_myncwins+$_cp_mynclosses+$_cp_myrosewins+$_cp_myroselosses;
echo "<th>National Championship Playoff Appearances:</th>";
echo "<td>$_cp_myplayoffs </td>";
#TODO Add years team appeared
#TODO Add total Seasons
echo "</tr>";
echo "<tr class='$mycolour5'>";
echo "<th>$_cp_myconference Championships: ($_cp_myconfwins)</th>";
echo "<td>$_cp_myconfyears</td>";
echo "</tr>";
echo "<tr class='$mycolour6'>";
echo "<th>National Championship Game Record:</th>";
echo "<td>$_cp_myncwins - $_cp_mynclosses</td>";
echo "</tr>";
echo "<tr class='$mycolour5'>";
echo "<th>Rose Bowl record: </th>";
echo "<td>$_cp_myrosewins - $_cp_myroselosses</td>";
echo "</tr>";
echo "<tr class='$mycolour6'>";
echo "<th>Cotton Bowl record: </th>";
echo "<td>$_cp_mycottonwins - $_cp_mycottonlosses</td>";
echo "</tr>";
echo "<tr class='$mycolour5'>";
echo "<th>Orange Bowl record: </th>";
echo "<td>$_cp_myorangewins - $_cp_myorangelosses</td>";
echo "</tr>";
echo "<tr class='$mycolour6'>";
echo "<th>Hawaii Bowl record: </th>";
echo "<td>$_cp_myhawaiiwins - $_cp_myhawaiilosses</td>";
echo "</tr>";
echo "<tr class='$mycolour5'>";
echo "<th>Motor City Bowl record: </th>";
echo "<td>$_cp_mymotorwins - $_cp_mymotorlosses</td>";
echo "</tr>";
echo "<tr class='$mycolour6'>";
echo "<th>Rivalry: </th>";
echo "<td><em>$_cp_myrivalgame</em> against $_cp_myrivalname : Record: $_cp_myrivalwins - $_cp_myrivallosses</td>";
echo "</tr>";

//Service Acadamies only
if (1==$_cp_myacademy){
echo "<tr class='$mycolour5'>";
echo "<th>Commander in Chief Trophy: ($_cp_mycicwins)</th>";
echo "<td>$_cp_myyears</td>";
echo "</tr>";
echo "<tr class='$mycolour6'>";
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
//Check for multiples
$_cp_sql22="SELECT COUNT(*) FROM `fc_seasons` WHERE `franchise`=$_cp_myfranchise AND `won`='$_cp_mywins'";
$res22 = execute_db($_cp_sql22, $conn);
while($row22 = fetch_row_db($res22)){
	$row22[0];
	$_cp_mywinyears = '';
	if ($row22[0]>1) {
		$_cp_mywinyears = " (x$row22[0])"; 
	}
}
echo "<th>Most games won in a Season:</th>";
echo "<td>$_cp_mywins $_cp_mywinyears</td>";
echo "</tr>";
echo "<tr class='$mycolour6'>";
//Check for multiples
$_cp_sql23="SELECT COUNT(*) FROM `fc_seasons` WHERE `franchise`=$_cp_myfranchise AND `lost`='$_cp_mylosses'";
$res23 = execute_db($_cp_sql23, $conn);
while($row23 = fetch_row_db($res23)){
	$row23[0];
	$_cp_mylossyears = '';
	if ($row23[0]>1) {
		$_cp_mylossyears = " (x$row23[0])"; 
	}
}
echo "<th>Most games lost in a Season:</th>";
echo "<td>$_cp_mylosses $_cp_mylossyears</td>";
echo "</tr>";
echo "<tr class='$mycolour5'>";
echo "<th>Most points scored in a Season:</th>";
echo "<td>$_cp_myscored</td>";
echo "</tr>";
echo "<tr class='$mycolour6'>";
echo "<th>Most points conceded in a Season:</th>";
echo "<td>$_cp_myconceded</td>";
echo "</tr>";
} else {
echo "<tr class='$mycolour5'>";
$_cp_sql2="SELECT MIN(`season`) 
FROM `f_games` WHERE `league`='$_cp_myleague' AND `franchise`=$_cp_myfranchise AND `coach`='$_cp_mycoach'";
$res2 = execute_db($_cp_sql2, $conn);
while($row2 = fetch_row_db($res2)){
	$_cp_mycoachyear = $row2[0];
}
echo "<th>Coach:</th>";
echo "<td>$_cp_mycoach (Since $_cp_mycoachyear)</td>";
echo "</tr>";
echo "<tr class='$mycolour6'>";
//Check for multiples
$_cp_sql22="SELECT COUNT(*) FROM `fc_seasons` WHERE `franchise`=$_cp_myfranchise AND `won`='$_cp_mywins'";
$res22 = execute_db($_cp_sql22, $conn);
while($row22 = fetch_row_db($res22)){
	$row22[0];
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
$_cp_sql23="SELECT COUNT(*) FROM `fc_seasons` WHERE `franchise`=$_cp_myfranchise AND `lost`='$_cp_mylosses'";
$res23 = execute_db($_cp_sql23, $conn);
while($row23 = fetch_row_db($res23)){
	$row23[0];
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
}
echo "</table>";
echo "</p>";

#This season's results
echo "<hr />";
echo "<br />";

$_cp_sql40="SELECT MAX(`season`) FROM `f_games` WHERE `league`='$_cp_myleague'";
$result40 = $conn->prepare($_cp_sql40); 
$result40->execute();
while($row40 = fetch_row_db($result40)){
	$_cp_mycurrentseason = $row40[0];
	}

echo "<table class='w3-table w3-striped w3-bordered'>";
echo "<caption text-align: left;>$_cp_myteam $_cp_mycurrentseason Results</caption>\n";
echo "<tr class='$mycolour7'><th>Week</th><th>Venue</th><th>Opponent</th><th>Score</th><th>Result</th></tr>";



//Find Season results
$_cp_sql3 = "SELECT a.`week`, b.`team`, a.`coach`, a.`score`,a.`homeaway`, c.`team`, a.`opp_coach`, a.`opp_score` 
	FROM `f_games` a
    INNER JOIN `fc_franchises` b ON a.`franchise`=b.franchise
    INNER JOIN `fc_franchises` c ON a.`opp_franchise`=c.franchise
WHERE a.`franchise`=$_cp_myfranchise AND a.`season`=$_cp_mycurrentseason
ORDER BY `week` DESC;";
#echo "<pre>$_cp_sql3</pre>";
				$res3 = execute_db($_cp_sql3, $conn);
					$i=1;
					while($row3 = fetch_row_db($res3)){
						//Allocate venue
						if (13==$row3[0]) {
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



#Season by Season records
echo "<hr />";
echo "<br />";

echo "<table class='w3-table w3-striped w3-bordered'>";
echo "<caption text-align: left;>Season by Season Records</caption>\n";
echo "<tr class='$mycolour7'><th>Season</th><th>Coach</th><th>Record</th><th>Points</th></tr>";



				//Find Season by season records
				$_cp_sql3 = "SELECT `season`, `coach`, `won`, `lost`, `tie`, `scored`, `conceded`
											FROM `fc_seasons` 
											WHERE `franchise`= $_cp_myfranchise
											ORDER BY `season` DESC";
#echo "<pre>$_cp_sql3</pre>";
				$res3 = execute_db($_cp_sql3, $conn);
					$i=1;
					while($row3 = fetch_row_db($res3)){
								
								//Check for Conference Wins
								$_cp_sql4 = "SELECT ConferenceYears FROM fc_franchises WHERE franchise=$_cp_myfranchise AND ConferenceYears LIKE '%$row3[0]%'";
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
								$_cp_sql4 = "SELECT WinnerYears FROM fc_franchises WHERE franchise=$_cp_myfranchise AND WinnerYears LIKE '%$row3[0]%'";
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
echo "<p>* indicates won National Championship, italics indicate won Conference.</p>";

}
//End of content
echo "</div>";

#Close yellow box - 2
echo "<br />";
echo "</div>";
echo "<div class='w3-col' style='width:5%'><p></p></div>";

echo "<footer class='w3-container $mycolour5'>";
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
