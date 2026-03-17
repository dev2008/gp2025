<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require_once 'g_functions.php';
require_once 'mydatabase.php';


$str="<br /><div class='w3-card-4'>";
$str.="<header class='w3-container w3-blue-gray'>";
$str.="<h1>Team Analysis</h1>";
$str.="</header>";
output($str);

$str="<div class='w3-container w3-pale-blue'>";
output($str);
#Team Selector
echo "<br />";
if (isset($_POST['_cp_myfranchise']) && !empty($_POST['_cp_myfranchise'])) {
	$_cp_myfranchise =  $_POST['_cp_myfranchise'];
} elseif (isset($_GET['_cp_myfranchise']) && !empty($_GET['_cp_myfranchise'])) { 
	$_cp_myfranchise =  $_GET['_cp_myfranchise'];
} else {
	$_cp_myfranchise = '2016';
}
	$i=0;
	$_cp_sql = "SELECT `franchise`,`league`,`team` FROM `fp_franchises` WHERE `ftype`='Pro' ORDER BY `league`,`city`,`nickname`";        
	$res = execute_db($_cp_sql, $conn);
	echo "<form action='index.php?function=show_static_page&id_static_page=20' method='post'>";	
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

//Find current Coach
$_cp_sql = "SELECT `team`,`coach`,`league`,`abbr` FROM `fp_franchises` WHERE `franchise`=$_cp_myfranchise";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$number_of_rows = number_format($result->rowCount() ); 
$str="<div class='w3-container w3-teal'>\n";
$mycount=0;	
while($row = fetch_row_db($result)){
	$_cp_myteam=$row[0];
	$_cp_mycoach=$row[1];
	$_cp_myleague=$row[2];
	$_cp_myabbr=$row[3];
}
$str.="<h2>$_cp_myteam coached by $_cp_mycoach"; 
//Work out how long this Coach has been in charge
//Note this will miss edge case of returning Coach
$_cp_sql2="SELECT MIN(`season`) 
FROM `f_games` WHERE `league`='$_cp_myleague' AND `franchise`=$_cp_myfranchise AND `coach`='$_cp_mycoach'";
$res2 = execute_db($_cp_sql2, $conn);
while($row2 = fetch_row_db($res2)){
	$_cp_mycoachstartyear = $row2[0];
}
$str.=" since $_cp_mycoachstartyear";
//Check current year (only go back 10 years)
$_cp_sql2="SELECT MAX(`season`) 
FROM `f_games` WHERE `league`='$_cp_myleague' AND `franchise`=$_cp_myfranchise AND `coach`='$_cp_mycoach'";
$res2 = execute_db($_cp_sql2, $conn);
while($row2 = fetch_row_db($res2)){
	$_cp_mymaxcoachyear = $row2[0];
}
$str.=" (now $_cp_mymaxcoachyear)</h3>";
//end of output
output($str);

//Add form to chose paramaters

//Now we can do analysis of his games
//Maximum of 10 years 
if (($_cp_mymaxcoachyear-($_cp_mycoachstartyear+1))>10){
	$_cp_mycoachyears=$_cp_mymaxcoachyear-10;
} else {
	$_cp_mycoachyears=($_cp_mycoachstartyear+1);
}
$str="<h4>(Taking account of seasons since $_cp_mycoachyears).</h4>";
output($str);

$_cp_sql2="SELECT `a_form`,`a_ocall`,COUNT(`a_ocall`) AS `Tot` 
											FROM `n_playbyplay` 
											WHERE `a_season`>2020 AND `a_off`='$_cp_myabbr' AND `a_form`<>'F' AND `a_form`<>'P' AND `a_form`<>'X'
											GROUP BY `a_form`, `a_ocall` 
											ORDER BY `a_form` ASC, `Tot` DESC;";
$res2 = execute_db($_cp_sql2, $conn);
$j=0;
	echo "<table class='w3-table w3-striped w3-bordered w3-mobile'>\n";
	echo "<caption text-align: left;>Play Summary</caption>\n";
	echo "<tr class='$mycolour7'><th>Formation</th><th>Play</th><th>No.</th></tr>";
while($row2 = fetch_row_db($res2)){
	if ($row2[2]>5){
									if (($j % 2) == 1) {
												echo "<tr class='$mycolour5'><td>$row2[0]</td><td>$row2[1]</td><td>$row2[2]</td></tr>";
									} else {
												echo "<tr class='$mycolour6'><td>$row2[0]</td><td>$row2[1]</td><td>$row2[2]</td></tr>";
									}
									$j++;
	}
}
echo "</table>";
echo "<br />";

//Start of footer
$str="</div>\n";
$str.="<br />";
$str.="</div>\n";
$str.="<footer class='w3-container w3-cyan'>\n";
output($str);
$i=number_format($number_of_rows);
$time_end = microtime(true);
$time=$time_end - $time_start;
$str="<h2  class='w3-yellow'>Job complete - processed $i records in ";
$str.=round($time,2) . "s";
$str.="</h2>\n";
output($str);

$_cp_sql1 = "SELECT `modification_time` FROM `f_games` WHERE 1 ORDER BY `modification_time` DESC LIMIT 1";
$res1 = execute_db($_cp_sql1, $conn);
while($row = fetch_row_db($res1)){
			$_cp_updated = $row[0];
}
$str="<div class='w3-right-align'>System was last updated on ";
$changeDate = date("d-m-Y H:i", strtotime($_cp_updated));
$str.= "$changeDate";

$str.= "</div>\n";
$str.="</footer>\n";
$str.="</div>\n";        

output($str);
?>
