<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

#Find all Leagues
//PDO Example with row count
$_cp_sql = "SELECT Distinct `League` FROM `a_turns` ";
try {
				$result = $conn->prepare($_cp_sql); 
				$result->execute(); 
				$number_of_rows = $result->rowCount() ; 
				#echo "<p>Rows - $number_of_rows</p>";
				$i=1;
} catch (PDOException $e) {
				echo "DataBase Error:<br>".$e->getMessage();
} catch (Exception $e) {
				echo "General Error:<br>".$e->getMessage();
}

//Loop through results
while($row = fetch_row_db($result)){
	$_cp_myleague = $row[0];
		#Find all Seasons
		$_cp_sql2 = "SELECT Distinct `Season` FROM `a_turns` WHERE  `League`='$_cp_myleague'";
		try {
				$result2 = $conn->prepare($_cp_sql2); 
				$result2->execute(); 
				$number_of_rows2 = $result2->rowCount() ; 
				#echo "<p>Rows - $number_of_rows</p>";
				$i=1;
		} catch (PDOException $e) {
						echo "DataBase Error:<br>".$e->getMessage();
		} catch (Exception $e) {
						echo "General Error:<br>".$e->getMessage();
		}	
		while($row2 = fetch_row_db($result2)){
		$_cp_myseason = $row2[0];
		
		#Loop through each league season and check for missing weeks
		#Write missing weeks to database table
		$x = 0;

		while($x <= 21) {
		echo "<p>$x</p>";

		$_cp_sql3 = "SELECT `turn_id` FROM `a_turns` WHERE  `league`='$_cp_myleague' AND `season`=$_cp_myseason   AND `week`=$x";
		#echo "<p>SQL - $_cp_sql3</p>";
		try {
				$result3 = $conn->prepare($_cp_sql3); 
				$result3->execute(); 
				$number_of_rows = $result->rowCount() ; 
				echo "<p>Rows - $number_of_rows</p>";

		} catch (PDOException $e) {
						echo "DataBase Error:<br>".$e->getMessage();
		} catch (Exception $e) {
						echo "General Error:<br>".$e->getMessage();
		}	
		while($row3 = fetch_row_db($result3)){
		$_cp_myresult3 = $row3[0];

		echo "<p>$_cp_myleague - $_cp_myseason - $x - $_cp_myresult3</p>";
		}
		$x++;
		}
	}
	$i++;
   }        


#Missing files can be viewed using Dadabik buult in functionality


$time_end = microtime(true);
$time = $time_end - $time_start;
echo "<p>Processed updates in ";
echo  round($time,2) . " s";
echo "</p>";

?>		
