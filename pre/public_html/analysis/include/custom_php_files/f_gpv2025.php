<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require_once 'f_functions.php';
require_once 'g_functions.php';

/* ---------------------------
   W3 helpers (no <head>/<body>)
---------------------------- */
$render_header = function(string $title, string $subtitle = '') {
  echo '<div class="w3-container w3-padding">';
  echo '  <h3 class="w3-margin-0">'.$title.'</h3>';
  if ($subtitle !== '') {
    echo '  <p class="w3-text-grey w3-small">'.$subtitle.'</p>';
  }
  echo '</div>';
};

$render_card = function(string $title, string $content_html, string $colorClass = 'w3-white') {
  echo '<div class="w3-card w3-round-xxlarge w3-margin-top '.$colorClass.'">';
  echo '  <div class="w3-container w3-padding">';
  if ($title !== '') echo '    <h3 class="w3-margin-top">'.$title.'</h3>';
  echo $content_html;
  echo '  </div>';
  echo '</div>';
};

$render_list = function(string $title, array $items, string $paleClass = 'w3-pale-blue') use ($render_card) {
  ob_start();
  echo '<ul class="w3-ul w3-border w3-hoverable w3-small">';
  foreach ($items as $it) echo '<li>'.htmlspecialchars($it, ENT_QUOTES).'</li>';
  echo '</ul>';
  $render_card($title, ob_get_clean(), $paleClass);
};

$render_kv = function(array $rows) {
  // rows: [[label, value, optionalBadgeClass]]
  echo '<div class="w3-row-padding">';
  foreach ($rows as $r) {
    $label = $r[0]; $value = $r[1]; $badge = $r[2] ?? 'w3-blue';
    echo '<div class="w3-col s12 m6 l4 w3-margin-bottom">';
    echo '  <div class="w3-container w3-white w3-round-xxlarge w3-padding w3-border">';
    echo '    <div class="w3-small w3-text-grey">'.htmlspecialchars($label, ENT_QUOTES).'</div>';
    echo '    <div class="w3-xlarge"><span class="w3-badge '.$badge.' w3-large" style="vertical-align:middle">'.htmlspecialchars($value, ENT_QUOTES).'</span></div>';
    echo '  </div>';
    echo '</div>';
  }
  echo '</div>';
};

// override your plain output text with a subtle card line
$w3out = function(string $html) use ($render_card) {
  $render_card('', '<div class="w3-small">'.$html.'</div>', 'w3-white');
};

/* ---------------------------
   Page heading
---------------------------- */
$render_header('GPVCon online', 'League Records and Bowl Games');

//Fix Washington Name
#$str="<h3>Fixing Washington name</h3>";
#output($str);
$_cp_sql2 = "UPDATE `f_games` SET `team`='Washington Redskins' WHERE `team` LIKE 'Washington%'";
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 

$_cp_sql2 = "UPDATE `f_games` SET `opp_team`='Washington Redskins' WHERE `opp_team` LIKE 'Washington%'";
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 

$_cp_sql2 = "UPDATE `n_playbyplay` SET `a_off`='WC' WHERE `a_off`='WR' OR `a_off`='WT'";
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 

$_cp_sql2 = "UPDATE `n_playbyplay` SET `a_def`='WC' WHERE `a_def`='WR' OR `a_def`='WT'";
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 

//Update Gametypes
#$str="<h3>Updating Gametypes</h3>";
#output($str);

$_cp_sql = "UPDATE `f_games`
SET `gametype` =20
WHERE `gametype` =0 AND `league` LIKE 'NFL%'";
$i=0;
$result = $conn->prepare($_cp_sql);
$result->execute();
$number_of_rows = $result->rowCount() ;

$_cp_sql = "UPDATE `f_games`
SET `gametype` =22
WHERE `gametype` = 17 AND `league` LIKE 'NFL%'";
$i=0;
$result = $conn->prepare($_cp_sql);
$result->execute();
$number_of_rows = $result->rowCount() ;

$_cp_sql = "UPDATE `f_games`
SET `gametype` =24
WHERE `gametype` = 1 AND `league` LIKE 'NFL%'";
$i=0;
$result = $conn->prepare($_cp_sql);
$result->execute();
$number_of_rows = $result->rowCount() ;

$_cp_sql = "UPDATE `f_games`
SET `gametype` =35
WHERE `gametype` = 2 AND `league` LIKE 'NFL%'";
$i=0;
$result = $conn->prepare($_cp_sql);
$result->execute();
$number_of_rows = $result->rowCount() ;

$_cp_sql = "UPDATE `f_games`
SET `gametype` =36
WHERE `gametype` = 3 AND `league` LIKE 'NFL%'";
$i=0;
$result = $conn->prepare($_cp_sql);
$result->execute();
$number_of_rows = $result->rowCount() ;

$_cp_sql = "UPDATE `f_games`
SET `gametype` =34
WHERE `gametype` = 4 AND `league` LIKE 'NFL%'";
$i=0;
$result = $conn->prepare($_cp_sql);
$result->execute();
$number_of_rows = $result->rowCount() ;

$_cp_sql = "UPDATE `f_games`
SET `gametype` =32
WHERE `gametype` = 5 AND `league` LIKE 'NFL%'";
$i=0;
$result = $conn->prepare($_cp_sql);
$result->execute();
$number_of_rows = $result->rowCount() ;

$_cp_sql = "UPDATE `f_games`
SET `gametype` =30
WHERE `gametype` = 6 AND `league` LIKE 'NFL%'";
$i=0;
$result = $conn->prepare($_cp_sql);
$result->execute();
$number_of_rows = $result->rowCount() ;

$_cp_sql = "UPDATE `f_games`
SET `gametype` =28
WHERE `gametype` = 7 AND `league` LIKE 'NFL%'";
$i=0;
$result = $conn->prepare($_cp_sql);
$result->execute();
$number_of_rows = $result->rowCount() ;
##$str="<p>$number_of_rows Silver Bowl games converted </p>";


//Now process Franchises
$_cp_sql2 = "SELECT MIN(`season`) FROM `f_games` WHERE `franchise` = '' OR `franchise` is null OR LENGTH(`franchise`)=0";

$result = $conn->prepare($_cp_sql2); 
$result->execute(); 
$number_of_rows = $result->rowCount(); 
   while($row = fetch_row_db($result)){
			$_cp_myseason = $row[0];
			if (1==$debug_mode) {
#				#$str="<p>Season - $_cp_myseason</p>";
				##output($str);	
			}	
   }  
 

if (is_null($_cp_myseason)) {	
	#$str="<h3>All Franchises allocated!</h3>";
	#output($str);	
	} else {
	#$str="<h3>Allocating Franchises</h3>";
	#output($str);
	#Find league matching season with no franchise
	try {
		$_cp_sql2 = "SELECT `league` FROM `f_games` WHERE `season`= $_cp_myseason AND (`franchise` = '' OR `franchise` is null OR LENGTH(`franchise`)=0)";

		$result = $conn->prepare($_cp_sql2);
		$result->execute();
		$number_of_rows = $result->rowCount();
		while ($row = fetch_row_db($result)) {
			$_cp_myleague = $row[0];
			##$str="<p>$row[0] - $row[1]</p>";
		}


		##$str="<p>Rows - $number_of_rows $_cp_myleague $_cp_myseason</p>";

		#if ($number_of_rows > 0 ) {
		if (!empty($_cp_myleague)) {
			#Loop called if records to look at
			$_cp_sql3 = "SELECT MIN(`week`) FROM `f_games` WHERE `season` = $_cp_myseason AND `league` = '$_cp_myleague' AND (`franchise` = '' or `franchise` is null OR LENGTH(`franchise`)=0)";
			$res3 = execute_db($_cp_sql3, $conn);
			while ($row = fetch_row_db($res3)) {
				$_cp_myweek = $row[0];
			}

			#Print results
#			#$str="<p><h3>No Franchises found for $_cp_myleague s$_cp_myseason w$_cp_myweek.</h3></p>";
			#output($str);

			#Now we have found the first week to process loop through it and match Teams to Franchises
			#$str="<h3>Updating Franchises ......</h3>";
			#output($str);
			#Set Pro or College
			$_cp_mysub=substr($_cp_myleague,0,2);
			if ('NF'==$_cp_mysub) {
				$_cp_myfranchises='fp_franchises';
#				#$str="<p>Set Franchise to Pro - ".$_cp_mysub.".</p>";
			} else {
				$_cp_myfranchises='fc_franchises';
#				#$str="<p>Set Franchise to College - ".$_cp_mysub.".</p>";
			};

			//Now do the update
			$_cp_sql = "SELECT `id_game`, `league`, `season`, `week`, `team`, `franchise`, `coach`
	FROM `f_games`
	WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason AND `week`=$_cp_myweek AND (`franchise` = '' or `franchise` is null)";
			$res = execute_db($_cp_sql, $conn);
			while ($row = fetch_row_db($res)) {
				$_cp_mygameid=$row[0];
				$_cp_myteam=$row[4];
				$_cp_sql1 = "SELECT `franchise` FROM `$_cp_myfranchises` WHERE `league`='$_cp_myleague' AND `team`='$_cp_myteam'";
				##$str="<p>$_cp_sql1</p>";
				$res1 = execute_db($_cp_sql1, $conn);
				while ($row = fetch_row_db($res1)) {
					$_cp_myfranchise=$row[0];
					$_cp_sql2="UPDATE `f_games` SET `franchise`= $_cp_myfranchise, modification_time=NOW() WHERE `id_game` ='$_cp_mygameid' ";
					##$str="<p>$_cp_sql2</p>";
					$res2 = execute_db($_cp_sql2, $conn);
				}
			}

			//Check if all records have been allocated
			$_cp_sql3="SELECT COUNT(*) AS UnMatched, `league`, `season`, `week`
						FROM `f_games`
						WHERE (`franchise` = '' OR `franchise` IS NULL) AND `league`='$_cp_myleague' AND `season`=$_cp_myseason AND `week`=$_cp_myweek";
			#echo "$_cp_sql3";
			$res3 = execute_db($_cp_sql3, $conn);
			while ($row = fetch_row_db($res3)) {
				$_cp_myunmatched=$row[0];
			}

			switch ($_cp_myunmatched) {
				case 0:
					#$str="<p><h3>All Franchises allocated for $_cp_myleague s$_cp_myseason w$_cp_myweek.</h3></p>";
					#output($str);
				break;
				case 1:
					#$str="<p><h3>1 Franchise moved!</h3></p>";
					#output($str);
					if ('fp_franchises'==$_cp_myfranchises){
						require_once 'fp_movefranchise.php';	
					} else {
						require_once 'fc_movefranchise.php';	
					}
					
				break;				
				default:
					#$str="<p><h3>$_cp_myunmatched Franchises moved!</h3></p>";
					$str.="<p><h3>Currently we only support single Franchise moves!</h3></p>";
					#output($str);
					break;
			}


			#End of overall loop
		} else {
			#$str="<p><h3>All Franchises allocated in database.</h3></p>";
			#output($str);
		}
	} catch (PDOException $e) {
		#$str="<p>SQL - $_cp_sql2</p>";
		$str.="DataBase Error:<br>".$e->getMessage();
		#output($str);
	} catch (Exception $e) {
		#$str="<p>SQL - $_cp_sql2</p>";
		$str.="General Error:<br>".$e->getMessage();
		#output($str);
	}
}

//Start League Loop
$_cp_sql = "SELECT Distinct League FROM `f_games` ORDER BY `League` ASC";

