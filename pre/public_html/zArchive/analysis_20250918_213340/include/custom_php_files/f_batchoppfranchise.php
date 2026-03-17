<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'g_functions.php';
require_once 'mydatabase.php';

$time_start = microtime(true);
$str="<br /><div class='nz-card'>";
$str.="<div class='w3-container $mycolour6'>";
$str.="<div class='w3-pale-green'>";
$str.="<h1>Processing opposition franchises</h1>";
$str.="</div>";
$str.="</header>";
output($str);
$str= "<div class='w3-panel $mycolour4 nz-card w3-round-xxlarge'>";
output($str);
$str="<h2>Finding Leagues</h3>";
output($str);
//Start League Loop
$_cp_sql = "SELECT Distinct League FROM `f_games` ORDER BY `League` ASC";

$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$number_of_rows = $result->rowCount() ; 
$str="<h2>Found $number_of_rows leagues</h3>";
output($str);
$j=0;
//This is the main League loop
while($row = fetch_row_db($result)){
	$_cp_myleague = $row[0];
	#$str="<p>League is $_cp_myleague</p>";
	#output($str);	

	//Start Season Loop
	//Loop through each Season for this League
	$_cp_sql = "SELECT Distinct `Season`  FROM `f_games` WHERE `League` = '$_cp_myleague' ORDER BY `Season` ASC";
	$result2 = $conn->prepare($_cp_sql); 
	$result2->execute(); 
	$number_of_rows2 = $result2->rowCount() ; 
	$j=$j+$number_of_rows2;
	#$str="<h2>Found $number_of_rows records</h3>";
	#output($str);
	$str="<h2>Looping through $number_of_rows2 $_cp_myleague Seasons.</h3>";
	$myseasons=$number_of_rows;
	$myprocessedseasons=0;
	$mycount=0;
	output($str);
	while($row = fetch_row_db($result2)){
		$_cp_myseason = $row[0];
		
		//Start Week Loop

			$_cp_sql = "SELECT Distinct `Week` FROM `f_games` WHERE `League` = '$_cp_myleague' and `Season` = $_cp_myseason AND `Week` < 90 ORDER BY `Week` ASC";
			$result3 = $conn->prepare($_cp_sql); 
			$result3->execute(); 
			$number_of_rows = $result3->rowCount() ; 
			#$str="$_cp_sql";
			#output($str);
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
$mycount++;	
if ($mycount % 10 == 0) {
$str='Processed ';
$str.=$mycount;
$str.=' Seasons / ';
output($str);
}

		}

//End Season Loop


//End League loop
$str='Processed ';
$str.=$mycount;
$str.=' Seasons.</p>';
output($str);
}



$str="</div>";
output($str);

//Start of footer
require_once 'g_footer.php';


?>
