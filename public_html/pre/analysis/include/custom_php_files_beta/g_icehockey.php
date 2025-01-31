<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require_once 'ih_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

$_cp_myname=$_SESSION['logged_user_infos_ar']["username_user"];
$arr=get_defined_vars();
#nz_debug($arr,"Debug:");


echo "<br />";
echo "<div class='w3-card-4'>";

#Header
echo "<div class='w3-container $mycolour6'>";

$_cp_dbcheck=substr($db_name,-3);
if ('DEV' == $_cp_dbcheck){
		echo "<h1>Welcome to Gameplan Analysis $_cp_myname</h1>";
		echo "<h2>You are logged on to ** DEV **</h2>";
		echo "<br>";
	} elseif ('PRE' == $_cp_dbcheck) {
		echo "<h1>Welcome to Gameplan Analysis $_cp_myname</h1>";
		echo "<h2>You are logged on to ** PRE-PROD **</h2>";
		echo "<br>";
	} else {
				echo "<h1>Welcome to Gameplan Analysis</h1>";
		echo "<br>";
	}

$str="<div class='w3-container $mycolour4 w3-round-xxlarge'>\n";
$str.="<h2>Slapshot Summary</h2>";
output($str);

#Start the table
echo "<table class='w3-table w3-striped w3-bordered w3-mobile'>\n";
echo "<tr><th class='$mycolour15'>League</th><th class='$mycolour15'>Season</th><th class='$mycolour15'>Week</th><th class='$mycolour15'>Players</th><th class='$mycolour16'>Draftees</th><th class='$mycolour16'>Scouted</th><th class='$mycolour17' >Batting Stats</th><th class='$mycolour17' >Pitching Stats</th></tr>";
echo "<tr><th class='$mycolour15'>&nbsp;</th><th class='$mycolour15'>&nbsp;</th><th class='$mycolour15'>&nbsp;</th><th class='$mycolour15'><em>(all time)</em></th><th class='$mycolour16'><em>(this Season)</em><th class='$mycolour16'><em>(this Season)</em></th><th class='$mycolour17'><em>(this Season)</em></th><th class='$mycolour17'><em>(this Season)</em></th></tr>";
#First establish what Leagues we have
$_cp_sql="SELECT DISTINCT `league` FROM `g_turnsummary` WHERE `league` LIKE 'NH%'";
$myleaguearray=nz_pdo_array($_cp_sql,$conn);
//Get details for each League
$j=0;
foreach ($myleaguearray as $myleagues){
	$myleague=$myleagues['league'];
	#Work out latest Season
	$_cp_sql="SELECT MAX(`season`) FROM `g_turnsummary` WHERE `league`=?";
	$_cp_val=$myleague;
	$_cp_season=nz_pdo_single($_cp_sql,$_cp_val,$conn);
	#Work out latest Week
	$_cp_sql="SELECT MAX(`week`) FROM `g_turnsummary` WHERE `season`=$_cp_season AND `league`=?";
	$_cp_val=$myleague;
	$_cp_week=nz_pdo_single($_cp_sql,$_cp_val,$conn);
	#Retrieve number of players 
	$_cp_sql="SELECT COUNT(*) FROM `ih_players` WHERE `f_league`=?";
	$_cp_val=$myleague;
	$_cp_players=nz_pdo_single($_cp_sql,$_cp_val,$conn);
	//Retrieve number of goaltending stats
	//First check for this week 
	$_cp_sql="SELECT COUNT(*) FROM `ih_statsg` WHERE `season`=$_cp_season  AND `week`=$_cp_week AND `league`=?";
	$_cp_val=$myleague;
	$_cp_bstats=nz_pdo_single($_cp_sql,$_cp_val,$conn);
	//If none for this week check for this season
	if (0==$_cp_bstats){
		$_cp_sql="SELECT COUNT(*) FROM `ih_statsg` WHERE `season`=$_cp_season  AND `league`=?";
		$_cp_val=$myleague;
		$_cp_bstats=nz_pdo_single($_cp_sql,$_cp_val,$conn);
	}
	#Retrieve number of skater stats 
	$_cp_sql="SELECT COUNT(*) FROM `ih_statss` WHERE `season`=$_cp_season  AND `week`=$_cp_week AND `league`=?";
	$_cp_val=$myleague;
	$_cp_pstats=nz_pdo_single($_cp_sql,$_cp_val,$conn);
	if (0==$_cp_pstats){
		$_cp_sql="SELECT COUNT(*) FROM `ih_statsg` WHERE `season`=$_cp_season  AND `league`=?";
		$_cp_val=$myleague;
		$_cp_pstats=nz_pdo_single($_cp_sql,$_cp_val,$conn);
	}	
	
	#Retrieve number of draftees 
	$_cp_sql="	SELECT COUNT(*) 
	FROM `ih_dplayers` a
		INNER JOIN `ih_drafts` b ON a.d_id=b.d_id 
	WHERE b.d_season=$_cp_season AND b.d_league=?";
	$_cp_val=$myleague;
	$_cp_draftees=nz_pdo_single($_cp_sql,$_cp_val,$conn);

	#Retrieve number of draftees scouted
	$_cp_sql="	SELECT COUNT(*) 
	FROM `ih_dplayers` a
		INNER JOIN `ih_drafts` b ON a.d_id=b.d_id 
	WHERE b.d_season=$_cp_season AND b.d_league=? and a.`scouted`='Y'";
	$_cp_val=$myleague;
	$_cp_scouted=nz_pdo_single($_cp_sql,$_cp_val,$conn);

	
	if (($j % 2) == 1) {
		echo "<tr class='$mycolour1'><td>$myleague</td><td>$_cp_season</td><td>$_cp_week</td><td>$_cp_players</td><td>$_cp_draftees</td><td>$_cp_scouted</td><td>$_cp_bstats</td><td>$_cp_pstats</td></tr>";
} else {
		echo "<tr class='$mycolour7'><td>$myleague</td><td>$_cp_season</td><td>$_cp_week</td><td>$_cp_players</td><td>$_cp_draftees</td><td>$_cp_scouted</td><td>$_cp_bstats</td><td>$_cp_pstats</td></tr>";
}
$j++;
}
#End the table
echo "</table>";