$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$number_of_rows = $result->rowCount() ; 
#$str="<h3>Found $number_of_rows leagues</h3>";
#output($str);
$j=0;
//This is the main League loop
while($row = fetch_row_db($result)){
	$_cp_myleague = $row[0];
	##$str="<p>League is $_cp_myleague</p>";
	##output($str);	

	//Start Season Loop
	//Loop through each Season for this League
	$_cp_sql = "SELECT Distinct `Season`  FROM `f_games` WHERE `League` = '$_cp_myleague' ORDER BY `Season` ASC";
	$result2 = $conn->prepare($_cp_sql); 
	$result2->execute(); 
	$number_of_rows2 = $result2->rowCount() ; 
	$j=$j+$number_of_rows2;
	##$str="<h3>Found $number_of_rows records</h3>";
	##output($str);
	#$str="<h3>Looping through $number_of_rows2 $_cp_myleague Seasons.</h3>";
	$myseasons=$number_of_rows;
	$myprocessedseasons=0;
	$mycount=0;
	#output($str);
	while($row = fetch_row_db($result2)){
		$_cp_myseason = $row[0];
		
		//Start Week Loop

			$_cp_sql = "SELECT Distinct `Week` FROM `f_games` WHERE `League` = '$_cp_myleague' and `Season` = $_cp_myseason AND `Week` < 90 ORDER BY `Week` ASC";
			$result3 = $conn->prepare($_cp_sql); 
			$result3->execute(); 
			$number_of_rows = $result3->rowCount() ; 
			##$str="$_cp_sql";
			##output($str);
			//Loop through results
			while($row = fetch_row_db($result3)){
				$_cp_myweek = $row[0];

					//Start Opp Franchise loop
					$_cp_sql4 = "SELECT Distinct `opp_team` FROM `f_games` WHERE  `League` = '$_cp_myleague' and `Season` = $_cp_myseason and `Week` = $_cp_myweek ORDER BY `opp_team` ASC";
					$result4 = $conn->prepare($_cp_sql4); 
					$result4->execute(); $_cp_sql4;
					$number_of_rows = $result4->rowCount() ; 
					//Loop through results
					while($row4 = fetch_row_db($result4)){
						$_cp_myoppteam = $row4[0];	
						#echo "<p>League is $_cp_myleague, Season is $_cp_myseason, Week is $_cp_myweek, Franchise is $_cp_myoppteam</p>";
						$i++;

							//Now do the update
							//Because of stupid MySQL bug we now need to fetch the franchise ID before the update 
							//https://stackoverflow.com/questions/4429319/you-cant-specify-target-table-for-update-in-from-clause?rq=1
							$_cp_sql5 = "SELECT `franchise` FROM `f_games` WHERE `team`= '$_cp_myoppteam' AND `league` = '$_cp_myleague' AND `season` = $_cp_myseason AND `week`=$_cp_myweek";
							#echo "<p>$_cp_sql5</p>";
							$result5 = $conn->prepare($_cp_sql5); 
							$result5->execute(); $_cp_sql5;
							
							while($row5 = fetch_row_db($result5)){
								$_cp_myoppfranchise = $row5[0];	
								$_cp_sql6 = "
								UPDATE `f_games` 
								SET `opp_franchise` = $_cp_myoppfranchise
								WHERE `opp_team`='$_cp_myoppteam' AND `league` = '$_cp_myleague' AND `season` = $_cp_myseason AND `week`=$_cp_myweek  ";
								#echo "<p>$_cp_sql6</p>";

									try {
										$result6 = $conn->prepare($_cp_sql6); 
										$result6->execute(); 
										$number_of_rows6 = $result6->rowCount() ; 
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

	};
							//End of update loop
							}


					//End Opp Franchise loop
					}

		//End Week Loop


		}

//End Season Loop


//End League loop

}

//Fix Tampa Bay
#$str="<h3>Fixing Tampa Bay bug</h3>";
#output($str);
$_cp_sql = "UPDATE `f_games` SET `opp_team` = 'Tampa Bay Buccs' WHERE `opp_team` LIKE 'Tampa Bay Buccaneers'";
$result0 = $conn->prepare($_cp_sql); 
$result0->execute(); 
#$str="<h3>Setting game types.</h3>";
#output($str);

//ensure correct gametypes
$_cp_sql2 = "	UPDATE `f_games` 
				SET `gametype`=1 
				WHERE `league` LIKE 'NCAA%' AND `week`>0 AND `week`<12";

$res2 = execute_db($_cp_sql2, $conn);
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 

$_cp_sql2 = "	UPDATE `f_games` 
				SET `gametype`=24
				WHERE `league` LIKE 'NFL%' AND `week`>0 AND `week`<17";

$res2 = execute_db($_cp_sql2, $conn);
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 

$_cp_sql2 = "	UPDATE `f_games` 
				SET `gametype`=30
				WHERE `league` LIKE 'NFL%' AND `week`=17 AND gametype =20";

$res2 = execute_db($_cp_sql2, $conn);
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 

$_cp_sql2 = "	UPDATE `f_games` 
				SET `gametype`=8 
				WHERE `gametype`=3 AND `league` LIKE 'NCAA%' AND `week`=13";

$res2 = execute_db($_cp_sql2, $conn);
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 

//PDO Example with row count
$_cp_sql2 = "	UPDATE `f_games` 
				SET `gametype`=9 
				WHERE `gametype`=4 AND `league` LIKE 'NCAA%' AND `week`=13";

$res2 = execute_db($_cp_sql2, $conn);
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 
##$str="<h3>Set game type to Rose Bowl</h3>";
##output($str);

//PDO Example with row count
$_cp_sql2 = "	UPDATE `f_games` 
				SET `gametype`=10 
				WHERE `gametype`=6 AND `league` LIKE 'NCAA%' AND `week`=13";

$res2 = execute_db($_cp_sql2, $conn);
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 
##$str="<h3>Set game type to Cotton Bowl</h3>";
##output($str);

//PDO Example with row count
$_cp_sql2 = "	UPDATE `f_games` 
				SET `gametype`=12
				WHERE `gametype`=7 AND `league` LIKE 'NCAA%' AND `week`=13";

$res2 = execute_db($_cp_sql2, $conn);
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 
##$str="<h3>Set game type to Hawaii Bowl</h3>";
##output($str);


//PDO Example with row count
$_cp_sql2 = "	UPDATE `f_games` 
				SET `gametype`=14 
				WHERE `gametype`=2 AND `league` LIKE 'NCAA%' AND `week`=12";

$res2 = execute_db($_cp_sql2, $conn);
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 
##$str="<h3>Set game type to NC Playoffs</h3>";
##output($str);


//PDO Example with row count
$_cp_sql2 = "	UPDATE `f_games` 
				SET `gametype`=15 
				WHERE `gametype`=6 AND `league` LIKE 'NCAA%' AND `week`=12";

$res2 = execute_db($_cp_sql2, $conn);
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 
##$str="<h3>Set game type to Cotton Bowl playoffs</h3>";
#output($str);

//PDO Example with row count
$_cp_sql2 = "	UPDATE `f_games` 
				SET `gametype`=16 
				WHERE `gametype`=7 AND `league` LIKE 'NCAA%' AND `week`=12";

$res2 = execute_db($_cp_sql2, $conn);
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 

#$str="<h3>All game types set to correct values.</h3>";
#output($str);

//Temporary fix for completed seasons
$_cp_sql = "SELECT Distinct `id_game` FROM `f_games` WHERE `week`<90 AND `season` > 2020 ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$number_of_rows = number_format($result->rowCount() ); 
##$str="<h3>Temporary fix for completed seasons</h3>";
#$str="<h3>Assigning results to $number_of_rows records</h3>";
#output($str);
$mycount=0;	

//Loop through results

	while($row = fetch_row_db($result)){
		$_cp_myid = $row[0];
		$_cp_sql = "UPDATE `f_games` SET `win`=1, `lose`=0, `tie`=0  WHERE `id_game` = '$_cp_myid' AND `score` > `opp_score` AND `week` < 90";
		$result1 = $conn->prepare($_cp_sql); 
		$result1->execute(); 
		$_cp_sql = "UPDATE `f_games` SET `win`=0, `lose`=1, `tie`=0  WHERE `id_game` = '$_cp_myid' AND `score` < `opp_score` AND `week` < 90";
		$result2 = $conn->prepare($_cp_sql); 
		$result2->execute(); 
		$_cp_sql = "UPDATE `f_games` SET `win`=0, `lose`=0, `tie`=1  WHERE `id_game` = '$_cp_myid' AND `score` = `opp_score` AND `week` < 90";
		$result3 = $conn->prepare($_cp_sql); 
		$result3->execute(); 
	}        
$str='Processed ';
$str.=number_format($mycount);
$str.=' records.<br>';
#output($str);
				
//PDO Example with row count
$_cp_sql = "SELECT SUM(`win`),SUM(`lose`),SUM(`tie`) FROM `f_games` WHERE `week`<90";
$result4 = $conn->prepare($_cp_sql); 
$result4->execute(); 

//Loop through results
while($row = fetch_row_db($result4)){
	$wins=number_format($row[0]);
	$losses=number_format($row[1]);
	$ties=number_format($row[2]);
	$str= "<h3>Found: wins -  $wins / losses - $losses / ties - $ties</h3>";
	#output($str);
   }

$str= "<h3>Cross checking $wins records.</h3>";
#output($str);

//Loop through all games and extract details
$_cp_sql = "SELECT `id_game`,`league`, `season`, `week`, `team`, `franchise`, `coach` 
			FROM `f_games` 
			WHERE `week`<90 AND `win`=1";
$result5 = $conn->prepare($_cp_sql); 
$result5->execute(); 
$number_of_rows5 = number_format($result5->rowCount()) ; 
$i=0;
$mycount2=0;	
//Loop through results
while($row = fetch_row_db($result5)){
	//Check for corresponding loss
	$_cp_myid = $row[0];
	$_cp_myleague = $row[1];
	$_cp_myseason = $row[2];
	$_cp_myweek = $row[3];
	$_cp_myteam = $row[4];
	//NZ 20200618 This was <90 but picking up old NFLAR post season games 
	//WHERE `week`<17 AND `lose`=1 AND `league`='$_cp_myleague' AND `season`='$_cp_myseason' AND `week`=$_cp_myweek AND `opp_team`='$_cp_myteam'";
try {
    $_cp_sql = "SELECT `id_game`
                FROM `f_games` 
                WHERE `lose`=1 
                  AND `league`=:league 
                  AND `season`=:season 
                  AND `week`=:week 
                  AND `opp_team`=:opp_team";
    
    // Prepare the SQL query
    $result6 = $conn->prepare($_cp_sql); 
   
    // Bind parameters
    $result6->bindParam(':league', $_cp_myleague);
    $result6->bindParam(':season', $_cp_myseason);
    $result6->bindParam(':week', $_cp_myweek);
    $result6->bindParam(':opp_team', $_cp_myteam);
    
    // Execute the query
    $result6->execute(); 
    $number_of_rows6 = $result6->rowCount();
   
    // Output no-match message and SQL query if no match is found
    if (1 <> $number_of_rows6 AND $_cp_myweek < 17) {
        // Manually substitute the parameters into the query for output
        $sql_with_params = str_replace(
            [':league', ':season', ':week', ':opp_team'],
            [$conn->quote($_cp_myleague), $conn->quote($_cp_myseason), (int)$_cp_myweek, $conn->quote($_cp_myteam)],
            $_cp_sql
        );
        
        // Output the SQL query and no match message
        $str = "<h3 class='w3-red'>No match found for <em>$_cp_myid ($_cp_myleague $_cp_myseason $_cp_myweek $_cp_myteam)</em></h3>";
        $str .= "<p>SQL Query: <code>" . htmlspecialchars($sql_with_params) . "</code></p>";
        #output($str);
    }
} catch (PDOException $e) {
    // Log or handle the error
    echo "Error: " . $e->getMessage();
}



		$mycount2++;	

   }
$str='Checked ';
$str.=number_format($mycount2);
$str.=' records.<br><hr>';
#output($str);

echo "<div class='w3-red'>";
//check Wild Card records
//PDO Example with row count
$_cp_sql = "SELECT DISTINCT `league`, `season`
			FROM  `f_games` 
			WHERE `league` LIKE 'NFL%'  
			AND `season` NOT IN (SELECT MAX(`season`) FROM  `f_games`  WHERE `league` LIKE 'NFL%' ) 
			AND `season` NOT IN (SELECT `season` FROM  `f_games`  WHERE `league` LIKE 'NFL%' AND `week` = 17 AND `gametype` = '35');";
$result4 = $conn->prepare($_cp_sql); 
$result4->execute(); 
//Loop through results
while($row = fetch_row_db($result4)){
	$league=$row[0];
	$season=$row[1];
	$str= "<h2>No Wild Card games found for $league s$season w17</h2>";
	#output($str);
   }

//Check Divisional records
//PDO Example with row count
$_cp_sql = "SELECT DISTINCT `league`, `season`
			FROM  `f_games` 
			WHERE `league` LIKE 'NFL%'  
			AND `season` NOT IN (SELECT MAX(`season`) FROM  `f_games`  WHERE `league` LIKE 'NFL%' ) 
			AND `season` NOT IN (SELECT `season` FROM  `f_games`  WHERE `league` LIKE 'NFL%' AND `week` = 18 AND `gametype` = '35');";
$result4 = $conn->prepare($_cp_sql); 
$result4->execute(); 
//Loop through results
while($row = fetch_row_db($result4)){
	$league=$row[0];
	$season=$row[1];
	$str= "<h2>No Divisional Round games found for $league s$season w18</h2>";
	#output($str);
   }

//Check Championship records
//PDO Example with row count
$_cp_sql = "SELECT DISTINCT `league`, `season`
			FROM  `f_games` 
			WHERE `league` LIKE 'NFL%'  
			AND `season` NOT IN (SELECT MAX(`season`) FROM  `f_games`  WHERE `league` LIKE 'NFL%' ) 
			AND `season` NOT IN (SELECT `season` FROM  `f_games`  WHERE `league` LIKE 'NFL%' AND `week` = 19 AND `gametype` =35);";
$result4 = $conn->prepare($_cp_sql); 
$result4->execute(); 
//Loop through results
while($row = fetch_row_db($result4)){
	$league=$row[0];
	$season=$row[1];
	$str= "<h2>No Championship Games found for $league s$season w19</h2>";
	#output($str);
   }

//Check Superbowl records
//PDO Example with row count
$_cp_sql = "SELECT DISTINCT `league`, `season`
			FROM  `f_games` 
			WHERE `league` LIKE 'NFL%'  
			AND `season` NOT IN (SELECT MAX(`season`) FROM  `f_games`  WHERE `league` LIKE 'NFL%' ) 
			AND `season` NOT IN (SELECT `season` FROM  `f_games`  WHERE `league` LIKE 'NFL%' AND `week` = 20 AND `gametype` = '36');";
$result4 = $conn->prepare($_cp_sql); 
$result4->execute(); 
//Loop through results
while($row = fetch_row_db($result4)){
	$league=$row[0];
	$season=$row[1];
	$str= "<h2>No Superbowl found for $league s$season w20</h2>";
	#output($str);
   }

//Check College Playoff records
//PDO Example with row count
$_cp_sql = "SELECT DISTINCT `league`, `season`
			FROM  `f_games` 
			WHERE `league` LIKE 'NCAA%'  
			AND `season` NOT IN (SELECT MAX(`season`) FROM  `f_games`  WHERE `league` LIKE 'NCAA%' ) 
			AND `season` NOT IN (SELECT `season` FROM  `f_games`  WHERE `league` LIKE 'NCAA%' AND `week` = 12 AND (`gametype` > 13 AND `gametype` < 17));";
$result4 = $conn->prepare($_cp_sql); 
$result4->execute(); 
//Loop through results
while($row = fetch_row_db($result4)){
	$league=$row[0];
	$season=$row[1];
	$str= "<h2>No Playoff games found for $league s$season w12</h2>";
	#output($str);
   }

//Check College Bowl records
//PDO Example with row count
$_cp_sql = "SELECT DISTINCT `league`, `season`
			FROM  `f_games` 
			WHERE `league` LIKE 'NCAA%'  
			AND `season` NOT IN (SELECT MAX(`season`) FROM  `f_games`  WHERE `league` LIKE 'NCAA%' ) 
			AND `season` NOT IN (SELECT `season` FROM  `f_games`  WHERE `league` LIKE 'NCAA%' AND `week` = 13 AND (`gametype` > 7 AND `gametype` < 14));";
$result4 = $conn->prepare($_cp_sql); 
$result4->execute(); 
//Loop through results
while($row = fetch_row_db($result4)){
	$league=$row[0];
	$season=$row[1];
	$str= "<h2>No Bowl games found for $league s$season w13</h2>";
	#output($str);
   }

echo "</div>";


//Text for middle box
##$str="<h2>Started update process</h3>";
#output($str);
//Reset all records
#$str="<h2>Resetting all Pro records</h3>";
#output($str);
$_cp_sql = "UPDATE `fp_franchises` SET `Winner`=0,`Runnerup`=0,`ChampionshipW`=0,`ChampionshipL`=0,`DivisionW`=0,`Wildcard`=0, `WinnerYears`='', `DivisionYears`='',ConferenceYears='',WildcardYears=''  WHERE `ftype`='Pro'";
$res = execute_db($_cp_sql, $conn);
//Reset Pro Games
$_cp_sql = "TRUNCATE fp_vgames";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

// Disable strict mode for the session
$conn->exec("SET SESSION sql_mode = '';");
//Fix invalid datetimes
$_cp_sql = "UPDATE `f_games`
			SET `creation_time` = NOW() 
			WHERE `creation_time` = '0000-00-00 00:00:00';";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$_cp_sql = "UPDATE `f_games`
			SET `modification_time` = NOW() 
			WHERE `creation_time` = '0000-00-00 00:00:00';";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
// Re-enable strict mode after the update
$conn->exec("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE';");

$_cp_sql = "INSERT INTO fp_vgames 
			SELECT `a`.`id_game` AS `id_game`,`a`.`league` AS `league`,`a`.`season` AS `season`,`a`.`week` AS `week`,`a`.`team` AS `team`,`a`.`franchise` AS `franchise`,`a`.`coach` AS `coach`,`a`.`qb` AS `qb`,`a`.`safe` AS `safe`,`a`.`q1` AS `q1`,`a`.`q2` AS `q2`,`a`.`q3` AS `q3`,`a`.`q4` AS `q4`,`a`.`ot` AS `ot`,`a`.`score` AS `score`,`a`.`fga` AS `fga`,`a`.`fgg` AS `fgg`,`a`.`epa` AS `epa`,`a`.`epg` AS `epg`,`a`.`cva` AS `cva`,`a`.`cvg` AS `cvg`,`a`.`punts` AS `punts`,`a`.`thirdcon` AS `thirdcon`,`a`.`thirddowns` AS `thirddowns`,`a`.`fourthcon` AS `fourthcon`,`a`.`fourthdowns` AS `fourthdowns`,`a`.`firstd` AS `firstd`,`a`.`passcmp` AS `passcmp`,`a`.`passatt` AS `passatt`,`a`.`passyds` AS `passyds`,`a`.`passlng` AS `passlng`,`a`.`passlngtd` AS `passlngtd`,`a`.`passtd` AS `passtd`,`a`.`passpct` AS `passpct`,`a`.`interception` AS `interception`,`a`.`hrd` AS `hrd`,`a`.`skd` AS `skd`,`a`.`rush` AS `rush`,`a`.`rushyds` AS `rushyds`,`a`.`rushlng` AS `rushlng`,`a`.`rushlngtd` AS `rushlngtd`,`a`.`rushtd` AS `rushtd`,`a`.`fum` AS `fum`,`a`.`qbatt` AS `qbatt`,`a`.`qbyds` AS `qbyds`,`a`.`kr` AS `kr`,`a`.`kryds` AS `kryds`,`a`.`krtd` AS `krtd`,`a`.`pr` AS `pr`,`a`.`pryds` AS `pryds`,`a`.`prtd` AS `prtd`,`a`.`form1` AS `form1`,`a`.`form2` AS `form2`,`a`.`run1` AS `run1`,`a`.`run2` AS `run2`,`a`.`pass1` AS `pass1`,`a`.`pass2` AS `pass2`,`a`.`def1` AS `def1`,`a`.`def2` AS `def2`,`a`.`homeaway` AS `homeaway`,`a`.`gametype` AS `gametype`,`a`.`opp_team` AS `opp_team`,`a`.`opp_franchise` AS `opp_franchise`,`a`.`opp_coach` AS `opp_coach`,`a`.`opp_qb` AS `opp_qb`,`a`.`opp_safe` AS `opp_safe`,`a`.`opp_q1` AS `opp_q1`,`a`.`opp_q2` AS `opp_q2`,`a`.`opp_q3` AS `opp_q3`,`a`.`opp_q4` AS `opp_q4`,`a`.`opp_ot` AS `opp_ot`,`a`.`opp_score` AS `opp_score`,`a`.`opp_fga` AS `opp_fga`,`a`.`opp_fgg` AS `opp_fgg`,`a`.`opp_epa` AS `opp_epa`,`a`.`opp_epg` AS `opp_epg`,`a`.`opp_cva` AS `opp_cva`,`a`.`opp_cvg` AS `opp_cvg`,`a`.`opp_punts` AS `opp_punts`,`a`.`opp_thirdcon` AS `opp_thirdcon`,`a`.`opp_thirddowns` AS `opp_thirddowns`,`a`.`opp_fourthcon` AS `opp_fourthcon`,`a`.`opp_fourthdowns` AS `opp_fourthdowns`,`a`.`opp_firstd` AS `opp_firstd`,`a`.`opp_passcmp` AS `opp_passcmp`,`a`.`opp_passatt` AS `opp_passatt`,`a`.`opp_passyds` AS `opp_passyds`,`a`.`opp_passlng` AS `opp_passlng`,`a`.`opp_passlngtd` AS `opp_passlngtd`,`a`.`opp_passtd` AS `opp_passtd`,`a`.`opp_passpct` AS `opp_passpct`,`a`.`opp_interception` AS `opp_interception`,`a`.`opp_hrd` AS `opp_hrd`,`a`.`opp_skd` AS `opp_skd`,`a`.`opp_rush` AS `opp_rush`,`a`.`opp_rushyds` AS `opp_rushyds`,`a`.`opp_rushlng` AS `opp_rushlng`,`a`.`opp_rushlngtd` AS `opp_rushlngtd`,`a`.`opp_rushtd` AS `opp_rushtd`,`a`.`opp_fum` AS `opp_fum`,`a`.`opp_qbatt` AS `opp_qbatt`,`a`.`opp_qbyds` AS `opp_qbyds`,`a`.`opp_kr` AS `opp_kr`,`a`.`opp_kryds` AS `opp_kryds`,`a`.`opp_krtd` AS `opp_krtd`,`a`.`opp_pr` AS `opp_pr`,`a`.`opp_pryds` AS `opp_pryds`,`a`.`opp_prtd` AS `opp_prtd`,`a`.`opp_form1` AS `opp_form1`,`a`.`opp_form2` AS `opp_form2`,`a`.`opp_run1` AS `opp_run1`,`a`.`opp_run2` AS `opp_run2`,`a`.`opp_pass1` AS `opp_pass1`,`a`.`opp_pass2` AS `opp_pass2`,`a`.`opp_def1` AS `opp_def1`,`a`.`opp_def2` AS `opp_def2`,`a`.`win` AS `win`,`a`.`lose` AS `lose`,`a`.`tie` AS `tie`,`a`.`creation_time` AS `creation_time`,`a`.`modification_time` AS `modification_time`,`a`.`modification_by` AS `modification_by`,`a`.`modification_from` AS `modification_from` 
			FROM `f_games` `a` 
			WHERE `a`.`league` like '%NFL%' and `a`.`week` < 21 and `a`.`score` <> 1 and `a`.`opp_score` <> 1 and `a`.`homeaway` = 1 
			ORDER BY `a`.`league`,`a`.`season` desc,`a`.`week`,`a`.`franchise`";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 


//Retrieve all Pro Leagues
$_cp_sql = "SELECT DISTINCT `league` 
FROM `fp_franchises` 
WHERE `ftype` = 'Pro'
ORDER BY `league`";
$res = execute_db($_cp_sql, $conn);
		$mycount=0;	
        while($row = fetch_row_db($res)){
				$_cp_myleague=$row[0];
				$_cp_sql2 = "SELECT DISTINCT `season`
				FROM `f_games` 
				WHERE `league`= '$_cp_myleague'
				ORDER BY  `season` DESC";
				
				$res2 = execute_db($_cp_sql2, $conn);
				$number_of_rows = number_format($res2->rowCount() ); 
				#$str="<h2>Looping through $number_of_rows seasons</h3>";
				#output($str);
				while($row2 = fetch_row_db($res2)){
					$_cp_myseason=$row2[0];
					$mycount++;	

				//Looping through Seasons	
				//Find Superbowl wins
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=1 AND `gametype`=36 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbwinner=$row3[0];
						$_cp_sql4 = "UPDATE `fp_franchises` SET Winner=Winner+1  WHERE `franchise`=$_cp_mysbwinner";
						$res4 = execute_db($_cp_sql4, $conn);
						#echo "<p>Superbowl winner for $_cp_myleague - $_cp_myseason is $row3[1]</p>";
						$_cp_sql5 = "UPDATE `fp_franchises`  SET WinnerYears = CONCAT(WinnerYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mysbwinner";
						$res5 = execute_db($_cp_sql5, $conn);	
					$_cp_sql15 = "UPDATE `fp_franchises`  SET ConferenceYears = CONCAT(ConferenceYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mysbwinner";
					$res15 = execute_db($_cp_sql15, $conn);			
					$_cp_sql6 = "UPDATE `fp_franchises` SET ChampionshipW=ChampionshipW+1  WHERE `franchise`=$_cp_mysbwinner";
					$res6 = execute_db($_cp_sql6, $conn);	

					}

				//Find Superbowl losses
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=0 AND `gametype`=36 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbloser=$row3[0];
						$_cp_sql4 = "UPDATE `fp_franchises` SET Runnerup=Runnerup+1  WHERE `franchise`=$_cp_mysbloser";
						$res4 = execute_db($_cp_sql4, $conn);
					$_cp_sql15 = "UPDATE `fp_franchises`  SET ConferenceYears = CONCAT(ConferenceYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mysbloser";
					$res15 = execute_db($_cp_sql15, $conn);									
					$_cp_sql6 = "UPDATE `fp_franchises` SET ChampionshipW=ChampionshipW+1  WHERE `franchise`=$_cp_mysbloser";
					$res6 = execute_db($_cp_sql6, $conn);	

					}

				//Find Championship Game wins
				$_cp_sql5 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=1 AND `gametype`=35 AND `week`=19 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res5 = execute_db($_cp_sql5, $conn);
					while($row5 = fetch_row_db($res5)){
						$_cp_mycgwinner=$row5[0];
					}

				//Find Championship Game losses
				$_cp_sql7 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=0 AND `gametype`=35 AND `week`=19 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res7 = execute_db($_cp_sql7, $conn);
					while($row7 = fetch_row_db($res7)){
						$_cp_mysbwinner=$row7[0];
						$_cp_sql8 = "UPDATE `fp_franchises` SET ChampionshipL=ChampionshipL+1  WHERE `franchise`=$_cp_mysbwinner";
						$res8 = execute_db($_cp_sql8, $conn);	
					}

				//Find Wildcard Appearances
				$_cp_sql9 = "SELECT `franchise`,`team` FROM `f_games` WHERE `gametype`=35 AND `week`=17 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res9 = execute_db($_cp_sql9, $conn);
					while($row9 = fetch_row_db($res9)){
						$_cp_mywildcard=$row9[0];
						$_cp_sql10 = "UPDATE `fp_franchises` SET Wildcard=Wildcard+1  WHERE `franchise`=$_cp_mywildcard";
						$res10 = execute_db($_cp_sql10, $conn);	
					$_cp_sql15 = "UPDATE `fp_franchises`  SET WildcardYears = CONCAT(WildcardYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mywildcard";
					$res15 = execute_db($_cp_sql15, $conn);	
	
					}
				

				//Find Division wins
				$_cp_sql3 = "	SELECT `franchise` FROM `f_games` WHERE `week` =18 AND `season` = $_cp_myseason AND `gametype`=35
								AND `franchise` NOT IN (SELECT `franchise` FROM `f_games` WHERE `gametype`=35 AND `week`=17 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason)";
				#echo "<p>$_cp_sql3</p>";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mydivwinner=$row3[0];
						$_cp_sql4 = "UPDATE `fp_franchises` SET DivisionW=DivisionW+1  WHERE `franchise`=$_cp_mydivwinner";
						$res4 = execute_db($_cp_sql4, $conn);
						$_cp_sql5 = "UPDATE `fp_franchises`  SET DivisionYears = CONCAT(DivisionYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mydivwinner";
						$res5 = execute_db($_cp_sql5, $conn);	
					}


					
				#Season loop ended	
				}  
        }  
