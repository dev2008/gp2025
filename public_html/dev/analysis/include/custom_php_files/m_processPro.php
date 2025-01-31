<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

$_cp_myoffcoach="";
$_cp_mydefcoach="";
$time_start = microtime(true);
$number_of_rowstotal=0;

//Set up headers etc
$str="<br /><div class='nz-card'>";
$str.="<div class='w3-container $mycolour6'>";
$str.="<div class='w3-pale-green'>";
$str.="<h1>Process Pro Play By Play</h1>";
$str.="</div>";
$str.= "<div class='w3-panel $mycolour4 nz-card w3-round-xxlarge'>";
$str.="<h2>Finding unprocessed records</h2>";
output($str);



//Allocate Coaches to  Pro games
//Find missing records
//Check for weeks we have in games table but no Coaches
$_cp_sql = "SELECT DISTINCT a.`league`,a.`season`,a.`week` FROM `f_games` a WHERE a.league LIKE 'NFL%' AND NOT EXISTS ( SELECT 1 FROM `fp_gamecoaches` b WHERE a.league = b.league AND a.season = b.season AND a.week = b.week );";
$_cp_weeks=nz_pdo_array($_cp_sql,$conn);
#$_cp_label="Weeks:-";
#nz_debug($_cp_weeks,$_cp_label);
//Loop through missing weeks
foreach ($_cp_weeks as $_cp_week) {
	#$_cp_label="Week:-";
	#nz_debug($_cp_week,$_cp_label);
    $_cp_mleague=$_cp_week['league'];
	$_cp_mseason=$_cp_week['season'];
	$_cp_mweek=$_cp_week['week'];

	$_cp_sql = "SELECT a.`league`,a.`season`,a.`week`,a.`franchise`, a.`coach`,c.`abbr`  
	FROM `f_games` a
		INNER JOIN `f_abbr` c ON a.`team`=c.`name`
		INNER JOIN `fp_franchises` d ON a.`franchise`=d.`franchise`
	WHERE a.`league` = '$_cp_mleague' AND a.`season` = $_cp_mseason AND a.`week` = $_cp_mweek;";
	#echo "<p>$_cp_sql</p>";
	$_cp_missing=nz_pdo_array($_cp_sql,$conn);
		foreach ($_cp_missing as $_cp_row) {
			#$_cp_label="Missing:-";
			#nz_debug($_cp_row,$_cp_label);
			//Populate GameCoaches Table
			$_cp_myleague=$_cp_row['league'];
			$_cp_myseason=$_cp_row['season'];
			$_cp_myweek=$_cp_row['week'];
			$_cp_myfranchise=$_cp_row['franchise'];
			$_cp_mycoach=$_cp_row['coach'];
			$_cp_myabbr=$_cp_row['abbr'];
			#echo "<p>Details - $_cp_myleague, $_cp_myseason, $_cp_myweek, $_cp_myfranchise, $_cp_mycoach,$_cp_myabbr</p>";
			$_cp_sql2="INSERT INTO `fp_gamecoaches` 
				(`id`, `league`, `season`, `week`, `franchise`, `coach`, `abbr`)
				VALUES (NULL, '$_cp_myleague', $_cp_myseason, $_cp_myweek, $_cp_myfranchise, '$_cp_mycoach','$_cp_myabbr');";
			$_cp_mc=nz_pdo_insert($_cp_sql2,$conn);
			#echo "<p>Inserted record with id $_cp_mc - $_cp_myleague, $_cp_myseason, $_cp_myweek, $_cp_myfranchise, $_cp_mycoach,$_cp_myabbr</p>";
		}
	}



//Pro Play by Plays
$str="<h2>Pro Play by Plays</h2>\n";
output($str);

$_cp_sql3 = "SELECT `a_id`,`a_league`, `a_season`, `a_week`,`a_off`,`a_def`,`n_offcoach`, `n_defcoach` 
FROM `n_playbyplay` 
WHERE `a_league` LIKE 'NFLAR%'  AND (n_offcoach IS NULL OR n_offcoach = '' OR n_defcoach IS NULL OR n_defcoach = '') 
ORDER BY `a_id`,`a_league` ASC, `a_season` ASC, `a_week` ASC;";
$res3 = execute_db($_cp_sql3, $conn);
$number_of_rows = $res3->rowCount() ; 
$number_of_rowsp=number_format($number_of_rows);
$number_of_rowstotal=$number_of_rowstotal+$number_of_rows;
$str="<h2>Finding Pro PBP records</h2>";
$str.="<h2>$number_of_rowsp records located</h3>";
output($str);
$i=0;
while($row3 = fetch_row_db($res3)){
			$_cp_myleague=$row3[1];
			$_cp_myseason=$row3[2];
			$_cp_myweek=$row3[3];
			$_cp_myoff=$row3[4];
			$_cp_mydef=$row3[5];
			//Find offensive coach for this team 
			$_cp_sql4 = "SELECT `coach` FROM `fp_gamecoaches` WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason AND `week`=$_cp_myweek AND `abbr`='$_cp_myoff';";
			#echo "<p>$_cp_sql4</p>";
			$res4 = execute_db($_cp_sql4, $conn);			
			while($row4 = fetch_row_db($res4)){
				$_cp_myoffcoach=$row4[0];
			}
			//Update record with Off coach for this team 
			$_cp_sql5 = "UPDATE `n_playbyplay` 
						SET `n_offcoach`='$_cp_myoffcoach' 
						WHERE `a_league`='$_cp_myleague' AND `a_season`=$_cp_myseason AND `a_week`=$_cp_myweek AND `a_off`='$_cp_myoff'";
			#echo "<p>$_cp_sql5</p>";
			$res5 = execute_db($_cp_sql5, $conn);
			
			
			
			//Find defensive coach for this team 
			$_cp_sql6 = "SELECT `coach` FROM `fp_gamecoaches` WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason AND `week`=$_cp_myweek AND `abbr`='$_cp_mydef';";
			$res6 = execute_db($_cp_sql6, $conn);			
			#echo "<p>$_cp_sql6</p>";
			while($row6 = fetch_row_db($res6)){
				$_cp_mydefcoach=$row6[0];
			}
			#echo "<p>$row3[0] / $row3[1] / $row3[2] / $row3[3] / $row3[4] / $row3[5] </p>";
			//Update record with Off coach for this team 
			$_cp_sql7 = "UPDATE `n_playbyplay` 
						SET `n_defcoach`='$_cp_mydefcoach'
						WHERE `a_league`='$_cp_myleague' AND `a_season`=$_cp_myseason AND `a_week`=$_cp_myweek AND `a_def`='$_cp_mydef'";
			$res7 = execute_db($_cp_sql7, $conn);
			#echo "<p>$_cp_sql7</p>";
			
			$i++;
		if ($i % 500 == 0) {
			$str='Processed ';
			$str.=$i;
			$str.=' Pro PBP Records /';
			output($str);
		}
}
//Report final 
$str='Processed ';
$str.=$i;
$str.=' Pro PBP Records ';
$str.="</p>\n";
$str.="<br />";
output($str);	


$str="</div>\n";
output($str);


//Start of footer
require_once 'g_footer.php';
?>
