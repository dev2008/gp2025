<?php
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

// Report all errors except E_NOTICE
error_reporting(E_ALL & ~E_NOTICE);

echo "<p>Checking for Pitching stats to be processed!</p>";

if (4==$_cp_processed) {
	$_cp_val=$_cp_processed;
	$_cp_sql="SELECT `turn_id` FROM `g_turnsummary` WHERE `processed`=? ORDER BY `turn_id` ASC LIMIT 1";
	$_cp_myid=nz_pdo_single($_cp_sql,$_cp_val,$conn);

		$_cp_sql="SELECT `pitstat1`, `pitstat2`, `pitstat3`, `pitstat4`, `pitstat5`, `pitstat6`, `pitstat7`, `pitstat8` FROM `g_turnsummary` WHERE `turn_id`=?";

			try {
				$row = myDB::run("$_cp_sql", [$_cp_myid])->fetch();
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
		#nz_debug($row);
		//Allocate rows in turnset
		$pitstat1=$row['pitstat1'];
		$pitstat2=$row['pitstat2'];
		$pitstat3=$row['pitstat3'];
		$pitstat4=$row['pitstat4'];
		$pitstat5=$row['pitstat5'];
		$pitstat6=$row['pitstat6'];
		$pitstat7=$row['pitstat7'];
		$pitstat8=$row['pitstat8'];

		if (($pitstat2-$pitstat1)>250){
			echo "<p>Extra roundup found!</p>";
			$x=2;
			$y=3;
		} else {
			$x=1;
			$y=2;
		}

		//TODO Parametirise this
		while($y <= 7) {
				$mypitstat="pitstat";
				$mypitstat.=$x;
				$mypitstat2="pitstat";
				$mypitstat2.=$y;
				$mymin=$row[$mypitstat];
				$mymax=$row[$mypitstat2];
				echo "Looking for data between $mymin and $mymax<br>";
				$x++;
				$y++;
				$_cp_sql="SELECT `tf_line` FROM `g_turnsfull` WHERE `ts_id`=$_cp_myid AND `tf_seq`>=$mymin AND `tf_seq`<$mymax;";
				#$_cp_sql="SELECT `tf_line` FROM `g_turnsfull` WHERE `ts_id`=$_cp_myid AND `tf_seq`=2461";
				echo "<p>$_cp_sql</p>";
				$_cp_myrows=nz_pdo_array($_cp_sql,$conn);
				$kk=0;
				foreach ($_cp_myrows as $_cp_myrow){ 
					nz_debug($_cp_myrow,"Row");
					//TODO Better handling of special cases!
					$_cp_mytext=$_cp_myrow['tf_line'];
					$_cp_mytext=str_replace("P.Grand Pre","Preston GrandPre","$_cp_mytext");
					$_cp_mytext=str_replace("V.Guerrero Jr","Vlad GuerreroJr","$_cp_mytext");
					$_cp_mytext=str_replace("J.D. Martinez","JD Martinez","$_cp_mytext");
					$_cp_mytext=str_replace("Jung Ho Kang","Jung HoKang","$_cp_mytext");
					$_cp_mytext=str_replace("Ivan De Jesus","Ivan DeJesus","$_cp_mytext");		
					$_cp_mytext=str_replace("P J Walters","PJ Walters","$_cp_mytext");	
					$_cp_mytext=str_replace("Jen-Ho","JenHo","$_cp_mytext");	
					$_cp_mytext=str_replace("Van Eyk","VanEyk","$_cp_mytext");
					$_cp_mytext=str_replace("Y.Castillo","Yhonkervix Castillo","$_cp_mytext");
					$_cp_mytext=str_replace("A.J.","AJ","$_cp_mytext");	
					$_cp_mytext=str_replace("J.J.","JJ","$_cp_mytext");	
					$_cp_mytext=str_replace("T.J.","TJ","$_cp_mytext");	
					$_cp_mytext=str_replace("     1/3",".3","$_cp_mytext");
					$_cp_mytext=str_replace("    1/3",".3","$_cp_mytext");
					$_cp_mytext=str_replace("   1/3",".3","$_cp_mytext");
					$_cp_mytext=str_replace("  1/3",".3","$_cp_mytext");
					$_cp_mytext=str_replace(" 1/3",".3","$_cp_mytext");
					$_cp_mytext=str_replace("     2/3",".6","$_cp_mytext");
					$_cp_mytext=str_replace("    2/3",".6","$_cp_mytext");
					$_cp_mytext=str_replace("   2/3",".6","$_cp_mytext");
					$_cp_mytext=str_replace("  2/3",".6","$_cp_mytext");
					$_cp_mytext=str_replace(" 2/3",".6","$_cp_mytext");
					$_cp_mytext=str_replace("-"," ","$_cp_mytext");
					$_cp_mytext=str_replace(".A"," A","$_cp_mytext");
					$_cp_mytext=str_replace(".B"," B","$_cp_mytext");
					$_cp_mytext=str_replace(".C"," C","$_cp_mytext");
					$_cp_mytext=str_replace(".D"," D","$_cp_mytext");
					$_cp_mytext=str_replace(".E"," E","$_cp_mytext");
					$_cp_mytext=str_replace(".F"," F","$_cp_mytext");
					$_cp_mytext=str_replace(".G"," G","$_cp_mytext");
					$_cp_mytext=str_replace(".H"," H","$_cp_mytext");
					$_cp_mytext=str_replace(".I"," I","$_cp_mytext");
					$_cp_mytext=str_replace(".J"," J","$_cp_mytext");
					$_cp_mytext=str_replace(".K"," K","$_cp_mytext");
					$_cp_mytext=str_replace(".L"," L","$_cp_mytext");
					$_cp_mytext=str_replace(".M"," M","$_cp_mytext");
					$_cp_mytext=str_replace(".N"," N","$_cp_mytext");
					$_cp_mytext=str_replace(".O"," O","$_cp_mytext");
					$_cp_mytext=str_replace(".P"," P","$_cp_mytext");
					$_cp_mytext=str_replace(".Q"," Q","$_cp_mytext");
					$_cp_mytext=str_replace(".R"," R","$_cp_mytext");
					$_cp_mytext=str_replace(".S"," S","$_cp_mytext");
					$_cp_mytext=str_replace(".T"," T","$_cp_mytext");
					$_cp_mytext=str_replace(".U"," U","$_cp_mytext");
					$_cp_mytext=str_replace(".V"," V","$_cp_mytext");
					$_cp_mytext=str_replace(".W"," W","$_cp_mytext");
					$_cp_mytext=str_replace(".X"," X","$_cp_mytext");
					$_cp_mytext=str_replace(".Y"," Y","$_cp_mytext");
					$_cp_mytext=str_replace(".Z"," Z","$_cp_mytext");
					$_cp_text = preg_split("/[\s,]+/", $_cp_mytext);

					if (is_numeric($_cp_text[0])) {
						$_cp_mycount=count($_cp_text);
						if ($_cp_mycount<>24) {
							echo "<p>Row $kk - Error - $_cp_mycount</p>";
							nz_debug($_cp_myrow['tf_line'],"Row");
							nz_debug($_cp_text,"Row");
						}
						$_cp_g=$_cp_text[4];
						$_cp_gs=$_cp_text[5];
						$_cp_cg=$_cp_text[6];
						$_cp_gf=$_cp_text[7];
						$_cp_ip=$_cp_text[8];
						$_cp_h=$_cp_text[9];
						$_cp_r=$_cp_text[10];
						$_cp_er=$_cp_text[11];
						$_cp_hr=$_cp_text[12];
						$_cp_bb=$_cp_text[13];
						$_cp_so=$_cp_text[14];
						$_cp_sb=$_cp_text[15];
						$_cp_w=$_cp_text[16];
						$_cp_l=$_cp_text[17];
						$_cp_sv=$_cp_text[18];
						$_cp_ws=$_cp_text[19];
						$_cp_ls=$_cp_text[20];
						$_cp_rs=$_cp_text[21];
						$_cp_hav=$_cp_text[22];
						$_cp_era=$_cp_text[23];
						
					if (is_numeric($_cp_text[0])) {
						//Work out the PlayerID	
						$_cp_cabbr=substr($_cp_text[1],0,1);
						$_cp_sql="	SELECT a.`p_id`, a.`p_hand`, a.`p_level`, a.`p_team`, b.f_conference 
									FROM `bb_players` a
										INNER JOIN `bb_franchises` b ON a.p_team=b.f_id 
									WHERE a.`f_league`='$_cp_league' AND a.`p_sh`=$_cp_text[0] AND LEFT(a.`p_cname`, 1)='$_cp_cabbr' AND a.`p_sname`='$_cp_text[2]' AND a.`p_pos`='P';";
						echo "<p>$_cp_sql</p>";
						$stmt = $conn->prepare($_cp_sql);
						$stmt->execute();
						$player = $stmt->fetch();
						$_cp_playerid=$player[0];
						$_cp_hand=$player[1];
						$_cp_level=$player[2];
						$_cp_teamid=$player[3];
						$_cp_conf=$player[4];
						echo "<p>$_cp_text[1] $_cp_text[2] is $_cp_playerid ($_cp_teamid)</p>";
					
						//Calculate WHIP
						$_cp_whip=(($_cp_h+$_cp_bb)/$_cp_ip);
						$_cp_whip=number_format("$_cp_whip",3);

						//Calculate FIP
						$_cp_fip=((((13*$_cp_hr)+(3*($_cp_bb)))-(2*$_cp_so))/($_cp_ip+3.10));
						$_cp_fip=number_format("$_cp_fip",3);					
						
						//Insert stats into DB	
						$_cp_sql3="	INSERT INTO `bb_statsp` (`league`, `season`, `week`, `p_id`, `hand`, `level`, `p_team`, `conf`,`G`, `GS`, `CG`, `GF`, `IP`, `H`, `R`, `ER`, `HR`, `BB`, `SO`, `SB`, `W`, `L`, `Sv`, `WS`, `LS`, `RunS`, `HAvg`, `ERA`, `WHIP`, `FIP`) 
									VALUES ('$_cp_league', '$_cp_season', '$_cp_week', '$_cp_playerid', '$_cp_hand', '$_cp_level', '$_cp_teamid', '$_cp_conf','$_cp_g','$_cp_gs','$_cp_cg','$_cp_gf','$_cp_ip','$_cp_h','$_cp_r','$_cp_er','$_cp_hr','$_cp_bb','$_cp_so','$_cp_sb','$_cp_w','$_cp_l','$_cp_sv','$_cp_ws','$_cp_ls','$_cp_rs','$_cp_hav','$_cp_era','$_cp_whip','$_cp_fip' );";
						echo "<p>$_cp_sql3</p>";
						$stmt3 = $conn->prepare($_cp_sql3);
						$stmt3->execute();
						$kk++;
						#echo "<p>Processed $kk rows</p>";
					} else {
						echo "<p>Skipping non player record!</p>";
						#echo "<p>$_cp_text</p>";
					}	
												
					}
		
			}

		}

	//End of Stats extract
	//TODO set processed to correct number
	$_cp_sql = "UPDATE `g_turnsummary` SET `processed`= 5 WHERE `turn_id`=$_cp_turnid";
	myDB::query("$_cp_sql");

	$str="<h3>Pitching Stats Updated!</h3>";
	output($str);	


} else {
	$str="<h2>No turns ready for pitching stats processing!!</h2>";
	output($str);	
}
?>