$str='Processed ';
$str.=$mycount;
$str.=' Seasons.';
#output($str);        
 
 
//Populate Bowls Table
$_cp_sql14 = "TRUNCATE fp_bowlgames";

$result14 = $conn->prepare($_cp_sql14); 
$result14->execute(); 

$_cp_sql15 = "INSERT INTO `fp_bowlgames` 
SELECT CONCAT(`a`.`league`,'-',`a`.`season`),`a`.`league` AS `league`,`a`.`season` AS `season`,`a`.`franchise` AS `franchise`,`b`.`team` AS `AFC_Champions`,`a`.`coach` AS `AFC_Coach`, 
concat(`a`.`score`,' - ',`a`.`opp_score`) AS `Score`,`a`.`opp_franchise` AS `opp_franchise`,`c`.`team` AS `NFC_Champions`,`a`.`opp_coach` AS `NFC_Coach`,`a`.`win` AS `win`, `a`.`score` AS `afc_score`,`a`.`opp_score`  AS `nfc_score`
from `f_games` `a` 
	inner join `fp_franchises` `b` on(`a`.`franchise` = `b`.`franchise`)
	inner join `fp_franchises` `c` on(`a`.`opp_franchise` = `c`.`franchise`)
where `a`.`week` = 20 and `a`.`gametype` = 36 and `a`.`homeaway` = 0 order by `a`.`season`";        
#echo "<p>$_cp_sql15</p>";
$result15 = $conn->prepare($_cp_sql15); 
$result15->execute(); 

