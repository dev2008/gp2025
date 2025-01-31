<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

//Retrieve file details
$_cp_sql = "SELECT `upload_id`, `filename`, `league`, `season`, `week`, `processed`
			FROM `a_uploads` 
			WHERE `upload_id`=? 
			ORDER BY `processed` ASC,`league` ASC, `season` ASC, `week` ASC;";
$myrow=nz_pdo_row($_cp_sql,$_cp_mychoice,$conn);
#nz_debug($myrow,"Turn");
//Retrieve turn details
$_cp_myid = $myrow['upload_id'];
$_cp_myfile = $myrow['filename'];
$_cp_myleague = $myrow['league'];
$_cp_myseason = $myrow['season'];
$_cp_myweek = $myrow['week'];

$str="<h3>Analysing file with name $_cp_myfile</h3>";
output($str);

//Set variables
$j=0;
$rt=1;
// Ensure the upload directory has a trailing slash
$upload_directory = rtrim($upload_directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
// Safely concatenate the directory and the file name
$file = $upload_directory . $_cp_myfile;

$myfilearray=file($file);
if (1==$debug_mode) {
	($myfilearray);
}

$i=0;
$mycount=0;
$rounduprext='';
$myfilearray=remove_sp_chr($myfilearray);
$myfilearray=remove_sp_chr2($myfilearray);

//Loop through each line and determine details

foreach ($myfilearray as $myline){ 
	$myline=trim($myline);
	#nz_debug($myline,"Line: $i");
	$mycount++;	
	//Report interim number of lines
	if ($mycount % 500 == 0) {
		$str='Processed ';
		$str.=number_format($mycount);
		$str.=' lines / ';
		output($str);
	}
//Roundup is normally ordered but is *issued* in a new league	
if ((preg_match_all('~\b(roundup|ordered)\b~i', $myline, $matches)==3) OR preg_match_all('~\b(roundup|issued)\b~i', $myline, $matches)==3) {
	$roundup='Y';
	$rounduprext='with roundup ';
}

				#echo strlen($myline);
				#echo "<br />";
				//preg_match_all returns 1 for each word found so check for all 4
				//check if Baseball Turn
				//TODO widen out to other games
				if (preg_match_all('~\b(Baseball|Team|Report|Season)\b~i', $myline, $matches)==4) {
						echo "<p>Looks like a Baseball turn!</p>";	
						$_cdp_type="Baseball";
						require_once 'g_turns_baseball.php';
				}	elseif (preg_match_all('~\b(SLAPSHOT|Team|Report|Season)\b~i', $myline, $matches)==4) {
						echo "<p>Looks like an Ice Hockey turn!</p>";
						$_cdp_type="Ice Hockey";
						require_once 'g_turns_icehockey.php';
				}

	//End of loop through lines
	$rt++;
	$i++;
}	
	 
//Report final number of lines
$str='Processed ';
$str.=number_format($mycount);
$str.=' lines.';
output($str);

//Insert into turns summary
if (1==$_cdp_valid){
$str="<h3>Found $_cdp_type turn $rounduprext for $_cp_league s$_cp_season w$_cp_week coached by $_cp_mycoachname</h3>";
output($str);
$_cp_sql="INSERT INTO `g_turnsummary` 
				(`turn_id`, `game`, `league`, `season`, `week`, `coach`, `mytimestamp`, `roundup`,`uploadID`) 
			VALUES 
				(NULL, '$_cdp_type', '$_cp_league', '$_cp_season', '$_cp_week', '$_cp_mycoachname', current_timestamp(), '$roundup',$_cp_myid);";
	$last_id=nz_pdo($_cp_sql,$conn);			
	$str="<h3>Valid $_cdp_type turn ($_cp_league s$_cp_season w$_cp_week - $_cp_mycoachname) stored in database with id $_cp_myid.</h3>";
	output($str);
	
//Update uploads
$_cp_sql="UPDATE `a_uploads` SET `processed`=1, `league`='$_cp_league', `season`=$_cp_season, `week`=$_cp_week  WHERE `upload_id`='$_cp_mychoice'";
nz_pdo($_cp_sql,$conn);
$str="<h3>Uploads table updated with turn details!</h3>";
output($str);

//Insert into turns full
$i=0;
$str="<p>";
$str="<h3>About to load full turn details into db!</h3>";
output($str);
foreach ($myfilearray as $myline){
	$myline2=trim($myline);
	$_cp_sql="INSERT INTO `g_turnsfull` (`tf_id`, `up_id`, `tf_seq`, `tf_line`) VALUES (NULL, $_cp_myid, $i, '$myline2');";
	$last_id=nz_pdo($_cp_sql,$conn);
	$i++;	
	//Report interim number of lines
	if ($i % 100 == 0) {
		$str='Inserted ';
		$str.=number_format($i);
		$str.=' lines / ';
		output($str);
	}		
}
$str='Inserted ';
$str.=number_format($i);
$str.=' lines </p>';
output($str);
$str="<h3>$i lines of turn $_cp_myid ($_cp_league s$_cp_season w$_cp_week - $_cp_mycoachname) stored in database.</h3>";
output($str);
$str="";

$_cp_sql="UPDATE `a_uploads` SET `processed`=1 WHERE `upload_id`=$_cp_myid";
nz_pdo($_cp_sql,$conn);

	
} else {
	$str="<h3>Sorry there appears to be a problem with this turn!</h3>";
	output($str);
}



require_once 'g_footer.php';
?>
