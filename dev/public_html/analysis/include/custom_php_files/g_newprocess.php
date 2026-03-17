<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }

require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';


$time_start = microtime(true);

echo "<h1>Gameplan - Process turns</h1>";

#Back up table
echo "<h2>Finding first turn to process ......</h2>";
$mytimestamp=$_SESSION['logged_user_infos_ar']["username_user"];
$mytimestamp.=idate("U");
$_cp_sql = "SELECT `upload_id`, `filename`, `league`, `season`, `week`, `mytimestamp`, `processed` FROM `g_uploads` ORDER BY `mytimestamp` ASC LIMIT 1;";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 

while($row = fetch_row_db($result)){
	$_cp_filename =  $row['filename'];
} 
echo "<h4>Found: $_cp_filename</h4>";
// (A) OPEN FILE
$_cdp_handle = fopen("$upload_directory/$_cp_filename", "r") or die("Error reading file!");
 
// (B) READ LINE BY LINE
$i=1;
while (($_cp_line = fgets($_cdp_handle)) !== false) {
// To better manage the memory, you can also specify how many bytes to read at once
// while (($line = fgets($handle, 4096)) !== false) {

	if (1==$i){
		if ("Gameplan Data Extraction by gp_vcon (v 0.5.12b)"<>substr("$_cp_line",0,47)){
			echo "<h4>File does not appear to be a valid extract!</h4>";
			$_cdp_valid=0;
		} else {
			echo "<p>File appears to be OK!</p>";
			$_cdp_valid=1;
		}
	}

if (0<>$i AND 1==$_cdp_valid ){	
  echo "<p>#$i - ";
  print_r ($_cp_line);
  echo "</p>";  
}
  $i++;
}
 
// (C) CLOSE FILE
fclose($handle);

$time_end = microtime(true);
$time = $time_end - $time_start;
if (1==$debug_mode){
echo "<p>Processed updates in ";
echo  round($time,2) . " s";
echo "</p>";
}

?>