$_cp_sql = "UPDATE `fp_bowlgames` SET `win`='AFC' WHERE `afc_score`>`nfc_score`";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

$_cp_sql = "UPDATE `fp_bowlgames` SET `win`='NFC' WHERE `afc_score`<`nfc_score`";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

$_cp_sql16 = "SELECT `season`,`AFC_Champions`,`NFC_Champions`,`win` FROM `fp_bowlgames` WHERE 1 ORDER BY `season` ASC";
$res16 = execute_db($_cp_sql16, $conn);
	while($row16 = fetch_row_db($res16)){
		$_cp_myseason=$row16[0];
		$_cp_myafc=$row16[1];
		$_cp_mynfc=$row16[2];
		$_cp_mywin=$row16[3];
		#echo "<p>$_cp_myseason - $_cp_myafc - $_cp_mynfc - $_cp_mywin</p>";
		if ("NFC"==$_cp_mywin){
			$_cp_sql17 = "UPDATE `fp_bowlgames` SET `NFC_Champions`  = CONCAT('<span style=\"font-weight: 900\">', `NFC_Champions`,'</span>') WHERE `season`=$_cp_myseason";
		} else {
			$_cp_sql17 = "UPDATE `fp_bowlgames` SET `AFC_Champions`  = CONCAT('<span style=\"font-weight: 900\">', `AFC_Champions`,'</span>') WHERE `season`=$_cp_myseason";
		}
		#echo "$_cp_sql17<br>";
		$res17 = execute_db($_cp_sql17, $conn);

}        


#$str="</div>";
#output($str);


##$str="<h2>About to process some records</h3>";
##output($str);
//Reset all records, this won't be needed once all done then update will only look for unprocessed seasons
$_cp_sql = "TRUNCATE `fp_seasons`";
$res = execute_db($_cp_sql, $conn);

//Retireve all Pro Leagues
$_cp_sql = "SELECT DISTINCT `league` 
FROM `fp_franchises` 
WHERE `ftype` = 'Pro'
ORDER BY `league`";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
				$_cp_myleague=$row[0];
				//Retrieve Seasons for that League
				$_cp_sql2 = "SELECT DISTINCT `season`
				FROM `f_games` 
				WHERE `league`= '$_cp_myleague'
				ORDER BY  `season`";
				$res2 = execute_db($_cp_sql2, $conn);
				$number_of_rows = number_format($res2->rowCount() ); 
				#$str="<h2>Found $number_of_rows seasons</h2>";
				#output($str);
				$j=0;
				while($row2 = fetch_row_db($res2)){
					$_cp_myseason=$row2[0];
					$j++;

				
				//Looping through Seasons	
				//Find Season Records
				$_cp_sql3 = "SELECT `franchise`,sum(`win`),sum(`lose`),sum(`tie`),sum(`score`), sum(`opp_score`)
							 FROM `f_games` 
							 WHERE league='$_cp_myleague' AND season=$_cp_myseason AND week <>0 AND `week` <>17  AND `week` <> 18  AND `week` <> 19 AND `week` <> 20
							 GROUP BY `franchise`
							 ORDER BY `franchise`";
				#echo "$_cp_sql3";				
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_sql4 = "
						INSERT INTO `fp_seasons` (`id`, `franchise`, `season`, `coach`, `won`, `lost`, `tie`, `scored`, `conceded`) 
						VALUES (NULL, '$row3[0]', '$_cp_myseason', '', '$row3[1]', '$row3[2]', '$row3[3]', '$row3[4]', '$row3[5]');	
						";
						$res4 = execute_db($_cp_sql4, $conn);

					}

				#Season loop ended	
				}  
        }  
		//final total
		$str=' Processed ';
		$str.=$j;
		$str.=' Pro Seasons.';
		#output($str);
		
//Find Season Coaches
$_cp_sql77 = "SELECT b.`league`,a.`franchise`, a.`season` 
				FROM `fp_seasons` a
				INNER JOIN `fp_franchises` b ON a.franchise=b.franchise
				WHERE 1 
				ORDER BY b.`league` ASC, a.`franchise` ASC, a.`season` ASC;";
$res77 = execute_db($_cp_sql77, $conn);
				$number_of_rows77 = number_format($res77->rowCount() ); 
				#$str="<h2>Found $number_of_rows77 Coach seasons</h2>";
				#output($str);
					$j=0;	
					while($row77 = fetch_row_db($res77)){
					$j++;
						$mycoach='';
						#Find Week 16 coach
						$myleague=$row77[0];
						$myfranchise=$row77[1];
						$myseason=$row77[2];
						$_cp_sql78 = "SELECT `coach` FROM `f_games` WHERE `league`='$myleague' AND `season`=$myseason AND `week`=16 AND `franchise`=$myfranchise";
						#echo "<br />$_cp_sql78";
							$res78 = execute_db($_cp_sql78, $conn);
							while($row78 = fetch_row_db($res78)){
								$mycoach=$row78[0];
							}
						$_cp_sql4 = "UPDATE `fp_seasons` SET `coach`='$mycoach' WHERE `franchise`=$myfranchise AND `season`=$myseason";
						$res4 = execute_db($_cp_sql4, $conn);
						#echo "<br />$_cp_sql4";
					}
							//final total
					$str='Processed ';
					$str.=$j;
					$str.=' Coach Seasons.';
					#output($str);
					
//Find missing Coaches
$_cp_sql80 = "SELECT `franchise`, `season` 
				FROM `fp_seasons` 
				WHERE `coach` IS NULL OR `coach` = ''
				ORDER BY `franchise`, `season` ;";
$res80 = execute_db($_cp_sql80, $conn);
						$number_of_rows80 = number_format($res80->rowCount() ); 
						#$str="<h2>Found $number_of_rows80 missing coach seasons</h2>";
						#output($str);
						$j=0;
					while($row80 = fetch_row_db($res80)){

					$j++;
						$mycoach='';
						#Find Week 99 coach
						$myfranchise=$row80[0];
						$myseason=$row80[1];
						$_cp_sql81 = "SELECT `coach` FROM `f_games` WHERE `season`=$myseason AND `week`=99 AND `franchise`=$myfranchise";
						#echo "<br />$_cp_sql81";
							$res81 = execute_db($_cp_sql81, $conn);
							while($row81 = fetch_row_db($res81)){
								$mycoach=$row81[0];
							}
						$_cp_sql82 = "UPDATE `fp_seasons` SET `coach`='$mycoach' WHERE `franchise`=$myfranchise AND `season`=$myseason";
						$res82 = execute_db($_cp_sql82, $conn);
						#echo "<br />$_cp_sql82";
					}
					$str='Processed ';
					$str.=$j;
					$str.=' missing Coach Seasons.';
					#output($str);

$_cp_sql80 = "SELECT `franchise`, `season` 
				FROM `fp_seasons` 
				WHERE `coach` IS NULL OR `coach` = ''
				ORDER BY `franchise`, `season` ;";
$res80 = execute_db($_cp_sql80, $conn);
						$number_of_rows80b = number_format($res80->rowCount() ); 
						#$str="<h2>Found $number_of_rows80b missing coach seasons</h2>";
						echo "<br />$str";
						$j=0;
					while($row80 = fetch_row_db($res80)){
						$j++;


						$mycoach='';
						#Find Week 1 coach
						$myfranchise=$row80[0];
						$myseason=$row80[1];
						$_cp_sql81 = "SELECT `coach` FROM `f_games` WHERE `season`=$myseason AND `week`=1 AND `franchise`=$myfranchise";
						#echo "<br />$_cp_sql81";
							$res81 = execute_db($_cp_sql81, $conn);
							while($row81 = fetch_row_db($res81)){
								$mycoach=$row81[0];
							}
						$_cp_sql82 = "UPDATE `fp_seasons` SET `coach`='$mycoach' WHERE `franchise`=$myfranchise AND `season`=$myseason";
						$res82 = execute_db($_cp_sql82, $conn);
						#echo "<br />$_cp_sql82";
					}	
					$str='Processed ';
					$str.=$j;
					$str.=' missing Coach Seasons.';
					#output($str);				

//Retrieve all Pro Leagues
$_cp_sql = "SELECT DISTINCT `franchise` 
FROM `fp_franchises` 
WHERE `ftype` = 'Pro'
ORDER BY `franchise` ";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$_cp_myfranchise=$row[0];
	#Loop through all teams and extract details
	$_cp_sql2 = "SELECT MAX(`won`), MAX(`lost`), MAX(`scored`), MAX(`conceded`)
					FROM `fp_seasons` 
					WHERE `franchise`=$_cp_myfranchise";
	$res2 = execute_db($_cp_sql2, $conn);
        while($row2 = fetch_row_db($res2)){
			$_cp_sql3 = "UPDATE `fp_franchises` SET `MaxWins`=$row2[0], `MaxLosses`=$row2[1], `MaxScored`=$row2[2], `MaxConceded`=$row2[3]
							WHERE `franchise`=$_cp_myfranchise";
			$res3 = execute_db($_cp_sql3, $conn);				
	}
}


//Retrieve all Pro Coaches
$_cp_sql = "TRUNCATE fp_coaches";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 


$_cp_sql = "SELECT DISTINCT `league`, `coach` 
FROM `f_games` 
WHERE `league` LIKE 'NF%'
ORDER BY `league` ASC, `coach` ASC";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
				$number_of_rowsX = number_format($result->rowCount() ); 
				#$str="<h2>Found $number_of_rowsX coaches</h3>";
				#output($str);
		$i=1;
		$k=0;
        while($row = fetch_row_db($result)){
					$k++;
					if ($k % 10 == 0) {
						$str='Processed ';
						$str.=$k;
						$str.=' Coaches / ';
						#output($str);
					}
				$_cp_myleague=$row[0];
				$_cp_mycoach=$row[1];
				
				#If active coach show current team and asterik
				$_cp_sql2 = "SELECT IFNULL((SELECT `team` FROM `fp_franchises` WHERE `coach`='$_cp_mycoach'), 'Retired') ";
				#echo "<p>$_cp_sql2</p>";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				while($row2 = fetch_row_db($result2)){
						#echo "<p>$row2[0]</p>";
						if ('Retired'==$row2[0]) {
							$_cp_myretirement=1;
							} else {
							$_cp_myretirement=0;
							$_cp_mycurrentteam=$row2[0];
							}

						}		
 								
				#If retired coach show old team(s)
				if (1==$_cp_myretirement){
					#echo "<p>Looking for old teams</p>";
					$_cp_myoldteams="";
					$_cp_sql3 = "SELECT DISTINCT b.`team` 
								 FROM `f_games` a
									INNER JOIN  `fp_franchises` b ON a.`franchise`=b.`franchise`  
								 WHERE a.`league` ='$_cp_myleague' AND a.`coach`='$_cp_mycoach' 
								 ORDER BY b.`team` ASC";
								#echo "<p>$_cp_sql3</p>";
								$result3 = $conn->prepare($_cp_sql3); 
								$result3->execute(); 
								while($row3 = fetch_row_db($result3)){
									$_cp_myoldteams.=$row3[0];	
									$_cp_myoldteams.=", ";
								}						

				} else {
					$_cp_myoldteams="";
				}
				$_cp_myoldteams=substr($_cp_myoldteams, 0, -2);
				
				#Determine wins and losses in Playoffs
				$_cp_sql4 = "	SELECT SUM(a.`win`),SUM(a.`lose`)
								FROM `f_games` a
								WHERE a.`league` ='$_cp_myleague' AND a.`coach`='$_cp_mycoach'  AND (a.`gametype` =35 OR a.`gametype` =36)";
				#echo "<p>$_cp_sql4</p>";
				$result4 = $conn->prepare($_cp_sql4); 
				$result4->execute(); 
				while($row4 = fetch_row_db($result4)){
					$_cp_mypowins=$row4[0];	
					$_cp_mypolosses=$row4[1];		
				}
				
				if(is_null($_cp_mypowins)){
					$_cp_mypowins=0;
				} 					
				if(is_null($_cp_mypolosses)){
					$_cp_mypolosses=0;
				}				
				
				
				#Determine wins and losses in Regular Season
				$_cp_sql4 = "	SELECT SUM(a.`win`),SUM(a.`lose`), SUM(a.`tie`)
								FROM `f_games` a
								WHERE a.`league` ='$_cp_myleague' AND a.`coach`='$_cp_mycoach'  AND a.`gametype` = 24  ";
				#echo "<p>$_cp_sql4</p>";
				$result4 = $conn->prepare($_cp_sql4); 
				$result4->execute(); 
				while($row4 = fetch_row_db($result4)){
					$_cp_myrswins=$row4[0];	
					$_cp_myrslosses=$row4[1];
					$_cp_myrsties=$row4[2];		
				}
				
				if(is_null($_cp_myrswins)){
					$_cp_myrswins=0;
				} 					
				if(is_null($_cp_myrslosses)){
					$_cp_myrslosses=0;
				}					
				if(is_null($_cp_myrsties)){
					$_cp_myrsties=0;
				}													
				
				#Determine Superbowls
				$_cp_sql2 = "SELECT IFNULL((SELECT SUM(`win`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `gametype`=36 AND `coach`='$_cp_mycoach'), 0) AS 'NCWins'";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				while($row2 = fetch_row_db($result2)){
						$_cp_mysb=$row2[0];
					}

				#Determine Superbowl Runners Up
				$_cp_sql2 = "SELECT IFNULL((SELECT SUM(`lose`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `gametype`=36 AND `coach`='$_cp_mycoach'), 0) AS 'NCLosses'";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				while($row2 = fetch_row_db($result2)){
						$_cp_myru=$row2[0];
					}

				#Add up total Regular Sreason games
				$_cp_myrsgames=$_cp_myrswins+$_cp_myrslosses+$_cp_myrsties;
				
				#Write data to table
				if (1==$_cp_myretirement){
					
						$_cp_sql4 = "INSERT INTO `fp_coaches` (`id`, `league`, `coach`, `team`, `retired`, `Winner`, `Runnerup`, `powins`, `polosses`, `rsgames`, `rswins`, `rslosses`, `rsties`, `Link`) 
									VALUES ('$_cp_myleague-$_cp_mycoach','$_cp_myleague', '$_cp_mycoach', '$_cp_myoldteams', 'Y', $_cp_mysb, $_cp_myru,  '$_cp_mypowins', '$_cp_mypolosses', '$_cp_myrsgames', '$_cp_myrswins', '$_cp_myrslosses', $_cp_myrsties, NULL)";
				} else {
					
								$_cp_sql4 = "INSERT INTO `fp_coaches` (`id`,`league`, `coach`, `team`, `retired`, `Winner`, `Runnerup`, `powins`, `polosses`, `rsgames`,  `rswins`, `rslosses`, `rsties`, `Link`) 
								VALUES ('$_cp_myleague-$_cp_mycoach','$_cp_myleague', '$_cp_mycoach', '$_cp_mycurrentteam', '', $_cp_mysb, $_cp_myru,  '$_cp_mypowins', '$_cp_mypolosses',  '$_cp_myrsgames', '$_cp_myrswins', '$_cp_myrslosses', $_cp_myrsties, NULL)";
				}
				$result4 = $conn->prepare($_cp_sql4); 
				$result4->execute(); 

				
				

				$i++;


}
$str='Processed ';
$str.=$k;
$str.=' Coaches.';
#output($str);

