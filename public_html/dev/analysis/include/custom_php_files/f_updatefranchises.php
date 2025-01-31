<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'g_functions.php';
require_once 'mydatabase.php';

$time_start = microtime(true);
//Create basic layout
$str="<div class='w3-container $mycolour6 w3-round-xxlarge'>";
$str.="<div class='w3-pale-green w3-round-large'>";
$str.="<h1>&nbsp;Processing Franchises</h1>";
$str.="</div>";
$str.="</header>";
output($str);
$str= "<div class='w3-panel $mycolour4 w3-round-medium '>";
output($str);

//Fix Washington Name
$str="<h4>Fixing Washington name</h4>";
output($str);
$_cp_sql2 = "UPDATE `f_games` SET `team`='Washington Commanders' WHERE `team` LIKE 'Washington%'";
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 

$_cp_sql2 = "UPDATE `f_games` SET `opp_team`='Washington Commanders' WHERE `opp_team` LIKE 'Washington%'";
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 

$_cp_sql2 = "UPDATE `n_playbyplay` SET `a_off`='WC' WHERE `a_off`='WR' OR `a_off`='WT'";
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 

$_cp_sql2 = "UPDATE `n_playbyplay` SET `a_def`='WC' WHERE `a_def`='WR' OR `a_def`='WT'";
$result = $conn->prepare($_cp_sql2); 
$result->execute(); 

//Update Gametypes
$str="<h4>Updating Gametypes</h4>";
output($str);

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
$str="<p>$number_of_rows Silver Bowl games converted </p>";


//Now process Franchises
$str="<h4>Finding first unallocated Franchise</h4>";
output($str);
$_cp_sql2 = "SELECT MIN(`season`) FROM `f_games` WHERE `franchise` = '' OR `franchise` is null OR LENGTH(`franchise`)=0";

$result = $conn->prepare($_cp_sql2); 
$result->execute(); 
$number_of_rows = $result->rowCount(); 
   while($row = fetch_row_db($result)){
			$_cp_myseason = $row[0];
			if (1==$debug_mode) {
				$str="<p>Season - $_cp_myseason</p>";
				#output($str);	
			}	
   }  
 

if (is_null($_cp_myseason)) {	
	$str="<h2>All Franchises allocated!</h4>";
	output($str);	
	} else {
	$str="<h4>Allocating Franchises</h4>";
	output($str);
	#Find league matching season with no franchise
	try {
		$_cp_sql2 = "SELECT `league` FROM `f_games` WHERE `season`= $_cp_myseason AND (`franchise` = '' OR `franchise` is null OR LENGTH(`franchise`)=0)";

		$result = $conn->prepare($_cp_sql2);
		$result->execute();
		$number_of_rows = $result->rowCount();
		while ($row = fetch_row_db($result)) {
			$_cp_myleague = $row[0];
			#$str="<p>$row[0] - $row[1]</p>";
		}


		#$str="<p>Rows - $number_of_rows $_cp_myleague $_cp_myseason</p>";

		#if ($number_of_rows > 0 ) {
		if (!empty($_cp_myleague)) {
			#Loop called if records to look at
			$_cp_sql3 = "SELECT MIN(`week`) FROM `f_games` WHERE `season` = $_cp_myseason AND `league` = '$_cp_myleague' AND (`franchise` = '' or `franchise` is null OR LENGTH(`franchise`)=0)";
			$res3 = execute_db($_cp_sql3, $conn);
			while ($row = fetch_row_db($res3)) {
				$_cp_myweek = $row[0];
			}

			#Print results
			$str="<p><h3>No Franchises found for $_cp_myleague s$_cp_myseason w$_cp_myweek.</h3></p>";
			output($str);

			#Now we have found the first week to process loop through it and match Teams to Franchises
			$str="<h4>Updating Franchises ......</h4>";
			output($str);
			#Set Pro or College
			$_cp_mysub=substr($_cp_myleague,0,2);
			if ('NF'==$_cp_mysub) {
				$_cp_myfranchises='fp_franchises';
				$str="<p>Set Franchise to Pro - ".$_cp_mysub.".</p>";
			} else {
				$_cp_myfranchises='fc_franchises';
				$str="<p>Set Franchise to College - ".$_cp_mysub.".</p>";
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
				#$str="<p>$_cp_sql1</p>";
				$res1 = execute_db($_cp_sql1, $conn);
				while ($row = fetch_row_db($res1)) {
					$_cp_myfranchise=$row[0];
					$_cp_sql2="UPDATE `f_games` SET `franchise`= $_cp_myfranchise, modification_time=NOW() WHERE `id_game` ='$_cp_mygameid' ";
					#$str="<p>$_cp_sql2</p>";
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
					$str="<p><h2>All Franchises allocated for $_cp_myleague s$_cp_myseason w$_cp_myweek.</h4></p>";
					output($str);
				break;
				case 1:
					$str="<p><h2>1 Franchise moved!</h2></p>";
					output($str);
					if ('fp_franchises'==$_cp_myfranchises){
						require_once 'fp_movefranchise.php';	
					} else {
						require_once 'fc_movefranchise.php';	
					}
					
				break;				
				default:
					$str="<p><h2>$_cp_myunmatched Franchises moved!</h2></p>";
					$str.="<p><h2>Currently we only support single Franchise moves!</h2></p>";
					output($str);
					break;
			}


			#End of overall loop
		} else {
			$str="<p><h2>All Franchises allocated in database.</h4></p>";
			output($str);
		}
	} catch (PDOException $e) {
		$str="<p>SQL - $_cp_sql2</p>";
		$str.="DataBase Error:<br>".$e->getMessage();
		output($str);
	} catch (Exception $e) {
		$str="<p>SQL - $_cp_sql2</p>";
		$str.="General Error:<br>".$e->getMessage();
		output($str);
	}
}

//Flag update has been run
$_cp_sql="INSERT INTO `g_updated` (`updated_id`, `updated_when`) VALUES (NULL, current_timestamp());";
nz_pdo($_cp_sql,$conn);


//End of page
$str="</div>";
output($str);

//Start of footer
require_once 'g_footer.php';
?>
