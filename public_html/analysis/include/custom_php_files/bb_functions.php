<?php

/*
 * Function to calculate the GPM rating of a draftee.
 * Used in draft analysis
 */
function draft_gpm($_cp_ptype, $_cp_skill1, $_cp_skill2, $_cp_skill3, $_cp_skill4, $_cp_pot)
	{
				switch ($_cp_skill1) {
					  case "Po":
					  $_cp_s1val=0;
					break;
					  case "Fa":
					  $_cp_s1val=1;
					break;
					  case "Av":
					  $_cp_s1val=2;
					break;
					  case "Go":
					  $_cp_s1val=3;
					break;										
					  case "Ex":
					  $_cp_s1val=4;
					break;										
					  case "WC":
					  $_cp_s1val=5;
					break;			
					  default:
					  $_cp_s1val="Error";
				}		
				switch ($_cp_skill2) {
					  case "Po":
					  $_cp_s2val=0;
					break;
					  case "Fa":
					  $_cp_s2val=1;
					break;
					  case "Av":
					  $_cp_s2val=2;
					break;
					  case "Go":
					  $_cp_s2val=3;
					break;										
					  case "Ex":
					  $_cp_s2val=4;
					break;										
					  case "WC":
					  $_cp_s2val=5;
					break;			
					  default:
					  $_cp_s2val="Error";
				}
				switch ($_cp_skill3) {
					  case "Po":
					  $_cp_s3val=0;
					break;
					  case "Fa":
					  $_cp_s3val=1;
					break;
					  case "Av":
					  $_cp_s3val=2;
					break;
					  case "Go":
					  $_cp_s3val=3;
					break;										
					  case "Ex":
					  $_cp_s3val=4;
					break;										
					  case "WC":
					  $_cp_s3val=5;
					break;			
					  default:
					  $_cp_s3val="Error";
				}
				switch ($_cp_skill4) {
					  case "Po":
					  $_cp_s4val=0;
					break;
					  case "Fa":
					  $_cp_s4val=1;
					break;
					  case "Av":
					  $_cp_s4val=2;
					break;
					  case "Go":
					  $_cp_s4val=3;
					break;										
					  case "Ex":
					  $_cp_s4val=4;
					break;										
					  case "WC":
					  $_cp_s4val=5;
					break;			
					  default:
					  $_cp_s4val="Error";
				}


				switch ($_cp_ptype) {
					  case "Pit":
					  $_cp_grate=3.25*($_cp_s1val*1.25)+($_cp_s2val*1.25)+($_cp_s3val*0.75)+($_cp_s4val*0.75)+($_cp_pot/2);
					break;
					  case "Cat":
					  $_cp_grate=3.25*($_cp_s1val*1.2)+($_cp_s2val*0.9)+($_cp_s3val*0.9)+($_cp_s4val*1.0)+($_cp_pot/2);
					break;
					  default:
						$_cp_grate=3.25*($_cp_s1val*1.2)+($_cp_s2val*0.95)+($_cp_s3val*1.1)+($_cp_s4val*0.7)+($_cp_pot/2);
				}		
					return $_cp_grate;	
	}

/*
 * Function to calculate the Division etc of a Franchise
 * Used in roster setup
 */
function whatsmydiv_asm($_cp_franchiseclub)
	{
		switch ($_cp_franchiseclub) {
			case "0":
			case "1":
			case "2":
			case "3":
				$_cp_mydiv[0]="AL";
				$_cp_mydiv[1]="East";
			break;
			case "4":
			case "5":
			case "6":
			case "7":
				$_cp_mydiv[0]="AL";
				$_cp_mydiv[1]="Central";
			break;
			case "8":
			case "9":
			case "10":
			case "11":
				$_cp_mydiv[0]="AL";
				$_cp_mydiv[1]="West";
			break;			  
			case "12":
			case "13":
			case "14":
			case "15":
				$_cp_mydiv[0]="NL";
				$_cp_mydiv[1]="East";
			break;
			case "16":
			case "17":
			case "18":
			case "19":
				$_cp_mydiv[0]="NL";
				$_cp_mydiv[1]="Central";
			break;
			case "20":
			case "21":
			case "22":
			case "23":
				$_cp_mydiv[0]="NL";
				$_cp_mydiv[1]="West";
			break;			  

			  default:
				
			}
		return $_cp_mydiv;
	}

function asm_franchisebase($_cp_league) {
	switch ($_cp_league) {
  case "MLB1":
    $_cp_franchisebase=10100;
  break;
  case "MLB2":
    $_cp_franchisebase=10200;
  break;
  case "MLB3":
    $_cp_franchisebase=10300;
  break;
  case "MLB4":
    $_cp_franchisebase=10400;
  break;
  case "MLB5":
    $_cp_franchisebase=10500;
  break;
  case "MLB6":
    $_cp_franchisebase=10600;
  break;
  case "MLB7":
    $_cp_franchisebase=10700;
  break;
  case "MLB8":
    $_cp_franchisebase=10800;
  break;
  case "MLB21":
    $_cp_franchisebase=12100;
  break;
  default:
    echo "Error!";
}

	return ($_cp_franchisebase);
}