//Update Team and Coach Names
$_cp_sql = "SELECT DISTINCT `league` 
FROM `fp_franchises` 
WHERE `ftype` = 'Pro'
ORDER BY `league`";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
				$_cp_myleague=$row[0];
				$_cp_sql2 = "SELECT MAX(`season`)
				FROM `f_games` 
				WHERE `league`= '$_cp_myleague'
				ORDER BY  `season`";
				$res2 = execute_db($_cp_sql2, $conn);
				while($row2 = fetch_row_db($res2)){
					$_cp_myseason=$row2[0];
					$_cp_sql3 = "SELECT MAX(`week`)
						FROM `f_games` 
						WHERE `league`= '$_cp_myleague' and `season`= '$_cp_myseason'
						ORDER BY  `week`";
					$res3 = execute_db($_cp_sql3, $conn);

					while($row3 = fetch_row_db($res3)){
					$_cp_myweek=$row3[0];
						$_cp_sql4 = "SELECT `team`, `franchise`, `coach`  
						FROM `f_games` 
						WHERE `league`= '$_cp_myleague' and `season`= '$_cp_myseason' and `week`= '$_cp_myweek'
						ORDER BY `franchise`
						";
						$res4 = execute_db($_cp_sql4, $conn);
						$number_of_rows4 = number_format($res4->rowCount() ); 
						#$str="<h4>Found $number_of_rows4 teams</h4>";
						#output($str);
						$l=0;
						while($row4 = fetch_row_db($res4)){
							$l++;
							$_cp_myteam=$row4[0];
							$_cp_myfranchise=$row4[1];
							$_cp_mycoach=$row4[2];
							echo "<p>Coach of $_cp_myteam ($_cp_myfranchise) in $_cp_myleague s$_cp_myseason w$_cp_myweek is $_cp_mycoach</p>";
							#Split out team name
							$_cp_myteamsplit=explode(' ',$_cp_myteam,2);
							$_cp_mycity=$_cp_myteamsplit[0];
							$_cp_mynickname=$_cp_myteamsplit[1];
							#echo "<p>City is $_cp_mycity, nickname is $_cp_mynickname</p>";
							if (strpos($_cp_mynickname,' ')) {
								#Nickname has muliple words so need to split
								$_cp_mynicknamesplit=explode(' ',$_cp_mynickname,2);
								$_cp_mycity.=' ';
								$_cp_mycity.=$_cp_mynicknamesplit[0];
								$_cp_mynickname=$_cp_mynicknamesplit[1];
								#echo "<p>City is now $_cp_mycity, nickname is now $_cp_mynickname</p>";
							}
							
							
							$_cp_sql5 = "	UPDATE `fp_franchises` 
											SET `team`='$_cp_myteam', `city`='$_cp_mycity', `nickname`='$_cp_mynickname', `coach`='$_cp_mycoach'
											WHERE `league`= '$_cp_myleague' and `franchise`=$_cp_myfranchise ";
							$res5 = execute_db($_cp_sql5, $conn);	
						}
					}
					}
				}
$str='Processed ';
$str.=$l;
$str.=' Teams.';
$str.="<br>";
#output($str);

#$str="<h2>About to truncate existing tables</h3>";
#output($str);
//Reset all records, this won't be needed once all done then update will only look for unprocessed seasons
$_cp_sql = "TRUNCATE `fc_seasons`";
$res = execute_db($_cp_sql, $conn);
$_cp_sql = "TRUNCATE `fc_cicgames`";
$res = execute_db($_cp_sql, $conn);
$_cp_sql = "TRUNCATE `fc_confgames`";
$res = execute_db($_cp_sql, $conn);
$_cp_sql = "TRUNCATE `fc_rivalrygames`";
$res = execute_db($_cp_sql, $conn);
$_cp_sql = "UPDATE `fc_franchises` 
SET PerfectYears='',Perfect=0, ConferenceYears='',ConfWins=0,CicYears='',CicWins=0,RivalryYears='',RivalryWins=0
WHERE 1";
$res = execute_db($_cp_sql, $conn);
$number_of_rows = number_format($res->rowCount() ); 
#$str="<h2>Calculating College Seasons</h3>";
#output($str);
$_cp_sql = "SELECT DISTINCT `league` 
FROM `fc_franchises` 
WHERE `ftype` = 'College'
ORDER BY `league`";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
				$_cp_myleague=$row[0];
				//Retrieve Seasons for that League
				$_cp_sql2 = "SELECT DISTINCT `season`
				FROM `f_games` 
				WHERE `league`= '$_cp_myleague'
				ORDER BY  `season` DESC";
				$res2 = execute_db($_cp_sql2, $conn);
				while($row2 = fetch_row_db($res2)){
					$_cp_myseason=$row2[0];
				
				//Looping through Seasons	
				//Find Season Records
				$_cp_sql3 = "SELECT `franchise`,sum(`win`),sum(`lose`),sum(`tie`),sum(`score`), sum(`opp_score`)
								FROM `f_games` 
								WHERE league='$_cp_myleague' AND season=$_cp_myseason AND `gametype`=1 
								GROUP BY `franchise`
								ORDER BY `franchise`";
				$res3 = execute_db($_cp_sql3, $conn);
				#echo "<p>$_cp_sql3</p>";
					while($row3 = fetch_row_db($res3)){
						$_cp_sql4 = "
						INSERT INTO `fc_seasons` (`id`, `franchise`, `season`, `coach`, `won`, `lost`, `tie`, `scored`, `conceded`) 
						VALUES (NULL, '$row3[0]', '$_cp_myseason', '', '$row3[1]', '$row3[2]', '$row3[3]', '$row3[4]', '$row3[5]');	
						";
						$res4 = execute_db($_cp_sql4, $conn);
					}
				#Season loop ended	
				}  
        }  

//Find Season Coaches
$_cp_sql77 = "SELECT b.`league`,a.`franchise`, a.`season` 
				FROM `fc_seasons` a
				INNER JOIN `fc_franchises` b ON a.franchise=b.franchise
				WHERE 1 
				ORDER BY b.`league` ASC, a.`franchise` ASC, a.`season` ASC;";
$res77 = execute_db($_cp_sql77, $conn);
				$number_of_rows77 = number_format($res77->rowCount() ); 
				#$str="<h2>Found $number_of_rows77 Coach seasons</h2>";
				#output($str);
					$j=0;	
					while($row77 = fetch_row_db($res77)){

					$j++;
						$mycoach='';
						#Find Week 16 coach
						$myleague=$row77[0];
						$myfranchise=$row77[1];
						$myseason=$row77[2];
						$_cp_sql78 = "SELECT `coach` FROM `f_games` WHERE `league`='$myleague' AND `season`=$myseason AND `week`=11 AND `franchise`=$myfranchise";
						#echo "<br />$_cp_sql78";
							$res78 = execute_db($_cp_sql78, $conn);
							while($row78 = fetch_row_db($res78)){
								$mycoach=$row78[0];
							}
						$_cp_sql4 = "UPDATE `fc_seasons` SET `coach`='$mycoach' WHERE `franchise`=$myfranchise AND `season`=$myseason";
						$res4 = execute_db($_cp_sql4, $conn);
						#echo "<br />$_cp_sql4";
					}
							//final total
					$str='Processed ';
					$str.=$j;
					$str.=' Coach Seasons.';
					#output($str);
					
//Find missing Coaches
$_cp_sql80 = "SELECT `franchise`, `season` 
				FROM `fc_seasons` 
				WHERE `coach` IS NULL OR `coach` = ''
				ORDER BY `franchise`, `season` ;";
$res80 = execute_db($_cp_sql80, $conn);
						$number_of_rows80 = number_format($res80->rowCount() ); 
						#$str="<h2>Found $number_of_rows80 missing coach seasons</h2>";
						#output($str);
						$j=0;
					while($row80 = fetch_row_db($res80)){
					$j++;
						$mycoach='';
						#Find Week 99 coach
						$myfranchise=$row80[0];
						$myseason=$row80[1];
						$_cp_sql81 = "SELECT `coach` FROM `f_games` WHERE `season`=$myseason AND `week`=99 AND `franchise`=$myfranchise";
						#echo "<br />$_cp_sql81";
							$res81 = execute_db($_cp_sql81, $conn);
							while($row81 = fetch_row_db($res81)){
								$mycoach=$row81[0];
							}
						$_cp_sql82 = "UPDATE `fc_seasons` SET `coach`='$mycoach' WHERE `franchise`=$myfranchise AND `season`=$myseason";
						$res82 = execute_db($_cp_sql82, $conn);
						#echo "<br />$_cp_sql82";
					}
					$str='Processed ';
					$str.=$j;
					$str.=' missing Coach Seasons.';
					#output($str);

//Find missing Coaches
$_cp_sql80 = "SELECT `franchise`, `season` 
				FROM `fc_seasons` 
				WHERE `coach` IS NULL OR `coach` = ''
				ORDER BY `franchise`, `season` ;";
$res80 = execute_db($_cp_sql80, $conn);
						$number_of_rows80 = number_format($res80->rowCount() ); 
						#$str="<h2>Found $number_of_rows80 missing coach seasons</h2>";
						#output($str);
						$j=0;
					while($row80 = fetch_row_db($res80)){
					$j++;
						$mycoach='';
						#Find Week 95 coach
						$myfranchise=$row80[0];
						$myseason=$row80[1];
						$_cp_sql81 = "SELECT `coach` FROM `f_games` WHERE `season`=$myseason AND `week`=95 AND `franchise`=$myfranchise";
						#echo "<br />$_cp_sql81";
							$res81 = execute_db($_cp_sql81, $conn);
							while($row81 = fetch_row_db($res81)){
								$mycoach=$row81[0];
							}
						$_cp_sql82 = "UPDATE `fc_seasons` SET `coach`='$mycoach' WHERE `franchise`=$myfranchise AND `season`=$myseason";
						$res82 = execute_db($_cp_sql82, $conn);
						#echo "<br />$_cp_sql82";
					}
					$str='Processed ';
					$str.=$j;
					$str.=' missing Coach Seasons.';
					#output($str);



$_cp_sql80 = "SELECT `franchise`, `season` 
				FROM `fc_seasons` 
				WHERE `coach` IS NULL OR `coach` = ''
				ORDER BY `franchise`, `season` ;";
$res80 = execute_db($_cp_sql80, $conn);
						$number_of_rows80b = number_format($res80->rowCount() ); 
						#$str="<h2>Found $number_of_rows80b missing coach seasons</h2>";
						echo "<br />$str";
						$j=0;
					while($row80 = fetch_row_db($res80)){
						$j++;
						if ($j % 5 == 0) {
							$str=' / Processed ';
							$str.=$j;
							$str.=' missing Coach Seasons ';
							#output($str);
						}

						$mycoach='';
						#Find Week 1 coach
						$myfranchise=$row80[0];
						$myseason=$row80[1];
						$_cp_sql81 = "SELECT `coach` FROM `f_games` WHERE `season`=$myseason AND `week`=1 AND `franchise`=$myfranchise";
						#echo "<br />$_cp_sql81";
							$res81 = execute_db($_cp_sql81, $conn);
							while($row81 = fetch_row_db($res81)){
								$mycoach=$row81[0];
							}
						$_cp_sql82 = "UPDATE `fc_seasons` SET `coach`='$mycoach' WHERE `franchise`=$myfranchise AND `season`=$myseason";
						$res82 = execute_db($_cp_sql82, $conn);
						#echo "<br />$_cp_sql82";
					}	
					$str='Processed ';
					$str.=$j;
					$str.=' missing Coach Seasons.';
					#output($str);				

//End of Coaches for Seasons

$_cp_sql10 = "UPDATE `fc_seasons` SET `complete`=1 WHERE `season` IN (SELECT `season`FROM `f_games` WHERE `week`=13)";
$res10 = execute_db($_cp_sql10, $conn);

#$str="<h2>Calculating Team records</>";
#output($str);
$_cp_sql = "SELECT DISTINCT `franchise` 
FROM `fc_franchises` 
WHERE `ftype` = 'College'
ORDER BY `franchise` ASC";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$_cp_myfranchise=$row[0];
			#Loop through all teams and extract details
			$_cp_sql2 = "SELECT MAX(`won`), MAX(`lost`), MAX(`scored`), MAX(`conceded`)
					FROM `fc_seasons` 
					WHERE `franchise`=$_cp_myfranchise";
			$res2 = execute_db($_cp_sql2, $conn);
				while($row2 = fetch_row_db($res2)){
				$_cp_sql3 = "UPDATE `fc_franchises` SET `MaxWins`=$row2[0], `MaxLosses`=$row2[1], `MaxScored`=$row2[2], `MaxConceded`=$row2[3]
							WHERE `franchise`=$_cp_myfranchise";
				$res3 = execute_db($_cp_sql3, $conn);				
				}
}

