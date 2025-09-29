<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
include_once('error_handler.php');

echo "<h1>Gameplan Football - Audit</h1>";


//Franchise check
$_cp_sql = "SELECT `league`,`season` FROM `f_games` WHERE `franchise` = '' or `franchise` is null";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$number_of_rows = $result->rowCount();
if ($number_of_rows>0){
	echo "<p>$number_of_rows Franchises not matched.</p>";
	   while($row = fetch_row_db($result)){
			echo "<p>$row[0] - $row[1] not matched</p>";
		}
} else {
	echo "<p>All Franchises matched.</p><hr />";
}


//Opp Franchise check
$_cp_sql = "SELECT  `franchise`,count(`id_game`) FROM `f_games` 
WHERE  `week`>0 AND `week`<17 
GROUP BY `franchise`
ORDER BY `franchise` ASC";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$i=0;
$number_of_rows = $result->rowCount();
while($row = fetch_row_db($result)){
	if ($i<>$row[1]){
		echo "<p>$row[0] - $row[1]</p>";
		$i=$row[1];
		#echo "<p>$i</p>";
	} else {
		$i=$row[1];
	}
}

//End of page
echo "<p>End of Audit.</p>";

$time_end = microtime(true);
$time = $time_end - $time_start;
if (1==$debug_mode){
echo "<p>Processed updates in ";
echo  round($time,2) . " s";
echo "</p>";
}

?>