/*
$_cp_sql="SELECT COUNT(*) FROM `ih_dplayers` WHERE ?=1";
$_cp_val='1';
$_cp_tot=nz_pdo_single($_cp_sql,$_cp_val,$conn);

$_cp_sql="SELECT COUNT(DISTINCT `d_league`) FROM `ih_drafts` WHERE 1=?";
$_cp_val='1';
$_cp_tot2=nz_pdo_single($_cp_sql,$_cp_val,$conn);

$_cp_sql="SELECT DISTINCT `d_league` FROM `ih_drafts` a INNER JOIN `ih_dplayers` b ON a.d_id=b.d_id WHERE 1=1 ORDER BY `d_league` ASC";
$_cp_res=nz_pdo_array($_cp_sql,$conn);
$_cp_league1=$_cp_res[0]['d_league'];
$_cp_league2=$_cp_res[1]['d_league'];


//TODO - cater for more than 2 Leagues!
$str="<h3>We have information on $_cp_tot draftees from $_cp_tot2"; 
if (1==$_cp_tot2) {
	$str.="	League ($_cp_league1)"; 
} else {
	$str.="	Leagues ($_cp_league1 and $_cp_league2)"; 
}
$str.=".</h3>";
$str.="<br>";
output($str);


$_cp_sql="SELECT COUNT(*) FROM `ih_dplayers` WHERE `dp_skill1` IS NOT NULL AND ?=1";
$_cp_val='1';
$_cp_tot=nz_pdo_single($_cp_sql,$_cp_val,$conn);

$_cp_sql="SELECT COUNT(DISTINCT `d_league`) FROM `ih_drafts` a INNER JOIN `ih_dplayers` b ON a.d_id=b.d_id WHERE  `dp_skill1` IS NOT NULL AND 1=? ORDER BY `d_league` ASC";
$_cp_val='1';
$_cp_tot2=nz_pdo_single($_cp_sql,$_cp_val,$conn);

$_cp_sql="SELECT DISTINCT `d_league` FROM `ih_drafts` a INNER JOIN `ih_dplayers` b ON a.d_id=b.d_id WHERE  `dp_skill1` IS NOT NULL AND 1=1 ORDER BY `d_league` ASC";
$_cp_res=nz_pdo_array($_cp_sql,$conn);
$_cp_league1=$_cp_res[0]['d_league'];
$_cp_league2=$_cp_res[1]['d_league'];

if ($_cp_tot>0) {
	$str="<h3>We have scouted $_cp_tot players from $_cp_tot2 ";
	if (1==$_cp_tot2) {
		$str.="	League ($_cp_league1)"; 
	} else {
		$str.="	Leagues ($_cp_league1 and $_cp_league2)"; 
	}
	$str.=".</h3>";
	$str.="<br>";
	output($str);
}

//Report on stats
$_cp_sql="SELECT COUNT(*) FROM `ih_statsg` WHERE ?=1";
$_cp_val='1';
$_cp_tot=nz_pdo_single($_cp_sql,$_cp_val,$conn);

$_cp_sql="SELECT COUNT(DISTINCT `league`) FROM `ih_statsg` WHERE ?=1;";
$_cp_val='1';
$_cp_tot2=nz_pdo_single($_cp_sql,$_cp_val,$conn);

$_cp_sql="SELECT DISTINCT `league` FROM `ih_statsg` ORDER BY `league` DESC;";
$_cp_res=nz_pdo_array($_cp_sql,$conn);
$_cp_league1=$_cp_res[0]['league'];

$str="<h3>We have batting stats for $_cp_tot players from ";
if (1==$_cp_tot2) {
		$str.="	League ($_cp_league1)"; 
	} else {
		$str.="	Leagues ($_cp_league1 and $_cp_league2)"; 
	}
$str.=".</h3>";
$str.="<br>";
output($str);

$_cp_sql="SELECT COUNT(*) FROM `ih_statss`  WHERE ?=1";
$_cp_val='1';
$_cp_tot=nz_pdo_single($_cp_sql,$_cp_val,$conn);

$_cp_sql="SELECT COUNT(DISTINCT `league`) FROM `ih_statss` WHERE ?=1;";
$_cp_val='1';
$_cp_tot2=nz_pdo_single($_cp_sql,$_cp_val,$conn);

$_cp_sql="SELECT DISTINCT `league` FROM `ih_statss` ORDER BY `league` DESC;";
$_cp_res=nz_pdo_array($_cp_sql,$conn);
$_cp_league1=$_cp_res[0]['league'];

$str="<h3>We have pitching stats for $_cp_tot players from $_cp_tot2 ";
if (1==$_cp_tot2) {
		$str.="	League ($_cp_league1)"; 
	} else {
		$str.="	Leagues ($_cp_league1 and $_cp_league2)"; 
	}
$str.=".</h3>";

*/


$str="<br>";
output($str);
require_once 'g_footer.php';
?>