#$str="<h2>Finding perfect Seasons</h3>";
#output($str);
$_cp_sql = "SELECT DISTINCT `franchise`,`season` 
FROM `fc_seasons` 
WHERE `won`=11 AND `complete`=1
ORDER BY `season` DESC, `franchise` ";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			#echo "<p>$row[0] - $row[1]</p>";
			$_cp_myfranchise=$row[0];
			$_cp_myseason=$row[1];


			//Now we have 11-0 teams check if they won NC
			$_cp_sql3 = "SELECT 'Yes' FROM `fc_franchises` WHERE `franchise` = $_cp_myfranchise AND  `WinnerYears` LIKE '%$_cp_myseason%'";
			#echo "<p>$_cp_sql3</p>";
			$res3 = execute_db($_cp_sql3, $conn);
			while($row3 = fetch_row_db($res3)){
						$_cp_myperfecto=$row3[0];
			}
				
				if ('Yes'==$_cp_myperfecto){
						#echo "<p>$_cp_myfranchise - $_cp_myseason - $_cp_myperfecto</p>";
						$_cp_sql4 = "UPDATE `fc_franchises` SET `PerfectYears`=CONCAT (`PerfectYears`,' <em>$_cp_myseason</em>') WHERE `franchise`=$_cp_myfranchise";
					} 
				$res4 = execute_db($_cp_sql4, $conn);
				$_cp_sql5 = "UPDATE `fc_franchises` SET `Perfect`=`Perfect` +1 WHERE `franchise`=$_cp_myfranchise";
				$res5 = execute_db($_cp_sql5, $conn);
		}	


#$str="<h2>Calculating Conference records</h3>";
#output($str);
$_cp_sql = "INSERT INTO `fc_confgames`
			SELECT CONCAT (a.`league`,'-',a.`season`,'-',a.`franchise`) AS `id`,a.`league`,a.`season`,a.`franchise`, b.`conference` as `myconf`,
			sum(a.`win`) AS `wins`, SUM(a.`lose`) AS `losses`, SUM(a.`tie`) as `ties` , SUM(a.`score`) as `pfor`, SUM(a.`opp_score`) as `pagn`,
			sum(a.`win`)*10 +  SUM(a.`tie`)*5, SUM(a.`score`)-SUM(a.`opp_score`)
			FROM `f_games` a
				INNER JOIN `fc_franchises` b ON a.`franchise` = b.`franchise`
				INNER JOIN `fc_franchises` c ON a.`opp_franchise` = c.`franchise`    
			WHERE `week` > 0 AND `week` <12  AND  b.`conference` =  c.`conference`
			GROUP BY a.`league`,a.`season`,a.`franchise`
			ORDER BY a.`league`,a.`season`,a.`franchise`";
$res = execute_db($_cp_sql, $conn);

//Now work out the winners
$_cp_sql = "SELECT DISTINCT `league`,`season`,`myconf` 
FROM `fc_confgames` 
WHERE 1
ORDER BY `league`,`season` DESC,`myconf`  ";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$_cp_myleague=$row[0];
			$_cp_myseason=$row[1];
			$_cp_myconf=$row[2];
				//Check regular season is over before assigning Conference title
				$_cp_sql4 = "SELECT MAX(`week`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res4 = execute_db($_cp_sql4, $conn);			
				while($row4 = fetch_row_db($res4)){
				if ($row4[0]>10) {
					#echo "<p>$_cp_myleague $_cp_myseason $_cp_myconf</p>";
					$_cp_sql2 = "SELECT `franchise` 
							FROM `fc_confgames`  
							WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason AND myconf='$_cp_myconf'
							ORDER BY `pts` DESC, `diff` DESC, `wins` DESC, `franchise` DESC
							LIMIT 1";
					$res2 = execute_db($_cp_sql2, $conn);			
					while($row2 = fetch_row_db($res2)){
						$_cp_sql3 = "UPDATE `fc_franchises` 
								SET `ConfWins`=`ConfWins`+1, `ConferenceYears`=CONCAT(`ConferenceYears`,'$_cp_myseason ')
								WHERE `franchise`=$row2[0]";
						$res3 = execute_db($_cp_sql3, $conn);			
						}		
				}
			}			 
}

#$str="<h2>Updating Team and Coach names</h3>";
#output($str);
$_cp_sql = "SELECT DISTINCT `league` 
FROM `fc_franchises` 
WHERE `ftype` = 'College'
ORDER BY `league`";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
				$_cp_myleague=$row[0];
				$_cp_sql2 = "SELECT MAX(`season`)
				FROM `f_games` 
				WHERE `league`= '$_cp_myleague'
				ORDER BY  `season`";
				$res2 = execute_db($_cp_sql2, $conn);
				while($row2 = fetch_row_db($res2)){
					$_cp_myseason=$row2[0];
					$_cp_sql3 = "SELECT MAX(`week`)
						FROM `f_games` 
						WHERE `league`= '$_cp_myleague' and `season`= '$_cp_myseason'
						ORDER BY  `week`";
					$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
					$_cp_myweek=$row3[0];
						$_cp_sql4 = "SELECT `team`, `franchise`, `coach`  
						FROM `f_games` 
						WHERE `league`= '$_cp_myleague' and `season`= '$_cp_myseason' and `week`= '$_cp_myweek'
						ORDER BY `franchise`
						";
						$res4 = execute_db($_cp_sql4, $conn);
						while($row4 = fetch_row_db($res4)){
							$_cp_myteam=$row4[0];
							$_cp_myfranchise=$row4[1];
							$_cp_mycoach=$row4[2];
							#echo "<p>Coach of $_cp_myteam ($_cp_myfranchise) in $_cp_myleague s$_cp_myseason w$_cp_myweek is $_cp_mycoach</p>";
							#Split out team name
							$_cp_myteamsplit=explode(' ',$_cp_myteam,2);
							$_cp_mycity=$_cp_myteamsplit[0];
							$_cp_mynickname=$_cp_myteamsplit[1];
							#echo "<p>City is $_cp_mycity, nickname is $_cp_mynickname</p>";
							if (strpos($_cp_mynickname,' ')) {
								#Nickname has muliple words so need to split
								$_cp_mynicknamesplit=explode(' ',$_cp_mynickname,2);
								$_cp_mycity.=' ';
								$_cp_mycity.=$_cp_mynicknamesplit[0];
								$_cp_mynickname=$_cp_mynicknamesplit[1];
								#echo "<p>City is now $_cp_mycity, nickname is now $_cp_mynickname</p>";
							}
							
							
							$_cp_sql5 = "	UPDATE `fc_franchises` 
											SET `team`='$_cp_myteam', `city`='$_cp_mycity', `nickname`='$_cp_mynickname', `coach`='$_cp_mycoach'
											WHERE `league`= '$_cp_myleague' and `franchise`=$_cp_myfranchise ";
							$res5 = execute_db($_cp_sql5, $conn);	
						}
					}
					}
				}

#$str="<h2>Calculating Commander in Chief records</h3>";
#output($str);
$_cp_sql = "INSERT INTO `fc_cicgames`
			SELECT CONCAT (a.`league`,'-',a.`season`,'-',a.`franchise`) AS `id`,a.`league`,a.`season`,a.`franchise`, 
			sum(a.`win`) AS `wins`, SUM(a.`lose`) AS `losses`, SUM(a.`tie`) as `ties` , SUM(a.`score`) as `pfor`, SUM(a.`opp_score`) as `pagn`,
			(sum(a.`win`)*10 +  SUM(a.`tie`)*5) AS `pts`, SUM(a.`score`)-SUM(a.`opp_score`) AS `diff`
			FROM `f_games` a
				INNER JOIN `fc_franchises` b ON a.`franchise` = b.`franchise`
				INNER JOIN `fc_franchises` c ON a.`opp_franchise` = c.`franchise`    
			WHERE `week` > 0 AND `week` <12  AND  b.`academy` = 1 AND  c.`academy` = 1 
			GROUP BY a.`league`,a.`season`,a.`franchise`
			ORDER BY a.`league`,a.`season`,a.`franchise`";
$res = execute_db($_cp_sql, $conn);


//Now work out the winners
$_cp_sql = "SELECT DISTINCT `league`,`season`
FROM `fc_cicgames` 
WHERE 1
ORDER BY `league`,`season` DESC ";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$_cp_myleague=$row[0];
			$_cp_myseason=$row[1];
				//Check regular season is over before assigning Conference title
				$_cp_sql4 = "SELECT MAX(`week`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res4 = execute_db($_cp_sql4, $conn);			
				while($row4 = fetch_row_db($res4)){
				if ($row4[0]>10) {
					#echo "<p>$_cp_myleague $_cp_myseason $_cp_myconf</p>";
					$_cp_sql2 = "SELECT `franchise` 
							FROM `fc_cicgames`  
							WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason 
							ORDER BY `pts` DESC, `diff` DESC, `wins` DESC, `franchise` DESC
							LIMIT 1";
					$res2 = execute_db($_cp_sql2, $conn);			
					while($row2 = fetch_row_db($res2)){
						$_cp_sql3 = "UPDATE `fc_franchises` 
								SET `CicWins`=`CicWins`+1, `CicYears`=CONCAT(`CicYears`,'$_cp_myseason ')
								WHERE `franchise`=$row2[0]";
						$res3 = execute_db($_cp_sql3, $conn);			
						}		
				}
			}			 
}

#$str="<h2>Calculating Rivalry records</h3>";
#output($str);
$_cp_sql = "INSERT INTO `fc_rivalrygames`
			SELECT CONCAT (a.`league`,'-',a.`season`,'-',a.`franchise`) AS `id`,a.`league`,a.`season`,a.`franchise`, 
			sum(a.`win`) AS `wins`, SUM(a.`lose`) AS `losses`, SUM(a.`tie`) as `ties` , SUM(a.`score`) as `pfor`, SUM(a.`opp_score`) as `pagn`,
			(sum(a.`win`)*10 +  SUM(a.`tie`)*5) AS `pts`, SUM(a.`score`)-SUM(a.`opp_score`) AS `diff`,b.`Rivalry`, c.`franchise`
			FROM `f_games` a
				INNER JOIN `fc_franchises` b ON a.`franchise` = b.`franchise`
				INNER JOIN `fc_franchises` c ON a.`opp_franchise` = c.`franchise`    
			WHERE `week` > 0 AND `week` <12  AND  (b.`rivalry` = c.`rivalry` ) 
			GROUP BY a.`league`,a.`season`,a.`franchise`,c.`franchise`";
$res = execute_db($_cp_sql, $conn);

//Now work out the winners
$_cp_sql = "SELECT DISTINCT `league`,`season`, `Rivalry`
FROM `fc_rivalrygames` 
WHERE 1
ORDER BY `league`,`season` DESC ";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$_cp_myleague=$row[0];
			$_cp_myseason=$row[1];
			$_cp_myrivalry=$row[2];
				//Check regular season is over before assigning Conference title
				$_cp_sql4 = "SELECT MAX(`week`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res4 = execute_db($_cp_sql4, $conn);			
				while($row4 = fetch_row_db($res4)){
				if ($row4[0]>10) {
					#echo "<p>$_cp_myleague $_cp_myseason $_cp_myconf</p>";
					$_cp_sql2 = "SELECT `franchise` 
							FROM `fc_rivalrygames`  
							WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason AND `Rivalry` = $_cp_myrivalry
							ORDER BY `pts` DESC, `diff` DESC, `wins` DESC, `franchise` DESC
							LIMIT 1";
					#echo "<p>$_cp_sql2</p>";		
					$res2 = execute_db($_cp_sql2, $conn);			
					while($row2 = fetch_row_db($res2)){
						$_cp_sql3 = "UPDATE `fc_franchises` 
								SET `RivalryWins`=`RivalryWins`+1, `RivalryYears`=CONCAT(`RivalryYears`,'$_cp_myseason ')
								WHERE `franchise`=$row2[0]";
						$res3 = execute_db($_cp_sql3, $conn);			
						}		
				}
			}			 
}

//Bowl games sorted so proceed
#$str="<h2>Resetting records.</h3>\n";
#output($str);
//Reset all records
$_cp_sql = "UPDATE `fc_franchises` 
SET `WinnerYears`='', `RunnerupYears`='',`ConferenceYears`='',`RoseYears`='',`CottonYears`='',`OrangeYears`='',`HawaiiYears`='',`MotorYears`='',`ConfWins`=0,`G_Winner`=0, `G_Runnerup`=0, `GC_Winner`=0, `GC_Runnerup`=0, `S_Winner`=0, `S_Runnerup`=0, `SC_Winner`=0, `SC_Runnerup`=0, `B_Winner`=0, `B_Runnerup`=0, `BC_Winner`=0, `BC_Runnerup`=0 WHERE 1";
$res = execute_db($_cp_sql, $conn);

//Reset College Games
$_cp_sql = "TRUNCATE fc_vgames";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

// Disable strict mode for the session
$conn->exec("SET SESSION sql_mode = '';");
//Fix invalid datetimes
$_cp_sql = "UPDATE `f_games`
			SET `creation_time` = NOW() 
			WHERE `creation_time` = '0000-00-00 00:00:00';";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$_cp_sql = "UPDATE `f_games`
			SET `modification_time` = NOW() 
			WHERE `creation_time` = '0000-00-00 00:00:00';";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
// Re-enable strict mode after the update
$conn->exec("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE';");



