<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';


$str="<p>Processing rosters.</p>";
output($str); 


	$_cp_sql9="SELECT `tf_seq`, `tf_line` FROM `g_turnsfull` WHERE `up_id`=$_cp_turnid ORDER BY `tf_seq` ASC ";
	#echo "<p>$_cp_sql9</p>";
	$_cp_mytext=nz_pdo_array($_cp_sql9,$conn);	
	$myarraysize = count($_cp_mytext); 
	$str="Found $myarraysize rows to analyse&nbsp;&nbsp;&nbsp;&nbsp;";
	output($str); 
	$i=-1;
	foreach ($_cp_mytext as $row) {
			$i++;
			#if ($i>1169 AND $i<1189) {
			#}
			$_cp_rowid=$row['tf_seq'];
			$_cp_rowtext=strtolower($row['tf_line']);
			#echo "$i<br>";
			#nz_debug($_cp_rowtext);		
			if ('roundup'==substr($_cp_rowtext,35,7)) {
				$_cp_rowno=$_cp_rowid;
				echo "<p>Found start of rosters on line $_cp_rowid</p>";
				#echo "<p>$_cp_rowid - $_cp_rowtext</p>";
			} else if ('roundup'==substr($_cp_rowtext,36,7)) {
				$_cp_rowno=$_cp_rowid;
				echo "<p>Found start of rosters on line $_cp_rowid</p>";
				#echo "<p>$_cp_rowid - $_cp_rowtext</p>";
			} else if ('roundup'==substr($_cp_rowtext,4,7)) {
				$_cp_rowno=$_cp_rowid;
				echo "<p>Found start of rosters on line $_cp_rowid</p>";
				#echo "<p>$_cp_rowid - $_cp_rowtext</p>";
			}	

	}
	
	//TODO Automate this!!
	$_cp_rownomax=$_cp_rowno+450;


	//Check that we have found roundup ok
	if (is_numeric($_cp_rowno) AND is_numeric($_cp_rownomax)) {

	$str="<p>Roundup can be found between lines $_cp_rowno and $_cp_rownomax&nbsp;&nbsp;&nbsp;&nbsp;</p>";
	output($str); 

	#Build franchise data into an array and insert it into DB

	//Initilaise various variables
	$_cp_franchisebase=asm_franchisebase($_cp_league);
	//Stores the 24 teams individual numbers
	$_cp_franchiseclub=0;
	$_cp_franchisearray=array();
	//Stores where the franchise information can be found
	$_cp_franchiselines=array();
	//Stores the franchise details for inserting into DB
	$_cp_franchiseraw=array();

	$_cp_sql="SELECT `abbr` FROM `bb_teams` WHERE 1=1";
	$teamabbrsraw=nz_pdo_array($_cp_sql,$conn);
	$teamabbrs=array();
	foreach ($teamabbrsraw as $row) {	
		array_push($teamabbrs,$row['abbr']);
	}

	#$title="Printing team abbreviations:-";
	#nz_debug($teamabbrs,$title);
	
	for ($x = $_cp_rowno; $x <= $_cp_rownomax; $x++) {
		$string=$_cp_mytext[$x]['tf_line'];
		$substring="Stadium";
		if (strpos($string, $substring) !== false) {
			$_cp_datastart=$x-1;
			#$title="Looking for franchise lines:-";
			#nz_debug($_cp_mytext[$x-1],$title);
			array_push($_cp_franchiselines,$_cp_datastart);
		} else {
			#echo "The string does not contain the substring.";
		}
#		if ('`ST.66`'==$_cp_etext[0]) {
#			$_cp_datastart=$x+1;
#			array_push($_cp_franchiselines,$_cp_datastart);
#		}
	}
	#$title="Printing franchise lines:-";
	#nz_debug($_cp_franchiselines,$title);

echo "<div class='w3-container w3-pale-blue'>";
echo "<details>";
echo "<summary>";
echo "Franchise info:-";
echo "</summary>";


	foreach ($_cp_franchiselines as $row) {
		$_cp_text1 = preg_split("/[\s,]+/", $_cp_mytext[$row]['tf_line']);
		$title="Text1:-";
		nz_debug($_cp_text1,$title);
		$_cp_text2 = preg_split("/[\s,]+/", $_cp_mytext[$row+1]['tf_line']);
		$mylength=strlen($_cp_text1[6]);
		$title="Text2:-";
		nz_debug($_cp_text2,$title);

		//1st Team
		if (in_array($_cp_text1[4], $teamabbrs)) {
			#echo "<p>Searching for $_cp_text1[4]</p>";
			$_cp_franchiseid=$_cp_franchisebase+$_cp_franchiseclub;
			//Allocate ID
			$_cp_franchiseraw[$_cp_franchiseid]['id']=$_cp_franchiseid;
			//Allocate Abbreviation
			$_cp_franchiseraw[$_cp_franchiseid]['abbr']=$_cp_text1[4];
			array_push($_cp_franchisearray,$_cp_text1[4]);
			//Allocate Division
			$_cp_mydiv=whatsmydiv_asm($_cp_franchiseclub);
			$_cp_franchiseraw[$_cp_franchiseid]['conf']=$_cp_mydiv[0];
			$_cp_franchiseraw[$_cp_franchiseid]['div']=$_cp_mydiv[1];	
			//Allocate Coach
			$_cp_franchiseraw[$_cp_franchiseid]['coach']=$_cp_text1[2];
			$_cp_franchiseraw[$_cp_franchiseid]['coach'].=" ";
			$_cp_franchiseraw[$_cp_franchiseid]['coach'].=$_cp_text1[3];
			//Allocate Stadium Details
			$_cp_franchiseraw[$_cp_franchiseid]['ssize']=$_cp_text2[1];
			$_cp_franchiseraw[$_cp_franchiseid]['ssurface']=$_cp_text2[2];
			$_cp_franchiseraw[$_cp_franchiseid]['stype']=$_cp_text2[3];
			$_cp_franchiseclub++;
		} elseif (in_array($_cp_text1[5], $teamabbrs)) {
			#echo "<p>Searching for $_cp_text1[5]</p>";
			$_cp_franchiseid=$_cp_franchisebase+$_cp_franchiseclub;
			//Allocate ID
			$_cp_franchiseraw[$_cp_franchiseid]['id']=$_cp_franchiseid;
			//Allocate Abbreviation
			$_cp_franchiseraw[$_cp_franchiseid]['abbr']=$_cp_text1[5];
			array_push($_cp_franchisearray,$_cp_text1[5]);
			//Allocate Division
			$_cp_mydiv=whatsmydiv_asm($_cp_franchiseclub);
			$_cp_franchiseraw[$_cp_franchiseid]['conf']=$_cp_mydiv[0];
			$_cp_franchiseraw[$_cp_franchiseid]['div']=$_cp_mydiv[1];	
			//Allocate Coach
			$_cp_franchiseraw[$_cp_franchiseid]['coach']=$_cp_text1[3];
			$_cp_franchiseraw[$_cp_franchiseid]['coach'].=" ";
			$_cp_franchiseraw[$_cp_franchiseid]['coach'].=$_cp_text1[4];
			//Allocate Stadium Details
			$_cp_franchiseraw[$_cp_franchiseid]['ssize']=$_cp_text2[1];
			$_cp_franchiseraw[$_cp_franchiseid]['ssurface']=$_cp_text2[2];
			$_cp_franchiseraw[$_cp_franchiseid]['stype']=$_cp_text2[3];
			$_cp_franchiseclub++;
		} elseif (in_array($_cp_text1[6], $teamabbrs)) {
			#echo "<p>Searching for $_cp_text1[6]</p>";
			$_cp_franchiseid=$_cp_franchisebase+$_cp_franchiseclub;
			//Allocate ID
			$_cp_franchiseraw[$_cp_franchiseid]['id']=$_cp_franchiseid;
			//Allocate Abbreviation
			$_cp_franchiseraw[$_cp_franchiseid]['abbr']=$_cp_text1[6];
			array_push($_cp_franchisearray,$_cp_text1[5]);
			//Allocate Division
			$_cp_mydiv=whatsmydiv_asm($_cp_franchiseclub);
			$_cp_franchiseraw[$_cp_franchiseid]['conf']=$_cp_mydiv[0];
			$_cp_franchiseraw[$_cp_franchiseid]['div']=$_cp_mydiv[1];	
			//Allocate Coach
			$_cp_franchiseraw[$_cp_franchiseid]['coach']=$_cp_text1[4];
			$_cp_franchiseraw[$_cp_franchiseid]['coach'].=" ";
			$_cp_franchiseraw[$_cp_franchiseid]['coach'].=$_cp_text1[5];
			//Allocate Stadium Details
			$_cp_franchiseraw[$_cp_franchiseid]['ssize']=$_cp_text2[1];
			$_cp_franchiseraw[$_cp_franchiseid]['ssurface']=$_cp_text2[2];
			$_cp_franchiseraw[$_cp_franchiseid]['stype']=$_cp_text2[3];
			$_cp_franchiseclub++;
		} else {
			//Nothing found
			#$str="<p>Nothing found for Team 1</p>";
			#output($str); 
		}

		//2nd Team
		if (in_array($_cp_text1[9], $teamabbrs)) {
			#echo "<p>Searching for $_cp_text1[9]</p>";
			$_cp_franchiseid=$_cp_franchisebase+$_cp_franchiseclub;
			//Allocate ID
			$_cp_franchiseraw[$_cp_franchiseid]['id']=$_cp_franchiseid;
			//Allocate Abbreviation
			$_cp_franchiseraw[$_cp_franchiseid]['abbr']=$_cp_text1[9];
			array_push($_cp_franchisearray,$_cp_text1[9]);
			//Allocate Division
			$_cp_mydiv=whatsmydiv_asm($_cp_franchiseclub);
			$_cp_franchiseraw[$_cp_franchiseid]['conf']=$_cp_mydiv[0];
			$_cp_franchiseraw[$_cp_franchiseid]['div']=$_cp_mydiv[1];	
			//Allocate Coach
			$_cp_franchiseraw[$_cp_franchiseid]['coach']=$_cp_text1[7];
			$_cp_franchiseraw[$_cp_franchiseid]['coach'].=" ";
			$_cp_franchiseraw[$_cp_franchiseid]['coach'].=$_cp_text1[8];
			//Allocate Stadium Details
			$_cp_franchiseraw[$_cp_franchiseid]['ssize']=$_cp_text2[5];
			$_cp_franchiseraw[$_cp_franchiseid]['ssurface']=$_cp_text2[6];
			$_cp_franchiseraw[$_cp_franchiseid]['stype']=$_cp_text2[7];
			$_cp_franchiseclub++;
		} elseif (in_array($_cp_text1[10], $teamabbrs)) {
			#echo "<p>Searching for $_cp_text1[10]</p>";
			$_cp_franchiseid=$_cp_franchisebase+$_cp_franchiseclub;
			//Allocate ID
			$_cp_franchiseraw[$_cp_franchiseid]['id']=$_cp_franchiseid;
			//Allocate Abbreviation
			$_cp_franchiseraw[$_cp_franchiseid]['abbr']=$_cp_text1[10];
			array_push($_cp_franchisearray,$_cp_text1[10]);
			//Allocate Division
			$_cp_mydiv=whatsmydiv_asm($_cp_franchiseclub);
			$_cp_franchiseraw[$_cp_franchiseid]['conf']=$_cp_mydiv[0];
			$_cp_franchiseraw[$_cp_franchiseid]['div']=$_cp_mydiv[1];	
			//Allocate Coach
			$_cp_franchiseraw[$_cp_franchiseid]['coach']=$_cp_text1[8];
			$_cp_franchiseraw[$_cp_franchiseid]['coach'].=" ";
			$_cp_franchiseraw[$_cp_franchiseid]['coach'].=$_cp_text1[9];
			//Allocate Stadium Details
			$_cp_franchiseraw[$_cp_franchiseid]['ssize']=$_cp_text2[5];
			$_cp_franchiseraw[$_cp_franchiseid]['ssurface']=$_cp_text2[6];
			$_cp_franchiseraw[$_cp_franchiseid]['stype']=$_cp_text2[7];
			$_cp_franchiseclub++;
		} elseif (in_array($_cp_text1[11], $teamabbrs)) {
			#echo "<p>Searching for $_cp_text1[11]</p>";
			$_cp_franchiseid=$_cp_franchisebase+$_cp_franchiseclub;
			//Allocate ID
			$_cp_franchiseraw[$_cp_franchiseid]['id']=$_cp_franchiseid;
			//Allocate Abbreviation
			$_cp_franchiseraw[$_cp_franchiseid]['abbr']=$_cp_text1[11];
			array_push($_cp_franchisearray,$_cp_text1[11]);
			//Allocate Division
			$_cp_mydiv=whatsmydiv_asm($_cp_franchiseclub);
			$_cp_franchiseraw[$_cp_franchiseid]['conf']=$_cp_mydiv[0];
			$_cp_franchiseraw[$_cp_franchiseid]['div']=$_cp_mydiv[1];	
			//Allocate Coach
			$_cp_franchiseraw[$_cp_franchiseid]['coach']=$_cp_text1[9];
			$_cp_franchiseraw[$_cp_franchiseid]['coach'].=" ";
			$_cp_franchiseraw[$_cp_franchiseid]['coach'].=$_cp_text1[10];
			//Allocate Stadium Details
			$_cp_franchiseraw[$_cp_franchiseid]['ssize']=$_cp_text2[5];
			$_cp_franchiseraw[$_cp_franchiseid]['ssurface']=$_cp_text2[6];
			$_cp_franchiseraw[$_cp_franchiseid]['stype']=$_cp_text2[7];
			$_cp_franchiseclub++;
		} elseif (in_array($_cp_text1[12], $teamabbrs)) {						
			#echo "<p>Searching for $_cp_text1[12]</p>";
			$_cp_franchiseid=$_cp_franchisebase+$_cp_franchiseclub;
			//Allocate ID
			$_cp_franchiseraw[$_cp_franchiseid]['id']=$_cp_franchiseid;
			//Allocate Abbreviation
			$_cp_franchiseraw[$_cp_franchiseid]['abbr']=$_cp_text1[12];
			array_push($_cp_franchisearray,$_cp_text1[12]);
			//Allocate Division
			$_cp_mydiv=whatsmydiv_asm($_cp_franchiseclub);
			$_cp_franchiseraw[$_cp_franchiseid]['conf']=$_cp_mydiv[0];
			$_cp_franchiseraw[$_cp_franchiseid]['div']=$_cp_mydiv[1];	
			//Allocate Coach
			$_cp_franchiseraw[$_cp_franchiseid]['coach']=$_cp_text1[10];
			$_cp_franchiseraw[$_cp_franchiseid]['coach'].=" ";
			$_cp_franchiseraw[$_cp_franchiseid]['coach'].=$_cp_text1[11];
			//Allocate Stadium Details
			$_cp_franchiseraw[$_cp_franchiseid]['ssize']=$_cp_text2[5];
			$_cp_franchiseraw[$_cp_franchiseid]['ssurface']=$_cp_text2[6];
			$_cp_franchiseraw[$_cp_franchiseid]['stype']=$_cp_text2[7];
			$_cp_franchiseclub++;
		} elseif (in_array($_cp_text1[13], $teamabbrs)) {						
			#echo "<p>Searching for $_cp_text1[12]</p>";
			$_cp_franchiseid=$_cp_franchisebase+$_cp_franchiseclub;
			//Allocate ID
			$_cp_franchiseraw[$_cp_franchiseid]['id']=$_cp_franchiseid;
			//Allocate Abbreviation
			$_cp_franchiseraw[$_cp_franchiseid]['abbr']=$_cp_text1[13];
			array_push($_cp_franchisearray,$_cp_text1[12]);
			//Allocate Division
			$_cp_mydiv=whatsmydiv_asm($_cp_franchiseclub);
			$_cp_franchiseraw[$_cp_franchiseid]['conf']=$_cp_mydiv[0];
			$_cp_franchiseraw[$_cp_franchiseid]['div']=$_cp_mydiv[1];	
			//Allocate Coach
			$_cp_franchiseraw[$_cp_franchiseid]['coach']=$_cp_text1[8];
			$_cp_franchiseraw[$_cp_franchiseid]['coach'].=" ";
			$_cp_franchiseraw[$_cp_franchiseid]['coach'].=$_cp_text1[9];
			$_cp_franchiseraw[$_cp_franchiseid]['coach'].=" & ";
			$_cp_franchiseraw[$_cp_franchiseid]['coach'].=$_cp_text1[11];
			$_cp_franchiseraw[$_cp_franchiseid]['coach'].=" ";
			$_cp_franchiseraw[$_cp_franchiseid]['coach'].=$_cp_text1[12];
						
			//Allocate Stadium Details
			$_cp_franchiseraw[$_cp_franchiseid]['ssize']=$_cp_text2[5];
			$_cp_franchiseraw[$_cp_franchiseid]['ssurface']=$_cp_text2[6];
			$_cp_franchiseraw[$_cp_franchiseid]['stype']=$_cp_text2[7];
			$_cp_franchiseclub++;
		} else {			//Nothing found
			#$str="<p>Nothing found for Team 2</p>";
			#output($str); 
		}


	}

$title="Franchise Data:-";
nz_debug($_cp_franchiseraw,$title);
echo "</details>";
echo "</div>";
echo "<br>";

	//Check we have 24 rows
	$myarraysize1 = count($_cp_franchiseraw); 
	#$str="<p>$myarraysize1 rows found</p>";
	#output($str); 
	
	if (24==$myarraysize1) {
	/*
	#$title="Franchise Data:-";
	#nz_debug($_cp_franchiseraw,$title);

	//Back up franchise table
	$mytimestamp=idate("U");
	$_cp_sql = "INSERT INTO `bb_franchises_backup` SELECT NULL,'$mytimestamp',`f_id`, `f_league`, `f_season`, `f_week`,`f_conference`, `f_division`, `f_ori_team`, `f_ori_city`, `f_ori_nickname`, `f_ori_coach`, `f_ori_size`, `f_ori_surface`, `f_ori_type`, `f_team`, `f_city`, `f_nickname`, `f_coach`, `f_size`, `f_surface`, `f_type`, `f_WinnerYears`, `f_ConferenceYears`, `f_DivisionYears`, `f_WildcardYears`, `f_Winner`, `f_Runnerup`, `f_ChampionshipW`, `f_ChampionshipL`, `f_DivisionW`, `f_Wildcard`, `f_MaxWins`, `f_MaxLosses`, `f_MaxScored`, `f_MaxConceded`, `f_Link`
	FROM `bb_franchises` WHERE 1";
	nz_pdo($_cp_sql,$conn);

	$str="Backed up Franchise table</p>";
	output($str); 
	*/


	//Check if records exist
	$_cp_sql = "SELECT `f_id`,`f_season`,`f_week`  FROM `bb_franchises` WHERE `f_league`='$_cp_league'";
	$myrows=nz_pdo_array($_cp_sql,$conn);
	#$title="Franchise check:-";
	#nz_debug($myrows,$title);	
	$myarraysize = count($myrows); 

	/*
	$_cp_cseason=$myrows['f_season'];
	$_cp_cweek=$myrows['f_week'];
	*/

	$_cp_cseason=$myrows[0]['f_season'];
	$_cp_cweek=$myrows[0]['f_week'];

	echo "<p>Week in database is s$_cp_cseason w$_cp_cweek; week being processed is s$_cp_season w$_cp_week</p>";


	if (24==$myarraysize) {
		// In Progress
		//First check if it's a later turn!
		//Is it a later Season
/*
		if ($_cp_cseason>$_cp_season) {
			$_cp_updateok='Y';
		//If same season is it a later week?
		} elseif ($_cp_cweek>$_cp_week) {
*/
		if ($_cp_season>$_cp_cseason) {
			$_cp_updateok='Y';
		//If same season is it a later week?
		} elseif ($_cp_week>$_cp_cweek) {

			$_cp_updateok='Y';
		} else {
			$_cp_updateok='N';			
		}
		if ('Y'==$_cp_updateok) {
			$str="<h3>$myarraysize1 records to be updated</h3>";
			output($str); 
			
			//Loop through and update each Franchise
			foreach ($_cp_franchiseraw as $row) {
				#nz_debug($row,"Row:");
				//Get values for Team / City / Nickname here so we get full Nickname
				$myabbr=$row['abbr'];
				#echo "<p>Row is $myabbr</p>";
				$_cp_sql2="SELECT concat(`city`,' ', `nickname`)AS `team`, `city`, `nickname` FROM `bb_teams` WHERE 1=? AND `abbr`='$myabbr' ORDER BY `id_teams` ASC LIMIT 1;";
				#echo "<p>$_cp_sql2</p>";
				$_cp_val=1;
				$_cp_myteaminfo=nz_pdo_row($_cp_sql2,$_cp_val,$conn);
				#$title="Nickname check:-";
				#nz_debug($_cp_myteaminfo,$title);	
				$_cp_myteam=$_cp_myteaminfo['team'];	
				$_cp_mycity=$_cp_myteaminfo['city'];	
				$_cp_mynickname=$_cp_myteaminfo['nickname'];	
				if (21==$_cp_week) {
					$_cp_nweek=0;
					$_cp_nseason=$_cp_season+1;
					//Write current record to Franchise history table
					//Retrieve required values
					$_cp_sql3 = "	SELECT `f_league`, `f_season`, `f_week`,`f_team`, `f_city`, `f_nickname`, `f_coach`, `f_size`, `f_surface`, `f_type`,`f_abbr`
									FROM `bb_franchises` 
									WHERE `f_id`=$row[id] AND 1=?";
					$_cp_val=1;
					$_cp_fh=nz_pdo_row($_cp_sql3,$_cp_val,$conn);
					$_cp_sql4 = "	INSERT INTO `bb_franchisehistory` (`fh_id`, `f_id`, `fh_league`, `fh_season`, `fh_week`, `fh_team`, `fh_city`, `fh_nickname`, `fh_coach`, `fh_size`, `fh_surface`, `fh_type`, `fh_abbr`) 
									VALUES (NULL, $row[id], '$_cp_fh[0]', $_cp_fh[1], $_cp_fh[2], '$_cp_fh[3]', '$_cp_fh[4]', '$_cp_fh[5]', '$_cp_fh[6]', '$_cp_fh[7]', '$_cp_fh[8]', '$_cp_fh[9]', '$_cp_fh[10]');";
					#echo "<p>$_cp_sql4</p>";
					nz_pdo($_cp_sql4,$conn);
					#$title="Franchise check:-";
					#nz_debug($_cp_fh,$title);	

				} else {
					$_cp_nweek=$_cp_week;
					$_cp_nseason=$_cp_season;			
				}
				//TODO fix this but it's quite complex!
				if ("J Hall"==$row['coach']) {
					$mycoach="Mark J Hall";
				} else {
					$mycoach=$row['coach'];
				}
				$_cp_sql5 = "UPDATE `bb_franchises` 
							 SET `f_season`=$_cp_nseason, `f_week`=$_cp_nweek, `f_team`='$_cp_myteam', `f_city`='$_cp_mycity', `f_nickname`='$_cp_mynickname', `f_coach`='$mycoach', `f_size`='$row[ssize]', `f_surface`='$row[ssurface]', `f_type`='$row[stype]', `f_abbr`='$myabbr'
							 WHERE  `f_id`=$row[id]";
				#echo "<p>$_cp_sql5</p>";
				nz_pdo($_cp_sql5,$conn);
			}

		} else {
			$str="<h3>Processing stopped - does not appear to be a newer turn!</h3>";
			output($str); 
		}
		
		
	} elseif (0==$myarraysize) {
		//Insert new records
		$j=0;
		$str="<p>$myarraysize1 rows to be inserted</p>";
		output($str); 

		foreach ($_cp_franchiseraw as $row) {
/*
				//Get values for Team / City / Nickname here
				$_cp_sql2="SELECT concat(`city`,' ', `nickname`)AS `team`, `city`, `nickname` FROM `bb_teams` WHERE `abbr`='$row[abbr]';";
				$_cp_myteaminfo=nz_pdo_array($_cp_sql2,$conn);
				foreach ($_cp_myteaminfo as $row2) {
					$_cp_myteam=$row2['team'];	
					$_cp_mycity=$row2['city'];	
					$_cp_mynickname=$row2['nickname'];	
				}
							
*/
				//Get values for Team / City / Nickname here so we get full Nickname
				$_cp_sql2="SELECT concat(`city`,' ', `nickname`)AS `team`, `city`, `nickname` FROM `bb_teams` WHERE 1=? AND `abbr`='$row[abbr]';";
				$_cp_val=1;
				$_cp_myteaminfo=nz_pdo_row($_cp_sql2,$_cp_val,$conn);
				#$title="Nickname check:-";
				#nz_debug($_cp_myteaminfo,$title);	
				$_cp_myteam=$_cp_myteaminfo['team'];	
				$_cp_mycity=$_cp_myteaminfo['city'];	
				$_cp_mynickname=$_cp_myteaminfo['nickname'];	
				//TODO fix this but it's quite complex!
				if ("J Hall"==$row['coach']) {
					$mycoach="Mark J Hall";
				} else {
					$mycoach=$row['coach'];
				}			

				$_cp_sql=  "INSERT INTO `bb_franchises` (`f_id`, `f_league`, `f_season`,`f_week`,`f_conference`, `f_division`, `f_ori_team`, `f_ori_city`, `f_ori_nickname`, `f_ori_coach`, `f_ori_size`, `f_ori_surface`, `f_ori_type`, `f_team`, `f_city`, `f_nickname`, `f_coach`, `f_size`, `f_surface`, `f_type`, `f_WinnerYears`, `f_ConferenceYears`, `f_DivisionYears`, `f_WildcardYears`, `f_Winner`, `f_Runnerup`, `f_ChampionshipW`, `f_ChampionshipL`, `f_DivisionW`, `f_Wildcard`, `f_MaxWins`, `f_MaxLosses`, `f_MaxScored`, `f_MaxConceded`, `f_Link`, `f_abbr`) 
				VALUES ('$row[id]', '$_cp_league', '$_cp_season','$_cp_week','$row[conf]', '$row[div]', '$_cp_myteam', '$_cp_mycity', '$_cp_mynickname', '$mycoach', '$row[ssize]', '$row[ssurface]', '$row[stype]', '$_cp_myteam', '$_cp_mycity', '$_cp_mynickname',  '$row[coach]', '$row[ssize]', '$row[ssurface]', '$row[stype]', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$row[abbr]');";
				nz_pdo($_cp_sql,$conn);
				$j++;
		}		
		$str="<h3>$j new records inserted to franchise table</h3>";
		output($str); 
		$_cp_sql = "UPDATE `g_turnsummary` SET `processed`= 2 WHERE `turn_id`=$_cp_turnid";
		myDB::query("$_cp_sql");
		#Build roster data into an array
		#TODO check this line
		#require 'g_process_baseballrosters.php';
	} else {
		$str="<h2>**Franchise table has $myarraysize records- investigation needed**</h2>";
		output($str); 
	}

	} else {
		$str="<h2>**Franchise array size is $myarraysize1 - investigation needed**</h2>";
		output($str); 
		$title="Franchise Data:-";
		nz_debug($_cp_franchiseraw,$title);

	}	

} else {
		$str="<h3>**Error - Roundup not found!**</h3>";
		output($str); 	
}		
 

?>

