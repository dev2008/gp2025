<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

$str="<h3>Roster Summary:-</h3>";
output($str);

$_cp_sql="SELECT DISTINCT `turn` FROM `bb_playerstemp` WHERE 1 ORDER BY `turn` ASC LIMIT 1";
$result=nz_pdo_array($_cp_sql,$conn);
foreach ($result as $row) {
	$_cp_turn=$row['turn'];	
}

/*
//Temp fix for players in wrong Team
$names= array (
  array(10801,9000058),
  array(10801,9000059),
);
$i=0;
foreach ($names as $name) {
	$team=$names[$i][0];
	$id=$names[$i][1];
	$i++;
$_cp_mysql="UPDATE `bb_playerstemp` SET `p_team` = :team WHERE `bb_playerstemp`.`p_id` = :id;";
	try {
		$stmt = myDB::run("$_cp_mysql", [$team, $id]);
	} catch (PDOException $e) {
		if (1==$debug_mode) {
			echo "DataBase Error:<br>".$e->getMessage();
			echo "<pre></pre>$_cp_sql</pre>";
		}
		exit ("<h1>****Warning - processing stopped on database error****</h1>");
	} catch (Exception $e) {
		if (1==$mydebug) {
			echo "A - General Error:<br>".$e->getMessage();
		}	
		exit ("<h1>****Warning - processing stopped on general error A****</h1>");
	}
}
*/
	
$str="<div class='w3-container w3-red w3-cell'>";
$str.="<h3>Team summary:&nbsp;&nbsp;&nbsp;&nbsp;</h3>";
output($str);
$_cp_sql="SELECT b.f_ori_nickname AS `Team`,count(1) AS `No` 
FROM `bb_playerstemp` a 
	INNER JOIN `bb_franchises` b ON a.p_team = b.f_id 
WHERE `turn`=$_cp_turn
GROUP BY b.f_ori_nickname
ORDER BY b.f_id;";
$_cp_myteams=nz_pdo_array($_cp_sql,$conn);
$i=1;
foreach ($_cp_myteams as $_cp_myteam) {
	$_cp_t1=$_cp_myteam['Team']; 
	$_cp_t2=$_cp_myteam['No'];
	$str="<p>$_cp_t1 - $_cp_t2";
	if ($_cp_t2<29 OR $_cp_t2>32) {
		$str.="&nbsp;&nbsp;&nbsp;**ERROR**";
	}
	if ($i % 4 ==0) {
		$str.="<hr />";	
	}
	$i++;
	$str.="</p>";
	output($str);
}
$str="</div>";
output($str);

$str="<div class='w3-container w3-teal w3-cell'>";
$str.="<p class='w3-text-teal'>Filler</p>";
$str.="</div>";
output($str);

$str="<div class='w3-container w3-light-blue w3-cell'>";
$str.="<h3>Position summary:&nbsp;&nbsp;&nbsp;&nbsp;</h3>";
output($str);
$_cp_sql="SELECT a.`p_pos` AS `Pos`,COUNT(1) AS `No`
FROM `bb_playerstemp` a
	INNER JOIN `bb_positions` b ON a.p_pos=b.position
WHERE 1
GROUP BY a.`p_pos`
ORDER BY b.id ASC ";
$_cp_myteams=nz_pdo_array($_cp_sql,$conn);
$i=1;
foreach ($_cp_myteams as $_cp_myteam) {
	$_cp_t1=$_cp_myteam['Pos']; 
	$_cp_t2=$_cp_myteam['No'];
	$str="<p>$_cp_t1 - $_cp_t2";
		if ('P'==$_cp_t1 AND ($_cp_t2<240 OR $_cp_t2>576)) {
		$str.="&nbsp;&nbsp;&nbsp;**ERROR**";
	}
	$i++;
	$str.="</p>";
	output($str);
}

$str="</div>";
output($str);

$str="<div class='w3-container w3-teal w3-cell'>";
$str.="<p class='w3-text-teal'>Filler</p>";
$str.="</div>";
output($str);

$str="<div class='w3-container w3-orange w3-cell'>";
$str.="<h3>Player extract:&nbsp;&nbsp;&nbsp;&nbsp;</h3>";
output($str);
# Getting array of rows and making into table
$_cp_mysql="SELECT `p_id` as `myplayerid`, `f_league` as `League`, `p_team` as `TeamID`, `p_sh` as `Sh`, `p_cname` as `Christian`, `p_sname` as `Surname`, `p_pos`  as `Pos`, `p_type` as `Type`, `p_hand` as `Hand`, `p_level` as `Level`, `p_best` as `Best`, `p_exp` as `Exp`, `p_pot` as `Pot`, `p_trade` as `Trade`, `p_val` as `Val`, `turn`  as `Turn` FROM `bb_playerstemp` WHERE `turn`=$_cp_turn";
	try {
		$all = myDB::run("$_cp_mysql")->fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		if (1==$debug_mode) {
			echo "DataBase Error:<br>".$e->getMessage();
			echo "<pre></pre>$_cp_sql</pre>";
		}
		exit ("<h1>****Warning - processing stopped on database error****</h1>");
	} catch (Exception $e) {
		if (1==$mydebug) {
			echo "B - General Error:<br>".$e->getMessage();
		}	
		exit ("<h1>****Warning - processing stopped on general error B****</h1>");
	}
echo build_table($all);

$str="</div>";
output($str);

$str="<br>";
output($str);

$str="<form action='index.php?function=show_static_page&id_static_page=12' method='post'>";
$str.="<input type='hidden' id='league' name='league' value='$_cp_league'>";
$str.="<input type='hidden' id='season' name='season' value='$_cp_season'>";
$str.="<input type='hidden' id='week' name='week' value='$_cp_week'>";
$str.="<input type='hidden' id='turn' name='turnid' value='$_cp_turnid'>";
$str.="<input type='submit' value='Are you sure all of the above is correct and ready for processing?'>";
$str.="</form>";
output($str);


$str="&nbsp;&nbsp;&nbsp;&nbsp;";
$str.="<br>";
$str.="&nbsp;&nbsp;&nbsp;&nbsp;";
output($str);

?>