$_cp_sql = "INSERT INTO fc_vgames
			SELECT `a`.`id_game` AS `id_game`, `a`.`league` AS `league`, `a`.`season` AS `season`, `a`.`week` AS `week`, `a`.`team` AS `team`, `a`.`franchise` AS `franchise`, `a`.`coach` AS `coach`, `a`.`qb` AS `qb`, `a`.`safe` AS `safe`, `a`.`q1` AS `q1`, `a`.`q2` AS `q2`, `a`.`q3` AS `q3`, `a`.`q4` AS `q4`, `a`.`ot` AS `ot`, `a`.`score` AS `score`, `a`.`fga` AS `fga`, `a`.`fgg` AS `fgg`, `a`.`epa` AS `epa`, `a`.`epg` AS `epg`, `a`.`cva` AS `cva`, `a`.`cvg` AS `cvg`, `a`.`punts` AS `punts`, `a`.`thirdcon` AS `thirdcon`, `a`.`thirddowns` AS `thirddowns`, `a`.`fourthcon` AS `fourthcon`, `a`.`fourthdowns` AS `fourthdowns`, `a`.`firstd` AS `firstd`, `a`.`passcmp` AS `passcmp`, `a`.`passatt` AS `passatt`, `a`.`passyds` AS `passyds`, `a`.`passlng` AS `passlng`, `a`.`passlngtd` AS `passlngtd`, `a`.`passtd` AS `passtd`, `a`.`passpct` AS `passpct`, `a`.`interception` AS `interception`, `a`.`hrd` AS `hrd`, `a`.`skd` AS `skd`, `a`.`rush` AS `rush`, `a`.`rushyds` AS `rushyds`, `a`.`rushlng` AS `rushlng`, `a`.`rushlngtd` AS `rushlngtd`, `a`.`rushtd` AS `rushtd`, `a`.`fum` AS `fum`, `a`.`qbatt` AS `qbatt`, `a`.`qbyds` AS `qbyds`, `a`.`kr` AS `kr`, `a`.`kryds` AS `kryds`, `a`.`krtd` AS `krtd`, `a`.`pr` AS `pr`, `a`.`pryds` AS `pryds`, `a`.`prtd` AS `prtd`, `a`.`form1` AS `form1`, `a`.`form2` AS `form2`, `a`.`run1` AS `run1`, `a`.`run2` AS `run2`, `a`.`pass1` AS `pass1`, `a`.`pass2` AS `pass2`, `a`.`def1` AS `def1`, `a`.`def2` AS `def2`, `a`.`homeaway` AS `homeaway`, `a`.`gametype` AS `gametype`, `a`.`opp_team` AS `opp_team`, `a`.`opp_franchise` AS `opp_franchise`, `a`.`opp_coach` AS `opp_coach`, `a`.`opp_qb` AS `opp_qb`, `a`.`opp_safe` AS `opp_safe`, `a`.`opp_q1` AS `opp_q1`, `a`.`opp_q2` AS `opp_q2`, `a`.`opp_q3` AS `opp_q3`, `a`.`opp_q4` AS `opp_q4`, `a`.`opp_ot` AS `opp_ot`, `a`.`opp_score` AS `opp_score`, `a`.`opp_fga` AS `opp_fga`, `a`.`opp_fgg` AS `opp_fgg`, `a`.`opp_epa` AS `opp_epa`, `a`.`opp_epg` AS `opp_epg`, `a`.`opp_cva` AS `opp_cva`, `a`.`opp_cvg` AS `opp_cvg`, `a`.`opp_punts` AS `opp_punts`, `a`.`opp_thirdcon` AS `opp_thirdcon`, `a`.`opp_thirddowns` AS `opp_thirddowns`, `a`.`opp_fourthcon` AS `opp_fourthcon`, `a`.`opp_fourthdowns` AS `opp_fourthdowns`, `a`.`opp_firstd` AS `opp_firstd`, `a`.`opp_passcmp` AS `opp_passcmp`, `a`.`opp_passatt` AS `opp_passatt`, `a`.`opp_passyds` AS `opp_passyds`, `a`.`opp_passlng` AS `opp_passlng`, `a`.`opp_passlngtd` AS `opp_passlngtd`, `a`.`opp_passtd` AS `opp_passtd`, `a`.`opp_passpct` AS `opp_passpct`, `a`.`opp_interception` AS `opp_interception`, `a`.`opp_hrd` AS `opp_hrd`, `a`.`opp_skd` AS `opp_skd`, `a`.`opp_rush` AS `opp_rush`, `a`.`opp_rushyds` AS `opp_rushyds`, `a`.`opp_rushlng` AS `opp_rushlng`, `a`.`opp_rushlngtd` AS `opp_rushlngtd`, `a`.`opp_rushtd` AS `opp_rushtd`, `a`.`opp_fum` AS `opp_fum`, `a`.`opp_qbatt` AS `opp_qbatt`, `a`.`opp_qbyds` AS `opp_qbyds`, `a`.`opp_kr` AS `opp_kr`, `a`.`opp_kryds` AS `opp_kryds`, `a`.`opp_krtd` AS `opp_krtd`, `a`.`opp_pr` AS `opp_pr`, `a`.`opp_pryds` AS `opp_pryds`, `a`.`opp_prtd` AS `opp_prtd`, `a`.`opp_form1` AS `opp_form1`, `a`.`opp_form2` AS `opp_form2`, `a`.`opp_run1` AS `opp_run1`, `a`.`opp_run2` AS `opp_run2`, `a`.`opp_pass1` AS `opp_pass1`, `a`.`opp_pass2` AS `opp_pass2`, `a`.`opp_def1` AS `opp_def1`, `a`.`opp_def2` AS `opp_def2`, `a`.`win` AS `win`, `a`.`lose` AS `lose`, `a`.`tie` AS `tie`, `a`.`creation_time` AS `creation_time`, `a`.`modification_time` AS `modification_time`, `a`.`modification_by` AS `modification_by`, `a`.`modification_from` AS `modification_from` 
			FROM `f_games` AS `a` 
			WHERE `a`.`league` like '%NCAA%' AND `a`.`week` < 14 AND `a`.`score` <> 1 AND `a`.`opp_score` <> 1 AND `homeaway`=1
			ORDER BY `a`.`league` ASC, `a`.`season` DESC, `a`.`week` ASC, `a`.`franchise` ASC ;";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

#$str="<h2>Processing Bowl wins and losses.</h3>\n";	
#output($str);
//Retireve all College Leagues
$_cp_sql = "SELECT DISTINCT `league` 
FROM `fc_franchises` 
ORDER BY `league`";
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
				$_cp_myleague=$row[0];
				$_cp_sql2 = "SELECT DISTINCT `season`
				FROM `f_games` 
				WHERE `league`= '$_cp_myleague'
				ORDER BY  `season` DESC";
				$res2 = execute_db($_cp_sql2, $conn);
				while($row2 = fetch_row_db($res2)){
					$_cp_myseason=$row2[0];
				
				//Looping through Seasons	
				//Find Gold Bowl wins
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=1 AND `gametype`=8 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbwinner=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET G_Winner=G_Winner+1  WHERE `franchise`=$_cp_mysbwinner";
						$res4 = execute_db($_cp_sql4, $conn);
						#echo "<p>Gold Bowl winner for $_cp_myleague - $_cp_myseason is $row3[1]</p>";
						$_cp_sql5 = "UPDATE `fc_franchises`  SET WinnerYears = CONCAT(WinnerYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mysbwinner";
						$res5 = execute_db($_cp_sql5, $conn);	
					}

				//Find Gold Bowl losses
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=0 AND `gametype`=8 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbloser=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET G_Runnerup=G_Runnerup+1  WHERE `franchise`=$_cp_mysbloser";
						$res4 = execute_db($_cp_sql4, $conn);
						$_cp_sql5 = "UPDATE `fc_franchises`  SET RunnerupYears = CONCAT(RunnerupYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mysbloser";
						$res5 = execute_db($_cp_sql5, $conn);	
					}

				//Find Rose Bowl wins
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=1 AND `gametype`=9 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbwinner=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET GC_Winner=GC_Winner+1  WHERE `franchise`=$_cp_mysbwinner";
						$res4 = execute_db($_cp_sql4, $conn);
						#echo "<p>Gold Bowl winner for $_cp_myleague - $_cp_myseason is $row3[1]</p>";
						$_cp_sql5 = "UPDATE `fc_franchises`  SET RoseYears = CONCAT(RoseYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mysbwinner";
						$res5 = execute_db($_cp_sql5, $conn);	
					}

				//Find Rose Bowl losses
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=0 AND `gametype`=9 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbloser=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET GC_Runnerup=GC_Runnerup+1  WHERE `franchise`=$_cp_mysbloser";
						$res4 = execute_db($_cp_sql4, $conn);
					}

				//Find Cotton Bowl wins
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=1 AND `gametype`=10 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbwinner=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET S_Winner=S_Winner+1  WHERE `franchise`=$_cp_mysbwinner";
						$res4 = execute_db($_cp_sql4, $conn);
						#echo "<p>Gold Bowl winner for $_cp_myleague - $_cp_myseason is $row3[1]</p>";
						$_cp_sql5 = "UPDATE `fc_franchises`  SET CottonYears = CONCAT(CottonYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mysbwinner";
						$res5 = execute_db($_cp_sql5, $conn);	
					}

				//Find Rose Bowl losses
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=0 AND `gametype`=10 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbloser=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET S_Runnerup=S_Runnerup+1  WHERE `franchise`=$_cp_mysbloser";
						$res4 = execute_db($_cp_sql4, $conn);
					}
					
				//Find Orange Bowl wins
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=1 AND `gametype`=11 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbwinner=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET SC_Winner=SC_Winner+1  WHERE `franchise`=$_cp_mysbwinner";
						$res4 = execute_db($_cp_sql4, $conn);
						#echo "<p>Gold Bowl winner for $_cp_myleague - $_cp_myseason is $row3[1]</p>";
						$_cp_sql5 = "UPDATE `fc_franchises`  SET OrangeYears = CONCAT(OrangeYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mysbwinner";
						$res5 = execute_db($_cp_sql5, $conn);	
					}

				//Find Orange Bowl losses
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=0 AND `gametype`=11 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbloser=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET SC_Runnerup=SC_Runnerup+1  WHERE `franchise`=$_cp_mysbloser";
						$res4 = execute_db($_cp_sql4, $conn);
					}

				//Find Hawaii Bowl wins
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=1 AND `gametype`=12 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbwinner=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET B_Winner=B_Winner+1  WHERE `franchise`=$_cp_mysbwinner";
						$res4 = execute_db($_cp_sql4, $conn);
						#echo "<p>Gold Bowl winner for $_cp_myleague - $_cp_myseason is $row3[1]</p>";
						$_cp_sql5 = "UPDATE `fc_franchises`  SET HawaiiYears = CONCAT(HawaiiYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mysbwinner";
						$res5 = execute_db($_cp_sql5, $conn);	
					}

				//Find Hawaii Bowl losses
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=0 AND `gametype`=12 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbloser=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET B_Runnerup=B_Runnerup+1  WHERE `franchise`=$_cp_mysbloser";
						$res4 = execute_db($_cp_sql4, $conn);
					}

				//Find MC Bowl wins
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=1 AND `gametype`=13 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbwinner=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET BC_Winner=BC_Winner+1  WHERE `franchise`=$_cp_mysbwinner";
						$res4 = execute_db($_cp_sql4, $conn);
						#echo "<p>Gold Bowl winner for $_cp_myleague - $_cp_myseason is $row3[1]</p>";
						$_cp_sql5 = "UPDATE `fc_franchises`  SET MotorYears = CONCAT(MotorYears, '$_cp_myseason ') WHERE `franchise`=$_cp_mysbwinner";
						$res5 = execute_db($_cp_sql5, $conn);	
					}

				//Find MC Bowl losses
				$_cp_sql3 = "SELECT `franchise`,`team` FROM `f_games` WHERE `win`=0 AND `gametype`=13 AND `league`='$_cp_myleague' AND `season`=$_cp_myseason";
				$res3 = execute_db($_cp_sql3, $conn);
					while($row3 = fetch_row_db($res3)){
						$_cp_mysbloser=$row3[0];
						$_cp_sql4 = "UPDATE `fc_franchises` SET BC_Runnerup=BC_Runnerup+1  WHERE `franchise`=$_cp_mysbloser";
						$res4 = execute_db($_cp_sql4, $conn);
					}					
		
				
				#Season loop ended	
				}  

        }  

		                
//Populate NC Table
$_cp_sql = "TRUNCATE fc_ncgames";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

$_cp_sql = "INSERT INTO `fc_ncgames` 
SELECT CONCAT(a.`league`, '-',a.`season`) as `id`, a.`league`, a.`season`, a.`week`, CONCAT('<span style=\"font-weight: 900\">', b.`team`,'</span>') AS `Winners`, b.franchise ,b.`Conference` AS `WinConf`, a.`coach` ,a.`score`, a.`opp_score`,c.`team` AS `RunnersUp`, c.franchise, c.`Conference` AS `RUConf`,a.`opp_coach`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise`=b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise`=c.`franchise`
WHERE `gametype`=8 AND `win`=1 ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
//End of NC Table


//Populate Table
$_cp_sql = "TRUNCATE fc_rosegames";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

$_cp_sql = "INSERT INTO `fc_rosegames` 
SELECT CONCAT(a.`league`, '-',a.`season`) as `id`, a.`league`, a.`season`, a.`week`, CONCAT('<span style=\"font-weight: 900\">', b.`team`,'</span>') AS `Winners`,  a.`coach`  AS `Coach`,b.franchise ,b.`Conference` AS `Conference`,a.`score` AS `Score`, a.`opp_score` AS `oScore`,c.`team` AS `RunnersUp`,a.`opp_coach` as `OCoach`, c.franchise as `oppfranchise`, c.`Conference` AS `rConference`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise`=b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise`=c.`franchise`
WHERE `gametype`=9 AND `win`=1 ";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
//End of Table

//Populate Table
$_cp_sql = "TRUNCATE fc_cottongames";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

