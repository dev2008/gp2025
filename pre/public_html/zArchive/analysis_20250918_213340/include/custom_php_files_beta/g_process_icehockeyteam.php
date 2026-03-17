<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'ih_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';
#pgetpost_print();
#$arr = get_defined_vars();
#nz_debug($arr,"");
$time_start = microtime(true);
//Text for middle box
$str="<h2>Processing my roster.</h3>";
output($str); 



#TODO allocate IH Franchise

$_cp_sql9="SELECT `tf_seq`, `tf_line` FROM `g_turnsfull` WHERE `up_id`=$_cp_turnid ORDER BY `tf_seq` ASC LIMIT 250 ";
$_cp_mytext=nz_pdo_array($_cp_sql9,$conn);	
$_cp_rosterstart=0;
foreach ($_cp_mytext as $row) {
		$_cp_rowid=$row['tf_seq'];
		$_cp_rowtext=$row['tf_line'];
		#echo "<p>$_cp_rowid - $_cp_rowtext</p>";
		if ('Sh Name'==substr($_cp_rowtext,0,7) AND 0==$_cp_rosterstart) {
			$_cp_rowno=$_cp_rowid+1;
			$_cp_seqno=$_cp_rowid;
			$_cp_rosterstart=1;
			#echo "<p>Found roster</p>";
		}	
		if ('Key to'==substr($_cp_rowtext,0,6)) {
			$_cp_rownomax=$_cp_rowid;
		}
}

