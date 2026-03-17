<?php
//Version 11.4.2 
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

//Create basic layout
$str="<div class='w3-container $mycolour6 w3-round-xxlarge'>";
$str.="<div class='w3-pale-green w3-round-large'>";
$str.="<h1>&nbsp;Slapshot Summary</h1>";
$str.="</div>";
$str.= "<div class='w3-panel $mycolour4 w3-round-medium '>";
output($str);


#Start the table
echo "<table class='w3-table w3-striped w3-bordered w3-mobile'>\n";
echo "<tr><th class='$mycolour4'>League</th><th class='$mycolour4'>Season</th><th class='$mycolour4'>Week</th><th class='$mycolour4'>Players</th><th class='$mycolour16'>Draftees</th><th class='$mycolour16'>Scouted</th><th class='$mycolour17' >Stats</th></tr>";
echo "<tr><th class='$mycolour4'>&nbsp;</th><th class='$mycolour4'>&nbsp;</th><th class='$mycolour4'>&nbsp;</th><th class='$mycolour4'><em>(all time)</em></th><th class='$mycolour16'><em>(this Season)</em><th class='$mycolour16'><em>(this Season)</em></th><th class='$mycolour17'><em>(this Season)</em></th></tr>";
#First establish what Leagues we have
$_cp_sql="SELECT DISTINCT `league` FROM `g_turnsummary` WHERE `league` LIKE 'NHL%'";
$myleaguearray=nz_pdo_array($_cp_sql,$conn);
//now loop through turn line by line
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
	$_cp_sql="SELECT COUNT(*) FROM `bb_players` WHERE `f_league`=?";
	$_cp_val=$myleague;
	$_cp_players=nz_pdo_single($_cp_sql,$_cp_val,$conn);
	#Retrieve number of batting stats 
	$_cp_sql="SELECT COUNT(*) FROM `bb_statsb` WHERE `season`=$_cp_season  AND `week`=$_cp_week AND `league`=?";
	$_cp_val=$myleague;
	$_cp_bstats=nz_pdo_single($_cp_sql,$_cp_val,$conn);
	if (0==$_cp_bstats){
		$_cp_sql="SELECT COUNT(*) FROM `bb_statsb` WHERE `season`=$_cp_season  AND `league`=?";
		$_cp_val=$myleague;
		$_cp_bstats=nz_pdo_single($_cp_sql,$_cp_val,$conn);
	}
	#Retrieve number of pitching stats 
	$_cp_sql="SELECT COUNT(*) FROM `bb_statsp` WHERE `season`=$_cp_season  AND `week`=$_cp_week AND `league`=?";
	$_cp_val=$myleague;
	$_cp_pstats=nz_pdo_single($_cp_sql,$_cp_val,$conn);
	if (0==$_cp_pstats){
		$_cp_sql="SELECT COUNT(*) FROM `bb_statsb` WHERE `season`=$_cp_season  AND `league`=?";
		$_cp_val=$myleague;
		$_cp_pstats=nz_pdo_single($_cp_sql,$_cp_val,$conn);
	}	
	
	#Retrieve number of draftees 
	$_cp_sql="	SELECT COUNT(*) 
	FROM `bb_dplayers` a
		INNER JOIN `bb_drafts` b ON a.d_id=b.d_id 
	WHERE b.d_season=$_cp_season AND b.d_league=?";
	$_cp_val=$myleague;
	$_cp_draftees=nz_pdo_single($_cp_sql,$_cp_val,$conn);

	#Retrieve number of draftees scouted
	$_cp_sql="	SELECT COUNT(*) 
	FROM `bb_dplayers` a
		INNER JOIN `bb_drafts` b ON a.d_id=b.d_id 
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
$_cp_sql="SELECT COUNT(*) FROM `bb_dplayers` WHERE ?=1";
$_cp_val='1';
$_cp_tot=nz_pdo_single($_cp_sql,$_cp_val,$conn);

$_cp_sql="SELECT COUNT(DISTINCT `d_league`) FROM `bb_drafts` WHERE 1=?";
$_cp_val='1';
$_cp_tot2=nz_pdo_single($_cp_sql,$_cp_val,$conn);

$_cp_sql="SELECT DISTINCT `d_league` FROM `bb_drafts` a INNER JOIN `bb_dplayers` b ON a.d_id=b.d_id WHERE 1=1 ORDER BY `d_league` ASC";
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


$_cp_sql="SELECT COUNT(*) FROM `bb_dplayers` WHERE `dp_skill1` IS NOT NULL AND ?=1";
$_cp_val='1';
$_cp_tot=nz_pdo_single($_cp_sql,$_cp_val,$conn);

$_cp_sql="SELECT COUNT(DISTINCT `d_league`) FROM `bb_drafts` a INNER JOIN `bb_dplayers` b ON a.d_id=b.d_id WHERE  `dp_skill1` IS NOT NULL AND 1=? ORDER BY `d_league` ASC";
$_cp_val='1';
$_cp_tot2=nz_pdo_single($_cp_sql,$_cp_val,$conn);

$_cp_sql="SELECT DISTINCT `d_league` FROM `bb_drafts` a INNER JOIN `bb_dplayers` b ON a.d_id=b.d_id WHERE  `dp_skill1` IS NOT NULL AND 1=1 ORDER BY `d_league` ASC";
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
$_cp_sql="SELECT COUNT(*) FROM `bb_statsb` WHERE ?=1";
$_cp_val='1';
$_cp_tot=nz_pdo_single($_cp_sql,$_cp_val,$conn);

$_cp_sql="SELECT COUNT(DISTINCT `league`) FROM `bb_statsb` WHERE ?=1;";
$_cp_val='1';
$_cp_tot2=nz_pdo_single($_cp_sql,$_cp_val,$conn);

$_cp_sql="SELECT DISTINCT `league` FROM `bb_statsb` ORDER BY `league` DESC;";
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

$_cp_sql="SELECT COUNT(*) FROM `bb_statsp`  WHERE ?=1";
$_cp_val='1';
$_cp_tot=nz_pdo_single($_cp_sql,$_cp_val,$conn);

$_cp_sql="SELECT COUNT(DISTINCT `league`) FROM `bb_statsp` WHERE ?=1;";
$_cp_val='1';
$_cp_tot2=nz_pdo_single($_cp_sql,$_cp_val,$conn);

$_cp_sql="SELECT DISTINCT `league` FROM `bb_statsp` ORDER BY `league` DESC;";
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


//End of page
$str="</div>";
output($str);

//Start of footer
require_once 'g_footer.php';
?>