function bb_mypitcher($_cp_text,$_cp_franchise,$_cp_turnid,$conn) {
	#nz_debug($_cp_text,'Pitcher:-');
	#Need to check for multiple names
	//Change Rookies to 0 and remove *
	$_cp_exp = str_replace('R', '0', $_cp_text[7]);
	$_cp_exp = preg_replace('/[^0-9]/', '', $_cp_exp);
	$_cp_text[17] = str_replace('*', '', $_cp_text[17]);


	if ('Pit'==$_cp_text[3]) {
	#$_cp_sql="INSERT INTO `bb_myteam` (`m_id`, `m_team`, `m_turnid`, `m_sh`, `m_cname`, `m_sname`, `m_type`, `m_hand`, `m_level`, `m_best`, `m_exp`, `m_pot`, `m_trd`, `m_fat`, `m_frm`, `m_tot`, `m_inj`, `m_val`, `m_s1`, `m_s2`, `m_s3`, `m_s4`, `m_sqd`, `m_pos`) 
	#VALUES (NULL, '$_cp_franchise', '$_cp_turnid', '$_cp_text[0]', '$_cp_text[1]', '$_cp_text[2]', '$_cp_text[3]', '$_cp_text[4]', '$_cp_text[5]', '$_cp_text[6]', '$_cp_exp', '$_cp_text[8]', '$_cp_text[9]', '$_cp_text[10]', '$_cp_text[11]', '$_cp_text[12]', '$_cp_text[13]', '$_cp_text[14]', '$_cp_text[15]', '$_cp_text[16]', '$_cp_text[17]', '$_cp_text[18]', '$_cp_text[19]', '$_cp_text[20]');";
$_cp_sql = "
    INSERT INTO `bb_myteam` 
    (`m_id`, `m_team`, `m_turnid`, `m_sh`, `m_cname`, `m_sname`, `m_type`, `m_hand`, `m_level`, `m_best`, `m_exp`, `m_pot`, `m_trd`, `m_fat`, `m_frm`, `m_tot`, `m_inj`, `m_val`, `m_s1`, `m_s2`, `m_s3`, `m_s4`, `m_sqd`, `m_pos`) 
    VALUES 
    (NULL, '$_cp_franchise', '$_cp_turnid', 
    '$_cp_text[0]', '$_cp_text[1]', '$_cp_text[2]', 
    '$_cp_text[3]', '$_cp_text[4]', '$_cp_text[5]', 
    '$_cp_text[6]', '$_cp_exp', '$_cp_text[8]', 
    '$_cp_text[9]', '$_cp_text[10]', '$_cp_text[11]', 
    '$_cp_text[12]', '$_cp_text[13]', '$_cp_text[14]', 
    '$_cp_text[16]', '$_cp_text[17]', '$_cp_text[18]', 
    '$_cp_text[19]', '$_cp_text[20]', 'P'
    );
";

	$_cp_lastid=nz_pdo_insert($_cp_sql,$conn);
	$str="Pitcher record inserted with id #$_cp_lastid <br>";
	output($str);
	} 
}

function bb_mybatter($_cp_text,$_cp_franchise,$_cp_turnid,$conn) {
	#echo "$_cp_text[21] /";
	#Need to check for multiple names
	//Change Rookies to 0 and remove *
	$_cp_exp = str_replace('R', '0', $_cp_text[7]);
	$_cp_exp = preg_replace('/[^0-9]/', '', $_cp_exp);
	$_cp_text[17] = str_replace('*', '', $_cp_text[17]);
	#echo "This is a Batter ($_cp_text[21])<br>";
	#nz_debug($_cp_text,'Batter:-');
	if (in_array($_cp_text[3], ['Bat', 'Cat'])) {
	$_cp_sql = "
		INSERT INTO `bb_myteam` 
		(`m_id`, `m_team`, `m_turnid`, `m_sh`, `m_cname`, `m_sname`, `m_type`, `m_hand`, `m_level`, `m_best`, `m_exp`, `m_pot`, `m_trd`, `m_fat`, `m_frm`, `m_tot`, `m_inj`, `m_val`, `m_s1`, `m_s2`, `m_s3`, `m_s4`, `m_sqd`, `m_pos`) 
		VALUES 
		(NULL, '$_cp_franchise', '$_cp_turnid', 
		'$_cp_text[0]', '$_cp_text[1]', '$_cp_text[2]', 
		'$_cp_text[3]', '$_cp_text[4]', '$_cp_text[5]', 
		'$_cp_text[6]', '$_cp_exp', '$_cp_text[8]', 
		'$_cp_text[9]', '$_cp_text[10]', '$_cp_text[11]', 
		'$_cp_text[12]', '$_cp_text[13]', '$_cp_text[14]', 
		'$_cp_text[16]', '$_cp_text[17]', '$_cp_text[18]', 
		'$_cp_text[19]', '$_cp_text[20]', '$_cp_text[21]'
		);
";



	$_cp_lastid=nz_pdo_insert($_cp_sql,$conn);
	$str="Batter record inserted with id #$_cp_lastid <br>";
	output($str);
	} 
}



