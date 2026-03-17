<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');

$time_start = microtime(true);

echo "<h1>Gameplan Football Team Summary Page</h1>";

// print all the server vars
#$_cp_arr = get_defined_vars();
#print_r($_cp_arr["_SERVER"]);
#print_r($_GET);  // for all GET variables
#print_r($_POST); // for all POST variables
#index.php?function=show_static_page&id_static_page=3

if (isset($_POST['_cp_myfranchise']) && !empty($_POST['_cp_myfranchise'])) {
	$_cp_myfranchise =  $_POST['_cp_myfranchise'];
	#include_once('f_teamdetails.php');
} elseif (isset($_GET['_cp_myfranchise']) && !empty($_GET['_cp_myfranchise'])) { 
	$_cp_myfranchise =  $_GET['_cp_myfranchise'];
	#include_once('f_teamdetails.php');	
} else {
	$_cp_myfranchise = '2016';
}

#Show Team Selector
	#Loop through all teams and select
	$i=0;
	$_cp_sql = "SELECT `franchise`,`league`,`team` FROM `fp_franchises` WHERE `ftype`='Pro' ORDER BY `league`,`city`,`nickname`";        
	$res = execute_db($_cp_sql, $conn);
	//Start the form
#	echo "<script type='text/javascript'>";
	echo "<form action='index.php?function=show_static_page&id_static_page=16' method='post'>";	
	echo "<select name='_cp_myfranchise'  onchange='this.form.submit()'>";
	//Populate the drop down list	
        while($row = fetch_row_db($res)){
			echo "<option value='$row[0]'";
			$x = ($_cp_myfranchise == $row[0]) ? " selected ": "";
			echo "$x>$row[1] - $row[2]</option>";
			$i++;
        } 
	echo "</select>";	

#	echo "<br />";

#	echo "<input type='submit' />";
	echo "</form>";	
#	echo "</script>";
	echo "<br />";

#If Franchise is set show table
if ('Not Set'<>$_cp_myfranchise){
	#Loop through all teams and extract details
	$_cp_sql = "SELECT `franchise`,`league`,`conference`,`division`,`team`,`coach`,`Winner`,`Runnerup`,`ChampionshipW`,`ChampionshipL`,`DivisionW`,`Wildcard`,`MaxWins`,`MaxLosses`,`MaxScored`,`MaxConceded`,`WinnerYears`,`DivisionYears`,`ConferenceYears`,`WildcardYears` FROM `fp_franchises` WHERE (`ftype`='Pro' OR `ftype`='College') AND `franchise`= $_cp_myfranchise";     
	#echo "<p>$_cp_sql</p>";   
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
        
#echo "<h3>$_cp_myleague $_cp_myteam ($_cp_mycoach)</h3>";
echo "<table style='width:80%'  class='w3-table w3-striped w3-bordered'>";
echo "<tr>";
echo "<th>Superbowl Champions: ($_cp_mybowlwins)</th>";
echo "<td>$_cp_mywinneryears</td>";
echo "</tr>";
echo "<tr>";
echo "<th>$_cp_myconference Championships: ($_cp_mychampwins)</th>";
echo "<td>$_cp_myconfyears</td>";
echo "</tr>";
echo "<tr>";
echo "<th>$_cp_mydivision Winners: ($_cp_mydivisionwins)</th>";
echo "<td>$_cp_mydivisionyears</td>";
echo "</tr>";
echo "<tr>";
echo "<th>Wildcards: ($_cp_mywildcards)</th>";
echo "<td>$_cp_mywildcardyears</td>";
echo "</tr>";
echo "<tr>";
$_cp_sql2="SELECT MIN(`season`) 
FROM `f_games` WHERE `league`='$_cp_myleague' AND `franchise`=$_cp_myfranchise AND `coach`='$_cp_mycoach'";
$res2 = execute_db($_cp_sql2, $conn);
while($row2 = fetch_row_db($res2)){
	$_cp_mycoachyear = $row2[0];
}
echo "<th>Coach:</th>";
echo "<td>$_cp_mycoach (Since $_cp_mycoachyear)</td>";
echo "</tr>";
echo "<tr>";
echo "<th>Superbowl record:</th>";
echo "<td>$_cp_mybowlwins - $_cp_mybowllosses</td>";
echo "</tr>";
echo "<tr>";
echo "<th>$_cp_myconference Championship Game Record:";
echo "</th>";
echo "<td>$_cp_mychampwins - $_cp_mychamplosses</td>";
echo "</tr>";
echo "<tr>";
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
echo "<tr>";
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
echo "<tr>";
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
echo "<tr>";
echo "<th>Most points scored in a Season:</th>";
echo "<td>$_cp_myscored</td>";
echo "</tr>";
echo "<th>Most points conceded in a Season:</th>";
echo "<td>$_cp_myconceded</td>";
echo "</tr>";
echo "</table>";
echo "</p>";

#Season by Season records
echo "<hr />";
echo "<br />";

echo "<table style='width:60%'  class='w3-table w3-striped w3-bordered'>";
echo "<tr><th>Season</th><th>Coach</th><th>Record</th><th>Points</th></tr>";


				//Find Season by season records
				$_cp_sql3 = "SELECT `season`, `coach`, `won`, `lost`, `tie`, `scored`, `conceded`
											FROM `fp_seasons` 
											WHERE `franchise`= $_cp_myfranchise
											ORDER BY `season` DESC";
				#echo "<pre>$_cp_sql3</pre>";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
								
								$_cp_sql4 = "SELECT DivisionYears FROM fp_franchises WHERE franchise=$_cp_myfranchise AND DivisionYears LIKE '%$row3[0]%'";
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
								
								//Check for SB Wins
								$_cp_sql4 = "SELECT WinnerYears FROM fp_franchises WHERE franchise=$_cp_myfranchise AND WinnerYears LIKE '%$row3[0]%'";
								$res4 = execute_db($_cp_sql4, $conn);
								$row_cnt = $res4->rowCount();
								#echo "<p>Count - $row_cnt</p>";
								if  (1==$row_cnt){
									$_cp_em3=" <span style=\"font-weight: 900\">*</span> ";
								} else {
									$_cp_em3="";
								}
								
						echo "<tr><td>$_cp_em$row3[0]$_cp_em3$_cp_em2</td><td>$_cp_em$row3[1]$_cp_em2</td><td>$_cp_em$row3[2]-$row3[3]$_cp_em2";
						if ($row3[4]==1){
							echo " ($_cp_em$row3[4] tie$_cp_em2)";
							} elseif ($row3[4]>1){ 
									echo " ($_cp_em$row3[4] ties$_cp_em2)";
								}
						echo "</td><td>$_cp_em$row3[5]$_cp_em2-$_cp_em$row3[6]$_cp_em2</td></tr>";

					}


echo "</table>";
echo "</p>";
echo "<p>* indicates won Superbowl, italics indicate won Division.</p>";

}

$time_end = microtime(true);
$time = $time_end - $time_start;
if (1==$debug_mode){
echo "<p>Processed in ";
echo  round($time,2) . "s";
#echo ", found $i records.";
echo "</p>";
}

?>