if (1==$_cp_rosterstart) {
echo "<h2>Found start of roster on row $_cp_rowno with id $_cp_turnid, end of roster is line $_cp_rownomax</h3>";

$_cp_sql8="SELECT `tf_seq`, `tf_line` FROM `g_turnsfull` WHERE `up_id`=$_cp_turnid AND `tf_seq`>=$_cp_rowno AND `tf_seq`<=$_cp_rownomax ORDER BY `tf_seq` ASC ";
$_cp_myrosterlines=nz_pdo_array($_cp_sql8,$conn);	

$_cp_gt=0;
$_cp_skater=0;
$_cp_franchise=40023;

$x=0;
foreach ($_cp_myrosterlines as $_cp_myrosterline) {
	$_cp_rowtext=$_cp_myrosterline['tf_line'];
	$_cp_rtext = preg_split("/[\s,]+/", $_cp_rowtext);
	#nz_debug($_cp_rtext,"");

	$_cp_pos=$_cp_rtext[3];
	if ('R'==$_cp_rtext[6]){
		$_cp_rtext[6]=0;
	}
	$_cp_rtext[6]=str_replace("*","",$_cp_rtext[6] ?? '');
	
	#nz_debug($_cp_rtext,"");
	#Find Franchise ID
	$_cp_sql="SELECT `f_id` FROM `ih_franchises` WHERE 1=? AND `f_league` LIKE '$_cp_league' AND `f_coach` LIKE '%$_cp_coach%'";
	$_cp_val=1;
	$_cp_franchise=nz_pdo_single($_cp_sql,$_cp_val,$conn);
	#echo "<p>Franchise is $_cp_franchise discovered by $_cp_sql</p>";
		
	switch ($_cp_pos) {
		case "GLT":
			$_cp_prate=ih_prateg($_cp_rtext[4], $_cp_rtext[6], $_cp_rtext[7]);
			$_cp_arate=ih_arateg($_cp_rtext[4], $_cp_rtext[6], $_cp_rtext[7],$_cp_rtext[22], $_cp_rtext[23], $_cp_rtext[24], $_cp_rtext[25] );
			//Check if we have this player already so we know whether to update or insert
			#Use ON DUPLICATE KEY UPDATE??
			$_cp_sql9="	INSERT INTO `ih_goaltenders` 
			(`g_id`, `ih_franchise`, `ih_season`,`ih_week`,`ih_player`,`g_sh`, `g_cname`, `g_sname`, `g_pos`, `g_level`, `g_best`, `g_exp`, `g_pot`, `g_trade`, `g_cnd`, `g_fat`, `g_frm`, `g_tot`, `g_val`, `g_wages`, `g_squad`, `g_fdf`, `g_fal`, `g_sdf`, `g_sal`, `g_ref`, `g_bal`, `g_han`, `g_dur`, `g_prate`, `g_arate`, `g_rank`, `g_role`) 
			VALUES (NULL, '$_cp_franchise', $_cp_season, $_cp_week, NULL, $_cp_rtext[0], '$_cp_rtext[1]', '$_cp_rtext[2]', 'GLT', $_cp_rtext[4], '$_cp_rtext[5]', $_cp_rtext[6], $_cp_rtext[7], $_cp_rtext[8], $_cp_rtext[9], '$_cp_rtext[10]', $_cp_rtext[11], $_cp_rtext[12], $_cp_rtext[13], $_cp_rtext[15], '$_cp_rtext[17]', $_cp_rtext[18], $_cp_rtext[19], $_cp_rtext[20], $_cp_rtext[21], '$_cp_rtext[22]', '$_cp_rtext[23]', '$_cp_rtext[24]', '$_cp_rtext[25]',$_cp_prate,$_cp_arate,0,'Unrated')
			ON DUPLICATE KEY UPDATE
			 `ih_season`= $_cp_season,
			`ih_week`= $_cp_week,
			 `g_level`= $_cp_rtext[4],
			 `g_best`= '$_cp_rtext[5]',
			 `g_exp`= $_cp_rtext[6],
			 `g_pot`= $_cp_rtext[7],
			 `g_trade`= $_cp_rtext[8],
			 `g_cnd`= $_cp_rtext[9],
			 `g_fat`= '$_cp_rtext[10]',
			 `g_frm`= $_cp_rtext[11],
			 `g_tot`= $_cp_rtext[12],
			 `g_val`= $_cp_rtext[13],
			 `g_wages`= $_cp_rtext[15],
			 `g_squad`= '$_cp_rtext[17]',
			 `g_fdf`= $_cp_rtext[18],
			 `g_fal`= $_cp_rtext[19],
			 `g_sdf`= $_cp_rtext[20],
			 `g_sal`= $_cp_rtext[21],
			 `g_ref`= '$_cp_rtext[22]',
			 `g_bal`= '$_cp_rtext[23]',
			 `g_han`= '$_cp_rtext[24]',
			 `g_dur`= '$_cp_rtext[25]',
			 `g_prate`=$_cp_prate,
			 `g_arate`=$_cp_arate;";	
			$_cp_gt++;
			#echo "<p>$_cp_sql9</p>";
			nz_pdo_insert($_cp_sql9,$conn);
		break;
		#Need to look at variations per position	
		case "LDF":
		case "RDF":
			$_cp_skater++;
			$_cp_prate=ih_prate($_cp_rtext[4], $_cp_rtext[6], $_cp_rtext[7], $_cp_rtext[9]);
			$_cp_arate=ih_arateD($_cp_rtext[4], $_cp_rtext[6], $_cp_rtext[7],$_cp_rtext[22], $_cp_rtext[23], $_cp_rtext[24], $_cp_rtext[25], $_cp_rtext[26], $_cp_rtext[27], $_cp_rtext[28], $_cp_rtext[29] );
			$_cp_sql10="INSERT INTO `ih_skaters` 
			(`s_id`, `ih_franchise`, `ih_season`, `ih_week`, `ih_player`, `s_sh`, `s_cname`, `s_sname`, `s_pos`, `s_level`, `s_best`, `s_exp`, `s_pot`, `s_trade`, `s_agg`, `s_fat`, `s_frm`, `s_tot`, `s_val`, `s_wages`, `s_squad`, `s_nms`, `s_pws`, `s_shs`, `s_ad`, `s_pow`, `s_acc`, `s_qui`, `s_con`, `s_pas`, `s_def`, `s_chk`, `s_sta`, `s_prate`, `s_arate`, `s_rank`, `s_role`) 
			VALUES 
			(NULL, '$_cp_franchise', $_cp_season, $_cp_week, NULL, $_cp_rtext[0], '$_cp_rtext[1]', '$_cp_rtext[2]', '$_cp_rtext[3]', $_cp_rtext[4], '$_cp_rtext[5]', $_cp_rtext[6], $_cp_rtext[7], $_cp_rtext[8], $_cp_rtext[9], '$_cp_rtext[10]', $_cp_rtext[11], $_cp_rtext[12], $_cp_rtext[13], $_cp_rtext[15], '$_cp_rtext[17]', $_cp_rtext[18], $_cp_rtext[19], $_cp_rtext[20], $_cp_rtext[21], '$_cp_rtext[22]', '$_cp_rtext[23]', '$_cp_rtext[24]', '$_cp_rtext[25]', '$_cp_rtext[26]', '$_cp_rtext[27]', '$_cp_rtext[28]', '$_cp_rtext[29]',$_cp_prate,$_cp_arate,0,'Unrated')
			ON DUPLICATE KEY UPDATE
			`ih_season`=$_cp_season,
			`ih_week`=$_cp_week,
			`s_level`=$_cp_rtext[4],
			`s_best`='$_cp_rtext[5]',
			`s_exp`=$_cp_rtext[6],
			`s_pot`=$_cp_rtext[7],
			`s_trade`=$_cp_rtext[8],
			`s_agg`=$_cp_rtext[9],
			`s_fat`='$_cp_rtext[10]',
			`s_frm`=$_cp_rtext[11],
			`s_tot`=$_cp_rtext[12],
			`s_val`=$_cp_rtext[13],
			`s_wages`=$_cp_rtext[15],
			`s_squad`='$_cp_rtext[17]',
			`s_nms`=$_cp_rtext[18],
			`s_pws`=$_cp_rtext[19],
			`s_shs`=$_cp_rtext[20],
			`s_ad`=$_cp_rtext[21],
			`s_pow`='$_cp_rtext[22]',
			`s_acc`='$_cp_rtext[23]',
			`s_qui`='$_cp_rtext[24]',
			`s_con`='$_cp_rtext[25]',
			`s_pas`='$_cp_rtext[26]',
			`s_def`='$_cp_rtext[27]',
			`s_chk`='$_cp_rtext[28]',
			`s_sta`='$_cp_rtext[29]',
			`s_prate`=$_cp_prate,
			`s_arate`=$_cp_arate;";
			#echo "<p>$_cp_sql10</p>";
			nz_pdo_insert($_cp_sql10,$conn);
			break;
		case "RWG":
		case "LWG":			
			$_cp_skater++;
			$_cp_prate=ih_prate($_cp_rtext[4], $_cp_rtext[6], $_cp_rtext[7], $_cp_rtext[9]);
			$_cp_arate=ih_arateW($_cp_rtext[4], $_cp_rtext[6], $_cp_rtext[7],$_cp_rtext[22], $_cp_rtext[23], $_cp_rtext[24], $_cp_rtext[25], $_cp_rtext[26], $_cp_rtext[27], $_cp_rtext[28], $_cp_rtext[29] );
			$_cp_sql10="INSERT INTO `ih_skaters` 
			(`s_id`, `ih_franchise`, `ih_season`, `ih_week`, `ih_player`, `s_sh`, `s_cname`, `s_sname`, `s_pos`, `s_level`, `s_best`, `s_exp`, `s_pot`, `s_trade`, `s_agg`, `s_fat`, `s_frm`, `s_tot`, `s_val`, `s_wages`, `s_squad`, `s_nms`, `s_pws`, `s_shs`, `s_ad`, `s_pow`, `s_acc`, `s_qui`, `s_con`, `s_pas`, `s_def`, `s_chk`, `s_sta`, `s_prate`, `s_arate`, `s_rank`, `s_role`) 
			VALUES 
			(NULL, '$_cp_franchise', $_cp_season, $_cp_week, NULL, $_cp_rtext[0], '$_cp_rtext[1]', '$_cp_rtext[2]', '$_cp_rtext[3]', $_cp_rtext[4], '$_cp_rtext[5]', $_cp_rtext[6], $_cp_rtext[7], $_cp_rtext[8], $_cp_rtext[9], '$_cp_rtext[10]', $_cp_rtext[11], $_cp_rtext[12], $_cp_rtext[13], $_cp_rtext[15], '$_cp_rtext[17]', $_cp_rtext[18], $_cp_rtext[19], $_cp_rtext[20], $_cp_rtext[21], '$_cp_rtext[22]', '$_cp_rtext[23]', '$_cp_rtext[24]', '$_cp_rtext[25]', '$_cp_rtext[26]', '$_cp_rtext[27]', '$_cp_rtext[28]', '$_cp_rtext[29]',$_cp_prate,$_cp_arate,0,'Unrated')
			ON DUPLICATE KEY UPDATE
			`ih_season`=$_cp_season,
			`ih_week`=$_cp_week,
			`s_level`=$_cp_rtext[4],
			`s_best`='$_cp_rtext[5]',
			`s_exp`=$_cp_rtext[6],
			`s_pot`=$_cp_rtext[7],
			`s_trade`=$_cp_rtext[8],
			`s_agg`=$_cp_rtext[9],
			`s_fat`='$_cp_rtext[10]',
			`s_frm`=$_cp_rtext[11],
			`s_tot`=$_cp_rtext[12],
			`s_val`=$_cp_rtext[13],
			`s_wages`=$_cp_rtext[15],
			`s_squad`='$_cp_rtext[17]',
			`s_nms`=$_cp_rtext[18],
			`s_pws`=$_cp_rtext[19],
			`s_shs`=$_cp_rtext[20],
			`s_ad`=$_cp_rtext[21],
			`s_pow`='$_cp_rtext[22]',
			`s_acc`='$_cp_rtext[23]',
			`s_qui`='$_cp_rtext[24]',
			`s_con`='$_cp_rtext[25]',
			`s_pas`='$_cp_rtext[26]',
			`s_def`='$_cp_rtext[27]',
			`s_chk`='$_cp_rtext[28]',
			`s_sta`='$_cp_rtext[29]',
			`s_prate`=$_cp_prate,
			`s_arate`=$_cp_arate;";
			#echo "<p>$_cp_sql10</p>";
			nz_pdo_insert($_cp_sql10,$conn);
		break;
		case "CEN":	
			$_cp_skater++;
			$_cp_prate=ih_prate($_cp_rtext[4], $_cp_rtext[6], $_cp_rtext[7], $_cp_rtext[9]);
			$_cp_arate=ih_arateC($_cp_rtext[4], $_cp_rtext[6], $_cp_rtext[7],$_cp_rtext[22], $_cp_rtext[23], $_cp_rtext[24], $_cp_rtext[25], $_cp_rtext[26], $_cp_rtext[27], $_cp_rtext[28], $_cp_rtext[29] );
			$_cp_sql10="INSERT INTO `ih_skaters` 
			(`s_id`, `ih_franchise`, `ih_season`, `ih_week`, `ih_player`, `s_sh`, `s_cname`, `s_sname`, `s_pos`, `s_level`, `s_best`, `s_exp`, `s_pot`, `s_trade`, `s_agg`, `s_fat`, `s_frm`, `s_tot`, `s_val`, `s_wages`, `s_squad`, `s_nms`, `s_pws`, `s_shs`, `s_ad`, `s_pow`, `s_acc`, `s_qui`, `s_con`, `s_pas`, `s_def`, `s_chk`, `s_sta`, `s_prate`, `s_arate`, `s_rank`, `s_role`) 
			VALUES 
			(NULL, '$_cp_franchise', $_cp_season, $_cp_week, NULL, $_cp_rtext[0], '$_cp_rtext[1]', '$_cp_rtext[2]', '$_cp_rtext[3]', $_cp_rtext[4], '$_cp_rtext[5]', $_cp_rtext[6], $_cp_rtext[7], $_cp_rtext[8], $_cp_rtext[9], '$_cp_rtext[10]', $_cp_rtext[11], $_cp_rtext[12], $_cp_rtext[13], $_cp_rtext[15], '$_cp_rtext[17]', $_cp_rtext[18], $_cp_rtext[19], $_cp_rtext[20], $_cp_rtext[21], '$_cp_rtext[22]', '$_cp_rtext[23]', '$_cp_rtext[24]', '$_cp_rtext[25]', '$_cp_rtext[26]', '$_cp_rtext[27]', '$_cp_rtext[28]', '$_cp_rtext[29]',$_cp_prate,$_cp_arate,0,'Unrated')
			ON DUPLICATE KEY UPDATE
			`ih_season`=$_cp_season,
			`ih_week`=$_cp_week,
			`s_level`=$_cp_rtext[4],
			`s_best`='$_cp_rtext[5]',
			`s_exp`=$_cp_rtext[6],
			`s_pot`=$_cp_rtext[7],
			`s_trade`=$_cp_rtext[8],
			`s_agg`=$_cp_rtext[9],
			`s_fat`='$_cp_rtext[10]',
			`s_frm`=$_cp_rtext[11],
			`s_tot`=$_cp_rtext[12],
			`s_val`=$_cp_rtext[13],
			`s_wages`=$_cp_rtext[15],
			`s_squad`='$_cp_rtext[17]',
			`s_nms`=$_cp_rtext[18],
			`s_pws`=$_cp_rtext[19],
			`s_shs`=$_cp_rtext[20],
			`s_ad`=$_cp_rtext[21],
			`s_pow`='$_cp_rtext[22]',
			`s_acc`='$_cp_rtext[23]',
			`s_qui`='$_cp_rtext[24]',
			`s_con`='$_cp_rtext[25]',
			`s_pas`='$_cp_rtext[26]',
			`s_def`='$_cp_rtext[27]',
			`s_chk`='$_cp_rtext[28]',
			`s_sta`='$_cp_rtext[29]',
			`s_prate`=$_cp_prate,
			`s_arate`=$_cp_arate;";
			#echo "<p>$_cp_sql10</p>";
			nz_pdo_insert($_cp_sql10,$conn);
		break;
		default:
		  //Not a player
	  }
}
$_cp_sw=$_cp_season.$_cp_week;
echo "<h3>Found $_cp_gt GoalTenders and $_cp_skater Skaters!</h3	>";
//Delete unmatched Skaters
$_cp_sql="SELECT `s_id`,`ih_season`, `ih_week`, `ih_player`, `s_sh`, `s_cname`, `s_sname`, `s_pos`, `s_rank`, `s_role`, `s_notes` FROM `ih_skaters` WHERE `ih_franchise`= $_cp_franchise AND CONCAT(`ih_season`,`ih_week`) <>$_cp_sw;";
#echo "<h3>UM: $_cp_sql</h3>";
$cp_unmatched=nz_pdo_array($_cp_sql,$conn);
if (sizeof($cp_unmatched)>0) {
	if (1==sizeof($cp_unmatched)){
		$_cp_noun="player";
	} else {
		$_cp_noun="players";
	}
	foreach ($cp_unmatched as $_cp_row) {
			#nz_debug($_cp_row,"Row:");
			$_cp_rowid=$_cp_row['s_id'];
			$_cp_season=$_cp_row['ih_season'];
			$_cp_week=$_cp_row['ih_week'];
			$_cp_player=$_cp_row['ih_player'];
			$_cp_sh=$_cp_row['s_sh'];
			$_cp_cname=$_cp_row['s_cname'];
			$_cp_sname=$_cp_row['s_sname'];
			$_cp_pos=$_cp_row['s_pos'];
			$_cp_rank=$_cp_row['s_rank'];
			$_cp_role=$_cp_row['s_role'];
			$_cp_notes=$_cp_row['s_notes'];
			echo "<h2>Deleting #$_cp_sh $_cp_cname $_cp_sname (Rank - $_cp_rank; Rating - $_cp_rating; Notes - $_cp_notes) from roster</h2>";
			$_cp_sql="DELETE FROM `ih_skaters` WHERE `s_id`=$_cp_rowid;";

			nz_pdo($_cp_sql,$conn);
	}
}
//Delete unmatched Goaltenders
$_cp_sql="SELECT `g_id`,`ih_season`, `ih_week`, `ih_player`, `g_sh`, `g_cname`, `g_sname`, `g_pos`, `g_rank`, `g_role`, `g_notes` FROM `ih_goaltenders` WHERE `ih_franchise`= $_cp_franchise AND CONCAT(`ih_season`,`ih_week`) <>$_cp_sw;";
#echo "<h3>UM: $_cp_sql</h3>";
$cp_unmatched=nz_pdo_array($_cp_sql,$conn);
	foreach ($cp_unmatched as $_cp_row) {
			#nz_debug($_cp_row,"Row:");
			$_cp_rowid=$_cp_row['g_id'];
			$_cp_season=$_cp_row['ih_season'];
			$_cp_week=$_cp_row['ih_week'];
			$_cp_player=$_cp_row['ih_player'];
			$_cp_sh=$_cp_row['g_sh'];
			$_cp_cname=$_cp_row['g_cname'];
			$_cp_sname=$_cp_row['g_sname'];
			$_cp_pos=$_cp_row['g_pos'];
			$_cp_rank=$_cp_row['g_rank'];
			$_cp_role=$_cp_row['g_role'];
			$_cp_notes=$_cp_row['g_notes'];
			$_cp_sql="DELETE FROM `ih_goaltenders` WHERE `g_id`=$_cp_rowid;";
			echo "<h2>Deleted $_cp_sh $_cp_cname $_cp_sname (Rank - $_cp_rank; Role - $_cp_role; Notes - $_cp_notes) from roster</h2>";
			nz_pdo($_cp_sql,$conn);
	}


} else {
	echo "<p>Sorry couldn't find the roster!!</p>";
}
echo "<br />";
?>
