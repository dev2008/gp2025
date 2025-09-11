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
 * Function to calculate the ASM rating of a draftee.
 * Used in draft analysis
 */
function draft_asm($_cp_pid,$_cp_plevel, $_cp_ppot,$_cp_phand,$_cp_ppos,$_cp_pbest,$_cp_pval)
	{
					//Allocate score for Level
					if ($_cp_plevel>7)        {
							$_cdp_levelboost=25;
					} else if ($_cp_plevel>6) {
							$_cdp_levelboost=22;
					} else if ($_cp_plevel>5) {
							$_cdp_levelboost=18;						
					} else if ($_cp_plevel>4) {
							$_cdp_levelboost=10;						
					} else if ($_cp_plevel>3) {
							$_cdp_levelboost=5;						
					} else  {
							$_cdp_levelboost=0;
					}						

					
					//Allocate score for Potential
					if ($_cp_ppot>19)        {
							$_cp_apot=25;
					} else if ($_cp_ppot>17) {
							$_cp_apot=20;
					} else if ($_cp_ppot>14) {
							$_cp_apot=15;						
					} else if ($_cp_ppot>10) {
							$_cp_apot=5;						
					} else  {
							$_cp_apot=0;
					}						
					
					//Allocate score for Position					
					switch ($_cp_ppos) {
					  case "SS":
					  case "C":
					  case "CF":
					  $_cdp_posboost=25;
					break;
					  case "IF":
					  case "OF":
					  $_cdp_posboost=20;
					break;
					  case "2B":
					  case "3B":
					  $_cdp_posboost=15;
					break;
					  case "1B":
					  case "LF":
					  case "RF":
					  $_cdp_posboost=10;
					break;
					  default:
						$_cdp_posboost=5;
					}
					
					//Allocate score for Best					
					switch ($_cp_pbest) {
					  case "HIT":
					  case "POW":
					  $_cdp_bestboost=25;
					break;
					  case "ACC":
					  case "CON":
					  $_cdp_bestboost=20;
					break;
					  case "SPD":
					  case "QUI":
					  $_cdp_bestboost=10;
					break;
					  default:
						$_cdp_bestboost=0;
					}

					//Allocate score for Handedness
					switch ($_cp_phand) {
						case "S":
							$_cdp_handboost=25;
						break;
						case "L":
							//Check if Pitcher
							if ("P"==$_cp_ppos){
								$_cdp_handboost=0;
							} else {
								$_cdp_handboost=18;
							}
						break;
						case "R":
							//Check if Pitcher
							if ("P"==$_cp_ppos){
								$_cdp_handboost=15;
							} else {
								$_cdp_handboost=5;
							}
						break;
					}
					
					$_cp_arate=(($_cdp_levelboost*1)+($_cp_apot*2)+($_cdp_posboost*0.25)+($_cdp_handboost*0.6)+($_cdp_bestboost*0.15));
					#echo "<p>$_cp_pid - $_cp_arate ($_cdp_levelboost,$_cp_apot,$_cdp_posboost,$_cdp_handboost)</p>";


					//Deduct points for 2lp wages
					if ($_cp_pval>19 AND $_cp_plevel<6 AND $_cp_ppot<17) {
						$_cp_arate=$_cp_arate-5;		
					}

					return $_cp_arate;
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
	nz_debug($_cp_text,'Pitcher:-');
	#Need to check for multiple names
	if ('R'==$_cp_text[7]) {
		$_cp_exp=0;
	} else {
		$_cp_exp=$_cp_text[7];
	}
	if ('Pit'==$_cp_text[3]) {
	$_cp_sql="INSERT INTO `bb_myteam` (`m_id`, `m_team`, `m_turnid`, `m_sh`, `m_cname`, `m_sname`, `m_type`, `m_hand`, `m_level`, `m_best`, `m_exp`, `m_pot`, `m_trd`, `m_fat`, `m_frm`, `m_tot`, `m_inj`, `m_val`, `m_s1`, `m_s2`, `m_s3`, `m_s4`, `m_sqd`, `m_pos`) 
	VALUES (NULL, '$_cp_franchise', '$_cp_turnid', '$_cp_text[0]', '$_cp_text[1]', '$_cp_text[2]', '$_cp_text[3]', '$_cp_text[4]', '$_cp_text[5]', '$_cp_text[6]', '$_cp_exp', '$_cp_text[8]', '$_cp_text[9]', '$_cp_text[10]', '$_cp_text[11]', '$_cp_text[12]', '$_cp_text[13]', '$_cp_text[14]', '$_cp_text[15]', '$_cp_text[16]', '$_cp_text[17]', '$_cp_text[18]', '$_cp_text[19]', '$_cp_text[20]');";
	$_cp_lastid=nz_pdo_insert($_cp_sql,$conn);
	$str="Pitcher record inserted with id #$_cp_lastid <br>";
	output($str);
	} else {
		nz_debug($_cp_text,'Pitcher:-');
		echo 'Hello';
	}
}

