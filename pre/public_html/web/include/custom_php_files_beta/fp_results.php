<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');
include_once('include/custom_php_files/g_functions.php');
ini_set('display_errors', '1');
#pgetpost_print();
//Header
function isMobileDevice() { 
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo 
|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i" 
, $_SERVER["HTTP_USER_AGENT"]); 
} 
if(isMobileDevice()){ 
    #echo "<p></p>Mobile Browser Detected</p>"; 
    $mymobile=1;
} 
else { 
    #echo "<p>Mobile Browser Not Detected</p>"; 
    $mymobile=0;
} 
echo "<h1>Gameplan Pro Football Results Page</h1>";
echo "<p><img src=\"images\NFL_logo.png\" alt=\"NFL logo\" width=\"91\" height=\"125\" ></p>";
$_cp_leaguetype="NFL%";

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
$_cp_sql200 = "SELECT MAX(`week`) AS 'myweek' FROM `f_games` WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason";
$result200 = $conn->prepare($_cp_sql200); 
$result200->execute(); 	
$row = $result200->fetch(PDO::FETCH_ASSOC);
$myweek=$row['myweek'];

	

//Work out week to display here
#if (isset($_POST['myweekchoice']) && !empty($_POST['myweekchoice'])) {
	if (isset($_POST['myweekchoice']) ) {
	$_cp_mydispweek =  $_POST['myweekchoice'];
	$_cp_myweekchoice =  $_POST['myweekchoice'];
#} elseif (isset($_GET['_cp_myweekchoice']) && !empty($_GET['_cp_myweekchoice'])) { 
	} elseif (isset($_GET['_cp_myweekchoice']))  { 
	$_cp_mydispweek =  $_GET['myweekchoice'];
	$_cp_myweekchoice =  $_GET['myweekchoice'];
}  else {
	$_cp_mydispweek = $myweek;
	$_cp_myweekchoice =  $myweek;
}

#Show League Selector
	#Loop through all leagues and select
	$_cp_sql = "SELECT DISTINCT `league`, `season` FROM `f_games` WHERE `league` LIKE '$_cp_leaguetype' ORDER BY `league` ASC, `season`  DESC";        
	$res = execute_db($_cp_sql, $conn);
	//Start the form
	echo "<form action='index.php?function=show_static_page&id_static_page=32' method='post'>\n";	
	echo "<input type='hidden' id='_cp_myweekchoice' name='_cp_myweekchoice' value='$_cp_myweekchoice'>\n";
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
	echo "<br />\n";

#If we have week 20 then show winners
//Display latest results for this season with an option to go back 
echo "<h3>Week $_cp_mydispweek Results</h3>";
	//Display week choice
	#Show League Selector
	$_cp_sql65 = "SELECT DISTINCT `week` FROM `vg_progames` WHERE `league` LIKE '$_cp_myleague' AND `season` LIKE '$_cp_myseason' ORDER BY `week`  DESC";        
	$res65 = execute_db($_cp_sql65, $conn);
	//Start the form
	echo "<form action='index.php?function=show_static_page&id_static_page=32' method='post'>\n";	
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
	echo "<br />\n";


//Add loop for gametypes
$_cp_sql56="SELECT `id`, `gametype` FROM `f_gametypes` WHERE `id`>19 ORDER BY `id` DESC";
#$_cp_sql56="SELECT `id`, `gametype` FROM `f_gametypes` WHERE `id`=35 ORDER BY `id` DESC";
$result56 = $conn->prepare($_cp_sql56); 
$result56->execute(); 
$number_of_rows56 = $result56->rowCount() ; 
while ($row56 = fetch_row_db($result56)) {

if ($mymobile==1) {
$_cp_sql55="SELECT c.`nickname` AS `Team2`, CONCAT(`opp_score`, ' - ',`score`) as `myscore`,  b.`nickname` AS `Team1` 
FROM `f_games` a 
	INNER JOIN `fp_franchises` b ON a.`franchise`=b.`franchise`
   	INNER JOIN `fp_franchises` c ON a.`opp_franchise`=c.`franchise`
WHERE a.`league`='$_cp_myleague' AND a.`season`=$_cp_myseason AND  a.`week`=$_cp_mydispweek AND `homeaway`=1 AND `gametype`=$row56[0]
ORDER BY `gametype` DESC, `Team1` ASC";
#echo "<p>$_cp_sql55</p>";
$result55 = $conn->prepare($_cp_sql55); 
$result55->execute(); 
$number_of_rows55 = $result55->rowCount() ; 
if ($number_of_rows55>0) {
//Loop through results
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

echo "<div class='w3-aqua w3-center' style='width:40% '>$mygametype</caption>";
while ($row = fetch_row_db($result55)) {
$team1=str_pad($row[0],25," ");
$score=str_pad($row[1],25," ");
$team2=str_pad($row[2],25," ");

$team1=$row[0];
$score=$row[1];
$team2=$row[2];

echo "<div class='w3-display-container w3-blue' style='height:30px'>";
  echo "<div class='w3-display-left'>$team1</div>";
  echo "<div class='w3-display-middle'>$score</div>";
  echo "<div class='w3-display-right'>$team2</div>";
echo "</div>";
}
echo "</div>";
echo "<br />";
}
} else {
$_cp_sql55="SELECT c.`team` AS `Team2`, CONCAT(`opp_score`, ' - ',`score`) as `myscore`,  b.`team` AS `Team1` 
FROM `f_games` a 
	INNER JOIN `fp_franchises` b ON a.`franchise`=b.`franchise`
   	INNER JOIN `fp_franchises` c ON a.`opp_franchise`=c.`franchise`
WHERE a.`league`='$_cp_myleague' AND a.`season`=$_cp_myseason AND  a.`week`=$_cp_mydispweek AND `homeaway`=1 AND `gametype`=$row56[0]
ORDER BY `gametype` DESC, `Team1` ASC";
#echo "<p>$_cp_sql55</p>";
$result55 = $conn->prepare($_cp_sql55); 
$result55->execute(); 
$number_of_rows55 = $result55->rowCount() ; 
if ($number_of_rows55>0) {
//Loop through results
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

echo "<div class='w3-aqua w3-center' style='width:60% '>$mygametype</caption>";
while ($row = fetch_row_db($result55)) {
$team1=str_pad($row[0],25," ");
$score=str_pad($row[1],25," ");
$team2=str_pad($row[2],25," ");

$team1=$row[0];
$score=$row[1];
$team2=$row[2];

echo "<div class='w3-display-container $mycolour4' style='height:30px'>";
  echo "<div class='w3-display-left'>$team1</div>";
  echo "<div class='w3-display-middle'>$score</div>";
  echo "<div class='w3-display-right'>$team2</div>";
echo "</div>";
}
echo "</div>";
echo "<br />";	
}
}

//End loop for gametypes
}

?>
