<?php

$str="<h2>Loading Turn</h2>";
output($str);

$_cp_sql = "SELECT `turn_id`, `game`, `league`, `season`, `week`, `coach`, `processed`, `roundup` , `uploadID` 
			FROM `g_turnsummary` 
			WHERE `uploadID`=?
			ORDER BY `processed` ASC,`league` ASC, `season` ASC, `week` ASC;";

$row=nz_pdo_row($_cp_sql,$_cp_mychoice,$conn);
#nz_debug($row);

$_cp_turnid=$row['uploadID'];
$_cp_game=$row['game'];
$_cp_league=$row['league'];
$_cp_season=$row['season'];
$_cp_week=$row['week'];
$_cp_coach=$row['coach'];
$_cp_processed=$row['processed'];
$_cp_roundup=$row['roundup'];

$_cp_sql = "SELECT `f_id`, `f_team`  FROM `bb_franchises` WHERE `f_league` LIKE '$_cp_league' AND `f_coach` LIKE ?";
$_cp_mychoice=$_cp_coach;
$row=nz_pdo_row($_cp_sql,$_cp_mychoice,$conn);
$_cp_franchise=$row['f_id'];
$_cp_team=$row['f_team'];
#$_cp_game="Run Chase";


switch ($_cp_game) {
  case "Baseball":
	//Text for middle box
	$str="<h2>About to process a Baseball turn!</h2>";
	output($str);
    require 'g_turns_processcontrol_baseball.php';
break;
case "Ice Hockey":
		//Text for middle box
		$str="<h2>About to process an Ice Hockey turn!</h2>";
		output($str);
		require 'g_turns_processcontrol_icehockey.php';
break;
case "Football":
    //Text for middle box
	$str="<h2>Sorry Gameplan Football is not yet supported!</h2>";
	output($str);
break;
case "Run Chase":
    //Text for middle box
	$str="<h2>Sorry Run Chase is not yet supported!</h2>";
	output($str);
break;
default:
    //Text for middle box
	$str="<h2>Sorry this type of turn is not yet supported!</h2>";
	output($str);

}

$_cp_sql="UPDATE `a_uploads` SET `processed`=2 WHERE `upload_id`=$_cp_turnid";
#nz_pdo($_cp_sql,$conn);

//Flag update has been run
$_cp_sql="INSERT INTO `g_updated` (`updated_id`, `updated_when`) VALUES (NULL, current_timestamp());";
nz_pdo($_cp_sql,$conn);




?>
