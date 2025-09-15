<?php
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

echo "<p>Checking for Batting stats to be processed!</p>";

if (3==$_cp_processed) {
	$_cp_val=$_cp_processed;
	$_cp_sql="SELECT `turn_id` FROM `g_turnsummary` WHERE `processed`=? ORDER BY `turn_id` ASC LIMIT 1";
	$_cp_myid=nz_pdo_single($_cp_sql,$_cp_val,$conn);

		$_cp_sql="SELECT `batstat1`, `batstat2`, `batstat3`, `batstat4`, `batstat5`, `batstat6`, `batstat7`, `batstat8` FROM `g_turnsummary` WHERE `turn_id`=?";

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
		$batstat1=$row['batstat1'];
		$batstat2=$row['batstat2'];
		$batstat3=$row['batstat3'];
		$batstat4=$row['batstat4'];
		$batstat5=$row['batstat5'];
		$batstat6=$row['batstat6'];
		$batstat7=$row['batstat7'];
		$batstat8=$row['batstat8'];

		if (($batstat2-$batstat1)>250){
			echo "<p>Extra roundup found!</p>";
			$x=2;
			$y=3;
		} else {
			$x=1;
			$y=2;
		}

		while($y <= 8) {
				$mybatstat="batstat";
				$mybatstat.=$x;
				$mybatstat2="batstat";
				$mybatstat2.=$y;
				$mymin=$row[$mybatstat];
				$mymax=$row[$mybatstat2];
				echo "Looking for data between $mymin and $mymax<br>";
				$x++;
				$y++;
				$_cp_sql="SELECT `tf_line` FROM `g_turnsfull` WHERE `ts_id`=$_cp_myid AND `tf_seq`>=$mymin AND `tf_seq`<$mymax;";
				$_cp_myrows=nz_pdo_array($_cp_sql,$conn);
				foreach ($_cp_myrows as $_cp_myrow){ 
					#nz_debug($_cp_myrow);
					//TODO Better handling of special cases!
					$_cp_mytext=$_cp_myrow['tf_line'];
					$_cp_mytext=str_replace("P.Grand Pre","Preston GrandPre","$_cp_mytext");
					$_cp_mytext=str_replace("V.Guerrero Jr","Vlad GuerreroJr","$_cp_mytext");
					$_cp_mytext=str_replace("J.D. Martinez","JD Martinez","$_cp_mytext");
					$_cp_mytext=str_replace("Jung Ho Kang","Jung HoKang","$_cp_mytext");
					$_cp_mytext=str_replace("Ivan De Jesus","Ivan DeJesus","$_cp_mytext");					
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
					if (isset($_cp_text[26])) {
						#nz_debug($_cp_text);
						//Work out the PlayerID	
						//TODO Need to change this so stats are loaded into a roundup table and linked to the player master record
						if (is_numeric($_cp_text[0])) {
							$_cp_cabbr=substr($_cp_text[1],0,1);
							$_cp_sql="	SELECT a.`p_id`, a.`p_team`, `p_pos`, `p_hand`, `p_level`, b.f_conference 
										FROM `bb_players` a
											INNER JOIN `bb_franchises` b ON a.p_team=b.f_id 
										WHERE a.`f_league`='$_cp_league' AND a.`p_sh`=$_cp_text[0] AND LEFT(a.`p_cname`, 1)='$_cp_cabbr' AND a.`p_sname`='$_cp_text[2]' AND a.`p_pos`='$_cp_text[4]';";
							$stmt = $conn->prepare($_cp_sql);
							$stmt->execute();
							$player = $stmt->fetch();
							$_cp_playerid=$player[0];
							$_cp_teamid=$player[1];
							$_cp_pos=$player[2];
							$_cp_hand=$player[3];
							$_cp_level=$player[4];
							$_cp_conf=$player[5];
							#echo "<p>$_cp_text[1] $_cp_text[2] is $_cp_playerid ($_cp_teamid)</p>";
				
							//Calculate OPS
							$_cp_ops=($_cp_text[25]+$_cp_text[26]);

							//Calculate wOBA
							$_cp_woba=((0.69*$_cp_text[14])+(0.89*($_cp_text[8]-$_cp_text[10]-$_cp_text[11]-$_cp_text[12]))+(1.27*$_cp_text[10])+(1.62*$_cp_text[11])+(2.10*$_cp_text[12]))/($_cp_text[6]+$_cp_text[14]+$_cp_text[15]);
							
							

							//Insert stats into DB	
							$_cp_sql3="	INSERT INTO `bb_statsb` (`bs_id`, `league`, `season`, `week`, `p_id`, `pos`, `hand`, `level`, `p_team`,`p_conf`, `G`, `AB`, `R`, `H`, `TB`, `2B`, `3B`, `HR`, `RBI`, `BB`, `SA`, `SB`, `SO`, `FS`, `DP`, `Er`, `PO`, `As`, `FldP`, `BAvg`, `OnBs`, `SlgP`, `OPS`, `wOBA`) 
										VALUES (NULL, '$_cp_league', '$_cp_season', '$_cp_week', '$_cp_playerid', '$_cp_pos', '$_cp_hand', '$_cp_level', '$_cp_teamid', '$_cp_conf', '$_cp_text[5]', '$_cp_text[6]', '$_cp_text[7]', '$_cp_text[8]', '$_cp_text[9]', '$_cp_text[10]', '$_cp_text[11]', '$_cp_text[12]', '$_cp_text[13]', '$_cp_text[14]', '$_cp_text[15]', '$_cp_text[16]', '$_cp_text[17]', '$_cp_text[18]', '$_cp_text[19]', '$_cp_text[20]', '$_cp_text[21]', '$_cp_text[22]', '$_cp_text[23]', '$_cp_text[24]', '$_cp_text[25]', '$_cp_text[26]',$_cp_ops,$_cp_woba);";
							#echo "<p>$_cp_sql3</p>";
							$stmt3 = $conn->prepare($_cp_sql3);
							$stmt3->execute();
						} else {
							echo "<p>Skipping non player record!</p>";
							#echo "<p>$_cp_text</p>";
						}
					}
					
			}
		}

	//End of Stats extract
	//TODO set processed to correct number
	$_cp_sql = "UPDATE `g_turnsummary` SET `processed`= 4 WHERE `turn_id`=$_cp_turnid";
	myDB::query("$_cp_sql");

	$str="<h3>Batting Stats Updated!</h3>";
	output($str);	


} else {
	$str="<h2>No turns ready for batting stats processing!!</h2>";
	output($str);	
}
?>
