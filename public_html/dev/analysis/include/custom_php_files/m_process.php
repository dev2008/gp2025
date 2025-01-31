<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

$time_start = microtime(true);

//Set up headers etc
$str="<br /><div class='nz-card'>";
$str.="<div class='w3-container $mycolour6'>";
$str.="<div class='w3-pale-green'>";
$str.="<h1>Process Play By Play</h1>";
$str.="</div>";
$str.= "<div class='w3-panel $mycolour4 nz-card w3-round-xxlarge'>";
$str.="<h2>Finding unprocessed records</h3>";
output($str);

#Select records not yet processed
$mysql="SELECT  `a_id`, `a_type`, `a_level`, `a_league`, `a_season`, `a_week`, `a_minutes`, `a_seconds`, `a_poss`, `a_off`, `a_def`, `a_field`, `a_down`, `a_distance`, `a_form`, `a_ocall`, `a_dcall`, `a_yards`, `a_team1`, `a_team2`, `a_text`, 0 as `a_td`, 0 as `a_first`, 0 as `a_fumble`, 0 as `a_intercept`, 0 as `a_penalty`, 0 as `a_sack`, 0 as `a_hurry`, 0 as `a_blitzpickup`, 0 as `a_blitznopickup`, 0 as `a_safety`, 'NC' as `a_security`, 0 as `a_incomplete`, 0 as `a_peno`, 0 as `a_pend`, 0 as `a_bado`, 0 as `a_twodowns`, 0 as `a_situationno`, 0 as `a_negative`, NULL as `playtype`
		FROM `n_pbptemp` 
		WHERE `a_id` NOT IN (SELECT `a_id` FROM `n_playbyplay` WHERE 1) 
		LIMIT 250";
#$mysql="SELECT  `a_id`, `a_type`, `a_level`, `a_league`, `a_season`, `a_week`, `a_minutes`, `a_seconds`, `a_poss`, `a_off`, `a_def`, `a_field`, `a_down`, `a_distance`, `a_form`, `a_ocall`, `a_dcall`, `a_yards`, `a_team1`, `a_team2`, `a_text`, 0 as `a_td`, 0 as `a_first`, 0 as `a_fumble`, 0 as `a_intercept`, 0 as `a_penalty`, 0 as `a_sack`, 0 as `a_hurry`, 0 as `a_blitzpickup`, 0 as `a_blitznopickup`, 0 as `a_safety`, 'NC' as `a_security`, 0 as `a_incomplete`, 0 as `a_peno`, 0 as `a_pend`, 0 as `a_bado`, 0 as `a_twodowns`, 0 as `a_situationno`, 0 as `a_negative`, NULL as `playtype`
#		FROM `n_pbptemp` 
#		WHERE `a_league`='NFLAR' AND `a_season` < 2035 AND `a_id` NOT IN (SELECT `a_id` FROM `n_playbyplay` WHERE 1) ";

#echo "<p>$mysql</p>";
$result = $conn->prepare($mysql); 
$result->execute(); 
$_cp_array = $result->fetchAll(PDO::FETCH_ASSOC);
$number_of_rows = $result->rowCount() ; 
$number_of_rowsp=number_format($number_of_rows);
$str="<div class='w3-container w3-teal'>\n";
$str.="<h4>$number_of_rowsp unprocessed records retrieved</h4>";
output($str);
$x=0;
$y=-1;#$name = $stmt->fetchColumn();
$z=1;

