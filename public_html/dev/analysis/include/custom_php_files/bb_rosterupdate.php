<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
//This should only ever be called from a form submission
$time_start = microtime(true);
require_once 'g_functions.php';
require_once 'bb_functions.php';
require_once 'mydatabase.php';

$str="<br /><div class='nz-card'>";
$str.="<header class='w3-container w3-blue-gray'>";
$str.="<h1>Baseball Rosters Update</h1>";
$str.="</header>";
output($str);

//Ensure not called directly 
if (!isset($_POST['league'])) {
	$str="<div class='w3-container w3-pale-blue'>";
	$str.="<br />";
	$str.="<div class='w3-container w3-teal'>\n";
	$str.="<h2>**ERROR**&nbsp;&nbsp;Page called directly!&nbsp;&nbsp;**ERROR**</h2>";
	output($str);
} 	else {
$_cp_league=$_POST["league"];
$_cp_season=$_POST["season"];
$_cp_week=$_POST["week"];
$_cp_turnid=$_POST["turnid"];


$str="<div class='w3-container w3-pale-blue'>";
output($str);
//Text for middle box
$str="<h2>About to process roster updates for Turn ID $_cp_turnid - $_cp_league s$_cp_season w$_cp_week</h3>";
output($str);

$str="<div class='w3-container w3-teal'>\n";
output($str);


//Determine if choice has been made already
if (isset($_POST['process'])) {
	#Process rosters
	$_cp_process=$_POST["process"];
	switch ($_cp_process) {
	  case "new":
	  //TODO Add progress indicator
	  //TODO Move transactions here
		$str="<h2>Rosters to be created.</h2>";
		output($str);
		# Retrieve players from temp table according to id
		$_cp_mysql="SELECT `p_id`, `f_league`, `p_team`, `p_sh`, `p_cname`, `p_sname`, `p_pos`, `p_type`, `p_hand`, `p_level`, `p_best`, `p_exp`, `p_pot`, `p_trade`, `p_val` FROM `bb_playerstemp` WHERE `turn`=$_cp_turnid";
			try {
				$stmt = myDB::run("$_cp_mysql");
			} catch (PDOException $e) {
				if (1==$debug_mode) {
					echo "DataBase Error:<br>".$e->getMessage();
					echo "<pre></pre>$_cp_sql</pre>";
				}
				exit ("<h1>****Warning - processing stopped on database error****</h1>");
			} catch (Exception $e) {
				if (1==$mydebug) {
					echo "General Error:<br>".$e->getMessage();
				}	
				exit ("<h1>****Warning - processing stopped on general error****</h1>");
			}
		//Loop through players and insert into DB	
		$i=0;
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$i++;	
			    if ($i % 50 == 0) {
					$str='Created ';
					$str.=$i;
					$str.=' roster records / ';
					output($str);
				};
			
			$_cp_data = [
			'id' => $row['p_id'],
			'league' => $row['f_league'],
			'team' => $row['p_team'],
			'sh' => $row['p_sh'],
			'cname' => $row['p_cname'],
			'sname' => $row['p_sname'],
			'pos' => $row['p_pos'],
			'type' => $row['p_type'],
			'hand' => $row['p_hand'],
			'level' => $row['p_level'],
			'best' => $row['p_best'],
			'exp' => $row['p_exp'],
			'pot' => $row['p_pot'],
			'trade' => $row['p_trade'],
			'val' => $row['p_val']
		];
		#nz_debug($_cp_data);  
		
		#$_cp_mysql="INSERT INTO pdowrapper (id,name) VALUES (:id, :name)";
		#INSERT INTO `bb_players` (`p_id`, `f_league`, `p_team`, `p_sh`, `p_cname`, `p_sname`, `p_pos`, `p_type`, `p_hand`, `p_level`, `p_best`, `p_exp`, `p_pot`, `p_trade`, `p_val`) VALUES ('1500000', 'MLB10', '65000', '99', 'Joe', 'Bloggs', 'P', 'Pit', 'R', '10', 'CON', '5', '10', '5', '50');
		$_cp_mysql2="INSERT INTO `bb_players`
			(`p_id`, `f_league`, `p_team`, `p_sh`, `p_cname`, `p_sname`, `p_pos`, `p_type`, `p_hand`, `p_level`, `p_best`, `p_exp`, `p_pot`, `p_trade`, `p_val`)
				VALUES 
			(:id,:league,:team,:sh,:cname,:sname,:pos,:type,:hand,:level,:best,:exp,:pot,:trade,:val)
		";
		
		$stmt2 = myDB::prepare("$_cp_mysql2");
		$stmt2->execute($_cp_data);
		$_cp_mysql4="	INSERT INTO `bb_transactions` (`id`, `league`, `franchise`, `player`, `season`, `week`, `source`) 
					VALUES 
					(NULL, :league, :franchise, :player, :season, :week, 2000);";
		$_cp_data4 = [
			'league' => $row['f_league'],
			'franchise' => $row['p_team'],
			'player' => $row['p_id'],
			'season' => $_cp_season,
			'week' => $_cp_week,
		];
		$stmt4 = myDB::prepare("$_cp_mysql4");
		$stmt4->execute($_cp_data4);
		}
		//Report final total
		$str='Created ';
		$str.=$i;
		$str.=' roster records!';
		output($str);	
		$_cp_sql = "UPDATE `g_turnsummary` SET `processed`= 3 WHERE `turn_id`=$_cp_turnid";
		myDB::query("$_cp_sql");	
		$_cp_sql = "UPDATE `bb_players` SET `p_cname`='JD' WHERE `p_cname`='J.D.';";
		myDB::query("$_cp_sql");	
		$_cp_sql = "UPDATE `bb_players` SET `p_sname`='GrandPre' WHERE `p_sname`='Grand Pre';";
		myDB::query("$_cp_sql");	
		$_cp_sql = "UPDATE `bb_players` SET `p_cname`='Vlad',`p_sname`='GuerreroJr' WHERE `p_cname`='V.Guerrero';";
		myDB::query("$_cp_sql");	
		$_cp_sql = "UPDATE `bb_players` SET `p_sname`='HoKang' WHERE `p_sname`='Ho Kang';";
		myDB::query("$_cp_sql");	
		$_cp_sql = "UPDATE `bb_players` SET `p_sname`='DeJesus' WHERE `p_sname`='De Jesus';";
		myDB::query("$_cp_sql");	
		$_cp_sql = "UPDATE `bb_players` SET `p_cname`='PJ',`p_sname`='Walters' WHERE `p_sname`='J Walters';";
		myDB::query("$_cp_sql");	
		$_cp_sql = "UPDATE `bb_players` SET `p_cname`='JenHo' WHERE `p_cname`='Jen-Ho';";
		myDB::query("$_cp_sql");	
		$_cp_sql = "UPDATE `bb_players` SET `p_sname`='VanEyk' WHERE `p_sname`='Van Eyk';";
		myDB::query("$_cp_sql");	
		$_cp_sql = "UPDATE `bb_players` SET `p_cname`='JJ' WHERE `p_cname`='J.J.';";
		myDB::query("$_cp_sql");	
		$_cp_sql = "UPDATE `bb_players` SET `p_cname`='AJ' WHERE `p_cname`='A.J.';";
		myDB::query("$_cp_sql");			
		$_cp_sql = "UPDATE `bb_players` SET `p_cname`='TJ' WHERE `p_cname`='T.J.';";
		myDB::query("$_cp_sql");			
		//ToDo - integrate this!
		$_cp_sql = "UPDATE `bb_players` targetTable LEFT JOIN `bb_franchises` sourceTable ON targetTable.`p_team`= sourceTable.`f_id` SET targetTable.`p_conf` = sourceTable.`f_conference`;";
		myDB::query("$_cp_sql");			
			
	break;
	  case "update":
		echo "<h3>Rosters to be updated.</h3>";
	break;
	  default:
		echo "<h2>** Sorry there seems to be an issue! **</h2>";
	}


	
} else 	{
# Getting single field value
$_cp_mysql="SELECT COUNT(*) FROM bb_players WHERE f_league=?";
$id=$_cp_league;
$_cp_value = myDB::run("$_cp_mysql", [$id])->fetchColumn();


if ($_cp_value>0){
	echo "<h3>Found $_cp_value existing players - rosters to be updated.</h3>";
} else {
	echo "<h2>No players found - rosters will be created!</h2>";
	$str="<form action='index.php?function=show_static_page&id_static_page=12' method='post'>";
	$str.="<input type='hidden' id='league' name='league' value='$_cp_league'>";
	$str.="<input type='hidden' id='season' name='season' value='$_cp_season'>";
	$str.="<input type='hidden' id='week' name='week' value='$_cp_week'>";
	$str.="<input type='hidden' id='turn' name='turnid' value='$_cp_turnid'>";
	$str.="<input type='hidden' id='process' name='process' value='new'>";
	$str.="<input type='submit' value='Create new rosters?'>";
	$str.="</form>";
	output($str);
}
}

//End of main program
$str="&nbsp;&nbsp;&nbsp;&nbsp;";
$str.="<br />";
output($str);
} 
require_once 'g_footer.php';
?>