function draft_asm($_cp_pid, $_cp_plevel, $_cp_ppot, $_cp_phand, $_cp_ppos, $_cp_pbest, $_cp_pval)
{
    // Total weight for precision
    $total_weight = 10000;

    // Level contributes 33.1% of the total (3310/10000)
    $level_weight = 3310;
    $_cdp_levelscore = ($_cp_plevel / 10.0) * $level_weight;  // Assuming max level is 10

    // Potential contributes 40.3% of the total (4030/10000), max potential is 25
    $potential_weight = 1530;
    $_cdp_potentialscore = ($_cp_ppot*100)+ ((($_cp_ppot-14) / 10.0) * $potential_weight);  // Extra value for ptential 15 and above

    // Position contributes 7.2% of the total (720/10000)
    // Assign unique position scores within 1520, ensuring no digit is repeated more than twice
    switch ($_cp_ppos) {
        case "SS":
            $_cdp_positionscore = 720;  // Highest value for SS
            break;
        case "C":
            $_cdp_positionscore = 681;  // Unique value for C
            break;
        case "CF":
            $_cdp_positionscore = 652;  // Unique value for CF
            break;
        case "IF":
            $_cdp_positionscore = 393;  // Unique value for IF
            break;
        case "OF":
            $_cdp_positionscore = 374;  // Unique value for OF
            break;
        case "2B":
            $_cdp_positionscore = 205;  // Unique value for 2B
            break;
        case "3B":
            $_cdp_positionscore = 176;   // Unique value for 3B
            break;
        case "1B":
            $_cdp_positionscore = 127;   // Unique value for 1B
            break;
        case "RF":
            $_cdp_positionscore = 117;   // Unique value for RF
            break;
        case "LF":
            $_cdp_positionscore = 107;   // Unique value for LF
            break;
        case "P":
            $_cdp_positionscore = 25;   // Fixed value for Pitchers
            break;
        default:
            $_cdp_positionscore = 1;   // Default unique value for unspecified positions
    }

    // Best skill contributes 4.8% of the total (480/10000)
    // Assign unique best skill scores within 480
    switch ($_cp_pbest) {
        case "HIT":
            $_cdp_bestscore = 480;  // Highest unique score for HIT
            break;
        case "POW":
            $_cdp_bestscore = 450;
            break;
        case "SPD":
            $_cdp_bestscore = 420;
            break;
        case "FLD":
            $_cdp_bestscore = 390;
            break;
        case "ACC":
            $_cdp_bestscore = 360;
            break;
        case "CON":
            $_cdp_bestscore = 330;
            break;
        case "QUI":
            $_cdp_bestscore = 300;
            break;
        case "STA":
            $_cdp_bestscore = 270;
            break;
        default:
            $_cdp_bestscore = 240;  // Lowest unique score if no match
    }

    // Handedness contributes 14.3% of the total (1430/10000)
    // Assign unique handedness scores within 1430
    switch ($_cp_phand) {
        case "S":
            $_cdp_handscore = 1430;  // Highest unique value for switch-hitters
            break;
        case "L":
            if ("P" == $_cp_ppos) {
                $_cdp_handscore = 0;  // Left-handed pitchers get no score here
            } else {
                $_cdp_handscore = 1200;  // Reduced score for left-handed non-pitchers
            }
            break;
        case "R":
            if ("P" == $_cp_ppos) {
                $_cdp_handscore = 1000;  // Unique value for right-handed pitchers
            } else {
                $_cdp_handscore = 800;   // Unique value for right-handed non-pitchers
            }
            break;
        default:
            $_cdp_handscore = 600;  // Default unique value for unspecified handedness
    }

    // Calculate the total raw score out of 10000
    $_cp_arate = $_cdp_levelscore + $_cdp_potentialscore + $_cdp_positionscore + $_cdp_bestscore + $_cdp_handscore;

    // Introduce a small deterministic variation based on player ID to prevent exact clustering
    // This adds a tiny fraction based on the last two digits of the player ID
    $adjustment = ($_cp_pid % 100) / 10000.0;  // Adds between 0.00 and 0.0099
    $_cp_arate += $adjustment;

    // Scale the raw score down to 1-100
    $_cp_arate = ($_cp_arate / $total_weight) * 100;

    // Reduce by 40% if the player is a left-handed pitcher
    if ($_cp_phand == "L" && $_cp_ppos == "P") {
        $_cp_arate *= 0.6;  // Apply 40% reduction
    }

    return round($_cp_arate, 2);  // Return a rounded value for consistency
}