while($x < $number_of_rows) {
    #echo "<p>".$x ." - ". $_cp_array[$x]['a_id'] ."</p> \n";
    
    if ($x % 25 == 0) {
		$str='Processed ';
		$str.=$x;
		$str.=' records / ';
		output($str);
	};

	//TD	
	if ($_cp_array[$x]['a_yards']==$_cp_array[$x]['a_field']) {
	  $_cp_array[$x]['a_td']=1;
	} else {
		$_cp_array[$x]['a_td']=0;
	}

	//1st
	if ($_cp_array[$x]['a_yards']==$_cp_array[$x]['a_distance']) {
	  $_cp_array[$x]['a_first']=1;
	} else {
		$_cp_array[$x]['a_first']=0;
	}

	//Fumble
	if (strpos($_cp_array[$x]['a_text'],'fumble') !== false) {
		$_cp_array[$x]['a_fumble']=1;
		$_cp_array[$x]['a_negative']=1;
	}
	
		//Interception
	if (strpos($_cp_array[$x]['a_text'],'intercept') !== false) {
		$_cp_array[$x]['a_intercept']=1;
		$_cp_array[$x]['a_yards']=0;
	}
	
		//Penalty
	if (strpos($_cp_array[$x]['a_text'],'penalty') !== false) {
		if(strpos($_cp_array[$x]['a_text'],'offence') !== false) {
		$_cp_array[$x]['a_peno']=1;
		$_cp_array[$x]['a_negative']=1;
		} else {
		$_cp_array[$x]['a_pend']=1;
		
		} 
	}	

	//Sack
	if (strpos($_cp_array[$x]['a_text'],'sacked') !== false) {
		$_cp_array[$x]['a_sack']=1;
		$_cp_array[$x]['a_negative']=1;
	}

	//Hurry
	if (strpos($_cp_array[$x]['a_text'],'hurried') !== false) {
		$_cp_array[$x]['a_hurry']=1;
		
	}
	
	//Blitz
	if (strpos($_cp_array[$x]['a_text'],'blitz') !== false) {
		if (strpos($_cp_array[$x]['a_text'],'not') !== false) {
		$_cp_array[$x]['a_blitznopickup']=1;
		
		} else {
		$_cp_array[$x]['a_blitzpickup']=1;
		
		} 
	}	

	//Safety
	if (strpos($_cp_array[$x]['a_text'],'safety') !== false) {
		$_cp_array[$x]['a_safety']=1;
		$myfield=$_cp_array[$x]['a_field'];
		$mysafeyards=$myfield-100;
		$_cp_array[$x]['a_yards']=$mysafeyards;
	}

	//Incomplete
	if (strpos($_cp_array[$x]['a_text'],'incomplete') !== false) {
		$_cp_array[$x]['a_incomplete']=1;
		$_cp_array[$x]['a_yards']=0;
		$_cp_array[$x]['a_negative']=1;

	}
	
		#Negative
	if ($_cp_array[$x]['a_yards']<1 AND "P"<>$_cp_array[$x]['a_form'] AND "F"<>$_cp_array[$x]['a_form'] AND "X"<>$_cp_array[$x]['a_form']){
	$_cp_array[$x]['a_negative']=1;
}
	#Assign play type
	$mysql3="SELECT b.`playtype` FROM `n_pbptemp` a INNER JOIN `n_playtypes` b ON a.`a_ocall` = b.`play` WHERE `a_ocall` = '".$_cp_array[$x]['a_ocall']."'	";
	$result3 = $conn->prepare($mysql3); 
	$result3->execute(); 	
	$myname = $result3->fetchColumn();

	//Insert into the database
	$mysql2="INSERT INTO `n_playbyplay` (`a_id`, `a_type`, `a_level`, `a_league`, `a_season`, `a_week`, `a_minutes`, `a_seconds`, `a_poss`, `a_off`, `a_def`, `a_field`, `a_down`, `a_distance`, `a_form`, `a_ocall`, `a_dcall`, `a_yards`, `a_team1`, `a_team2`, `a_text`, `a_td`, 
	`a_first`, `a_fumble`, `a_intercept`, `a_penalty`, `a_sack`, `a_hurry`, `a_blitzpickup`, `a_blitznopickup`, `a_safety`, `a_security`, `a_incomplete`, `a_peno`, `a_pend`, `a_bado`, `a_twodowns`, `a_situationno`, `a_negative`, `a_playtype`) 
	VALUES (".$_cp_array[$x]['a_id'].", '".$_cp_array[$x]['a_type']."', '".$_cp_array[$x]['a_level']."', '".$_cp_array[$x]['a_league']."', ".$_cp_array[$x]['a_season'].", ".$_cp_array[$x]['a_week'].", ".$_cp_array[$x]['a_minutes'].", ".$_cp_array[$x]['a_seconds'].", '".$_cp_array[$x]['a_poss']."', '".$_cp_array[$x]['a_off']."', '".$_cp_array[$x]['a_def']."', ".$_cp_array[$x]['a_field'].", ".$_cp_array[$x]['a_down'].", ".$_cp_array[$x]['a_distance'].", '".$_cp_array[$x]['a_form']."', '".$_cp_array[$x]['a_ocall']."', '".$_cp_array[$x]['a_dcall']."', ".$_cp_array[$x]['a_yards'].", '".$_cp_array[$x]['a_team1']."', '".$_cp_array[$x]['a_team2']."', '".$_cp_array[$x]['a_text']."', 
	".$_cp_array[$x]['a_td'].", ".$_cp_array[$x]['a_first'].", ".$_cp_array[$x]['a_fumble'].", ".$_cp_array[$x]['a_intercept'].", ".$_cp_array[$x]['a_penalty'].", ".$_cp_array[$x]['a_sack'].", ".$_cp_array[$x]['a_hurry'].", ".$_cp_array[$x]['a_blitzpickup'].", ".$_cp_array[$x]['a_blitznopickup'].", ".$_cp_array[$x]['a_safety'].", '".$_cp_array[$x]['a_security']."', ".$_cp_array[$x]['a_incomplete'].", ".$_cp_array[$x]['a_peno'].", ".$_cp_array[$x]['a_pend'].", ".$_cp_array[$x]['a_bado'].", ".$_cp_array[$x]['a_twodowns'].", ".$_cp_array[$x]['a_situationno'].", ".$_cp_array[$x]['a_negative'].",'$myname')";
	#echo "<p>$mysql2</p>";

	$result2 = $conn->prepare($mysql2); 
	$result2->execute(); 

	//Increment counter
	$x++;
}