$_cp_sql = "INSERT INTO `fc_cottongames` 
SELECT CONCAT(a.`league`, '-',a.`season`) as `id`, a.`league`, a.`season`, a.`week`, CONCAT('<span style=\"font-weight: 900\">', b.`team`,'</span>') AS `Winners`,  a.`coach`  AS `Coach`,b.franchise ,b.`Conference` AS `Conference`,a.`score` AS `Score`, a.`opp_score` AS `oScore`,c.`team` AS `RunnersUp`,a.`opp_coach` as `OCoach`, c.franchise as `oppfranchise`, c.`Conference` AS `rConference`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise`=b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise`=c.`franchise`
WHERE `gametype`=10 AND `win`=1";
#echo "$_cp_sql";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
//End of Table

//Populate Table
$_cp_sql = "TRUNCATE fc_orangegames";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

$_cp_sql = "INSERT INTO `fc_orangegames` 
SELECT CONCAT(a.`league`, '-',a.`season`) as `id`, a.`league`, a.`season`, a.`week`, CONCAT('<span style=\"font-weight: 900\">', b.`team`,'</span>') AS `Winners`,  a.`coach`  AS `Coach`,b.franchise ,b.`Conference` AS `Conference`,a.`score` AS `Score`, a.`opp_score` AS `oScore`,c.`team` AS `RunnersUp`,a.`opp_coach` as `OCoach`, c.franchise as `oppfranchise`, c.`Conference` AS `rConference`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise`=b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise`=c.`franchise`
WHERE `gametype`=11 AND `win`=1";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
//End of Table

//Populate Table
$_cp_sql = "TRUNCATE fc_hawaiigames";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

$_cp_sql = "INSERT INTO `fc_hawaiigames` 
SELECT CONCAT(a.`league`, '-',a.`season`) as `id`, a.`league`, a.`season`, a.`week`, CONCAT('<span style=\"font-weight: 900\">', b.`team`,'</span>') AS `Winners`,  a.`coach`  AS `Coach`,b.franchise ,b.`Conference` AS `Conference`,a.`score` AS `Score`, a.`opp_score` AS `oScore`,c.`team` AS `RunnersUp`,a.`opp_coach` as `OCoach`, c.franchise as `oppfranchise`, c.`Conference` AS `rConference`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise`=b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise`=c.`franchise`
WHERE `gametype`=12 AND `win`=1";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
//End of Table

//Populate Table
$_cp_sql = "TRUNCATE fc_motorgames";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

$_cp_sql = "INSERT INTO `fc_motorgames` 
SELECT CONCAT(a.`league`, '-',a.`season`) as `id`, a.`league`, a.`season`, a.`week`, CONCAT('<span style=\"font-weight: 900\">', b.`team`,'</span>') AS `Winners`,  a.`coach`  AS `Coach`,b.franchise ,b.`Conference` AS `Conference`,a.`score` AS `Score`, a.`opp_score` AS `oScore`,c.`team` AS `RunnersUp`,a.`opp_coach` as `OCoach`, c.franchise as `oppfranchise`, c.`Conference` AS `rConference`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise`=b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise`=c.`franchise`
WHERE `gametype`=13 AND `win`=1";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
//End of Table

#$str="<h2>Processing Coaches.</h3>\n";
#output($str);

$totgwins=0;
$totglosses=0;
$totbwins=0;
$totblosses=0;
$totrswins=0;
$totrslosses=0;

$_cp_sql = "TRUNCATE fc_coaches";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 


$_cp_sql = "SELECT DISTINCT `league`, `coach` 
FROM `f_games` 
WHERE `league` LIKE 'NC%'
ORDER BY `league` ASC, `coach` ASC";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$i=1;
        while($row = fetch_row_db($result)){
				$_cp_myleague=$row[0];
				$_cp_mycoach=$row[1];
				
				#If active coach show current team and asterik
				$_cp_sql2 = "SELECT IFNULL((SELECT `team` FROM `fc_franchises` WHERE `coach`='$_cp_mycoach'), 'Retired') ";
				#echo "<p>$_cp_sql2</p>";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				while($row2 = fetch_row_db($result2)){
						#echo "<p>$row2[0]</p>";
						if ('Retired'==$row2[0]) {
							$_cp_myretirement=1;
							} else {
							$_cp_myretirement=0;
							$_cp_mycurrentteam=$row2[0];
							}

						}		
 								
				#If retired coach show old team(s)
				if (1==$_cp_myretirement){
					#echo "<p>Looking for old teams</p>";
					$_cp_myoldteams="";
					$_cp_sql3 = "SELECT DISTINCT b.`team` 
								 FROM `f_games` a
									INNER JOIN  `fc_franchises` b ON a.`franchise`=b.`franchise`  
								 WHERE a.`league` ='$_cp_myleague' AND a.`coach`='$_cp_mycoach' 
								 ORDER BY b.`team` ASC";
								#echo "<p>$_cp_sql3</p>";
								$result3 = $conn->prepare($_cp_sql3); 
								$result3->execute(); 
								while($row3 = fetch_row_db($result3)){
									$_cp_myoldteams.=$row3[0];	
									$_cp_myoldteams.=", ";
								}						

				} else {
					$_cp_myoldteams="";
				}
				$_cp_myoldteams=substr($_cp_myoldteams, 0, -2);
				
				#Determine wins and losses in Gold Bowl Playoffs
				$_cp_sql4 = "	SELECT SUM(a.`win`),SUM(a.`lose`)
								FROM `f_games` a
								WHERE a.`league` ='$_cp_myleague' AND a.`coach`='$_cp_mycoach'  AND (a.`gametype` =8 OR a.`gametype`=14)";
				#echo "<p>$_cp_sql4</p>";
				$result4 = $conn->prepare($_cp_sql4); 
				$result4->execute(); 
				while($row4 = fetch_row_db($result4)){
					$_cp_mywins=$row4[0];	
					$_cp_mylosses=$row4[1];		
				}
				
				if(is_null($_cp_mywins)){
					$_cp_mywins=0;
				} 					
				if(is_null($_cp_mylosses)){
					$_cp_mylosses=0;
				}				
				
				
				#Determine wins and losses in all Bowl Playoffs
				$_cp_sql4 = "	SELECT SUM(a.`win`),SUM(a.`lose`)
								FROM `f_games` a
								WHERE a.`league` ='$_cp_myleague' AND a.`coach`='$_cp_mycoach'  AND a.`gametype` >7 AND a.`gametype`<17 ";
				#echo "<p>$_cp_sql4</p>";
				$result4 = $conn->prepare($_cp_sql4); 
				$result4->execute(); 
				while($row4 = fetch_row_db($result4)){
					$_cp_mybwins=$row4[0];	
					$_cp_myblosses=$row4[1];		
				}
				
				if(is_null($_cp_mybwins)){
					$_cp_mybwins=0;
				} 					
				if(is_null($_cp_myblosses)){
					$_cp_myblosses=0;
				}					
				
				#Determine wins and losses in Regular Season
				$_cp_sql4 = "	SELECT SUM(a.`win`),SUM(a.`lose`), SUM(a.`tie`)
								FROM `f_games` a
								WHERE a.`league` ='$_cp_myleague' AND a.`coach`='$_cp_mycoach'  AND a.`gametype` =1  ";
				#echo "<p>$_cp_sql4</p>";
				$result4 = $conn->prepare($_cp_sql4); 
				$result4->execute(); 
				while($row4 = fetch_row_db($result4)){
					$_cp_myrswins=$row4[0];	
					$_cp_myrslosses=$row4[1];
					$_cp_myrsties=$row4[2];		
				}
				
				if(is_null($_cp_myrswins)){
					$_cp_myrswins=0;
				} 					
				if(is_null($_cp_myrslosses)){
					$_cp_myrslosses=0;
				}					
				if(is_null($_cp_myrsties)){
					$_cp_myrsties=0;
				}													
				
				#Determine National Championships
				$_cp_sql2 = "SELECT IFNULL((SELECT SUM(`win`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `gametype`=8 AND `coach`='$_cp_mycoach'), 0) AS 'NCWins'";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				while($row2 = fetch_row_db($result2)){
						$_cp_mync=$row2[0];
					}

				#Determine National Championship Runners Up
				$_cp_sql2 = "SELECT IFNULL((SELECT SUM(`lose`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `gametype`=8 AND `coach`='$_cp_mycoach'), 0) AS 'NCLosses'";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				while($row2 = fetch_row_db($result2)){
						$_cp_myru=$row2[0];
					}


				#Determine Rose Bowls
				$_cp_sql2 = "SELECT IFNULL((SELECT SUM(`win`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `gametype`=9 AND `coach`='$_cp_mycoach'), 0) AS 'RBWins'";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				while($row2 = fetch_row_db($result2)){
						$_cp_myrb=$row2[0];
					}				

				#Determine Cotton Bowls
				$_cp_sql2 = "SELECT IFNULL((SELECT SUM(`win`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `gametype`=10 AND `coach`='$_cp_mycoach'), 0) AS 'RBWins'";
				#echo "<p>$_cp_sql2</p>";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				while($row2 = fetch_row_db($result2)){
						$_cp_mycb=$row2[0];
					}	

				#Determine Orange Bowls
				$_cp_sql2 = "SELECT IFNULL((SELECT SUM(`win`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `gametype`=11 AND `coach`='$_cp_mycoach'), 0) AS 'RBWins'";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				while($row2 = fetch_row_db($result2)){
						$_cp_myob=$row2[0];
					}	

				#Determine Hawaii Bowls
				$_cp_sql2 = "SELECT IFNULL((SELECT SUM(`win`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `gametype`=12 AND `coach`='$_cp_mycoach'), 0) AS 'RBWins'";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				while($row2 = fetch_row_db($result2)){
						$_cp_myhb=$row2[0];
					}	

				#Determine Motor Bowls
				$_cp_sql2 = "SELECT IFNULL((SELECT SUM(`win`) FROM `f_games` WHERE `league`='$_cp_myleague' AND `gametype`=13 AND `coach`='$_cp_mycoach'), 0) AS 'RBWins'";
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				while($row2 = fetch_row_db($result2)){
						$_cp_mymb=$row2[0];
					}	
				
				#Write data to table
				if (1==$_cp_myretirement){
					#echo "<p>$i - $_cp_myleague - $_cp_mycoach$_cp_myretirement ($_cp_myoldteams). NC Record $_cp_mywins-$_cp_mylosses , Bowl Record $_cp_mybwins-$_cp_myblosses, Regular Season Record $_cp_myrswins-$_cp_myrslosses (t$_cp_myrsties)  </p>";
						$_cp_sql4 = "INSERT INTO `fc_coaches` (`id`, `league`, `coach`, `team`, `retired`, `Winner`, `Runnerup`, `RoseW`, `CottonW`, `OrangeW`, `HawaiiW`, `MotorW`, `ncwins`, `nclosses`, `bwins`, `blosses`, `rswins`, `rslosses`, `rsties`, `Link`) 
									VALUES ('$_cp_myleague-$_cp_mycoach','$_cp_myleague', '$_cp_mycoach', '$_cp_myoldteams', 'Y', $_cp_mync, $_cp_myru, $_cp_myrb, $_cp_mycb, $_cp_myob, $_cp_myhb, $_cp_mymb, '$_cp_mywins', '$_cp_mylosses', '$_cp_mybwins', '$_cp_myblosses', '$_cp_myrswins', '$_cp_myrslosses', $_cp_myrsties, NULL)";
				} else {
					#echo "<p>$i - $_cp_myleague - $_cp_mycoach. NC Record $_cp_mywins-$_cp_mylosses, Bowl Record $_cp_mybwins-$_cp_myblosses, , Regular Season Record $_cp_myrswins-$_cp_myrslosses   (t$_cp_myrsties)  </p>";	
								$_cp_sql4 = "INSERT INTO `fc_coaches` (`id`,`league`, `coach`, `team`, `retired`, `Winner`, `Runnerup`, `RoseW`, `CottonW`, `OrangeW`, `HawaiiW`, `MotorW`, `ncwins`, `nclosses`, `bwins`, `blosses`, `rswins`, `rslosses`, `rsties`, `Link`) 
								VALUES ('$_cp_myleague-$_cp_mycoach','$_cp_myleague', '$_cp_mycoach', '$_cp_mycurrentteam', '', $_cp_mync, $_cp_myru, $_cp_myrb, $_cp_mycb, $_cp_myob, $_cp_myhb, $_cp_mymb, '$_cp_mywins', '$_cp_mylosses', '$_cp_mybwins', '$_cp_myblosses', '$_cp_myrswins', '$_cp_myrslosses', $_cp_myrsties, NULL)";
				}
				#echo "<p>$_cp_sql4</p>";
				$result4 = $conn->prepare($_cp_sql4); 
				$result4->execute(); 

				
				$totgwins=$totgwins+$_cp_mywins;
				$totglosses=$totglosses+$_cp_mylosses;
				$totbwins=$totbwins+$_cp_mybwins;
				$totblosses=$totblosses+$_cp_myblosses;
				$totrswins=$totrswins+$_cp_myrswins;
				$totrslosses=$totrslosses+$_cp_myrslosses;
				

				$i++;
				
				#echo "<h2>Finished Coaches</h3>";

}


$totgwins=number_format($totgwins);
$totglosses=number_format($totglosses);
$totbwins=number_format($totbwins);
$totblosses=number_format($totblosses);
$totrswins=number_format($totrswins);
$totrslosses=number_format($totrslosses);


#$str="<h2>National Championship Games (including playoffs): $totgwins - $totglosses</h3>\n";
$str.="<h2>All Bowls (including playoffs): $totbwins - $totblosses</h3>\n";
$str.="<h2>Regular Season: $totrswins - $totrslosses</h3>\n";
$str.="</div>\n";
$str.="<br />";
$str.="</div>\n";
#output($str);

/* ---------------------------
   Footer stats
---------------------------- */
$time_end = microtime(true);
$time = $time_end - $time_start;

ob_start();
$render_kv([
  ['Elapsed time (s)', round($time, 2), 'w3-black'],
]);
$render_card('Summary', ob_get_clean(), 'w3-pale-yellow');