$str='Processed ';
$str.=$x;
$str.=' records! <br>';
output($str);

//Punts
$_cp_sql = "UPDATE `n_playbyplay` SET `a_yards`=0 WHERE `a_form`='P' AND (`a_ocall`='PN' OR `a_ocall`='PC' OR `a_ocall`='PC');";
$res = execute_db($_cp_sql, $conn);
$number_of_rows = $res->rowCount() ; 
$number_of_rowsp=number_format($number_of_rows);
$number_of_rowstotal=$number_of_rows;
$str="<h2>Fixing Punts</h2>";
$str.="<h2>$number_of_rowsp records fixed</h3>";
$str.="<br />\n";
output($str);

//Fix wrong yardage

$_cp_sql = "UPDATE `n_playbyplay` SET `a_yards`=`a_field` WHERE (`a_text` LIKE '%gain%' AND `a_text`  LIKE '%break%' AND `a_text` NOT LIKE '%no gain%' AND `a_text` NOT LIKE '%loss%' AND `a_text` NOT LIKE '%intercept%' AND `a_text` NOT LIKE '%fumble%' AND `a_text` NOT LIKE '%penalty%' AND `a_text` NOT LIKE '%foul%' AND `a_text` NOT LIKE '%punt%' AND `a_down` <>4 AND `a_minutes`<>'29' AND `a_minutes` <>59 AND `a_yards` <=0);";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$number_of_rows = $result->rowCount() ; 
$str="<p>Fixed yardage for $number_of_rows records!</p>\n";
output($str);

//Flag update has been run
$_cp_sql="INSERT INTO `g_updated` (`updated_id`, `updated_when`) VALUES (NULL, current_timestamp());";
nz_pdo($_cp_sql,$conn);


$str="</div>\n";
$str.="<br />";
$str.="</div>\n";
output($str);

//Start of footer
require_once 'g_footer.php';


?>
