<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$franchises = '';
if (isset($_POST['franchises']) && !empty($_POST['franchises'])) {
	$franchises = $_POST['franchises'];
} 
//Check if Franchise move to be processed
if (isset($_POST['movefranchise']) && !empty($_POST['movefranchise'])) {
	$_cp_mychoice=$_POST['movefranchise'];
} else {
	$_cp_mychoice="No";
}
if ("Yes"<>$_cp_mychoice) {
switch($_cp_myunmatched) {	
case '1':

	$_cp_sql44="SELECT `team`,`coach`
	FROM `f_games` 
	WHERE (`franchise` = '' OR `franchise` IS NULL) AND `league`='$_cp_myleague' AND `season`=$_cp_myseason AND `week`=$_cp_myweek";
  $res44 = execute_db($_cp_sql44, $conn);
	while($row = fetch_row_db($res44)){
		$_cp_myunmatchedteam=$row[0];
		$_cp_myunmatchedcoach=$row[1];
		$str="<p><h1>$_cp_myunmatchedteam coached by $_cp_myunmatchedcoach are unmatched.</h1></p>";
		#output($str);	
	}	
  
  $_cp_sql444="SELECT `franchise`, `team`, `city`, `nickname`, `coach`
					FROM `fc_franchises`
					WHERE `franchise`
					NOT IN (SELECT `franchise` FROM `f_games` WHERE `league`='$_cp_myleague' AND `season`=$_cp_myseason AND `week`=$_cp_myweek);";  
  $res444 = execute_db($_cp_sql444, $conn);  				
	  while ($row = fetch_row_db($res444)) {
		  $_cp_myunmatchedfranchise=$row[0];
		  $_cp_myunmatchedfteam=$row[1];
		  $_cp_myunmatchedfcity=$row[2];
		  $_cp_myunmatchedfnickname=$row[3];
		  $_cp_myunmatchedfcoach=$row[4];
		  $str="<p><h1>$_cp_myunmatchedfteam ($_cp_myunmatchedfranchise) coached by $_cp_myunmatchedfcoach are unmatched.</h1></p>";
		  #output($str);
  }
  $str="<h2>It appears that Franchise #$_cp_myunmatchedfranchise has changed from $_cp_myunmatchedfteam to $_cp_myunmatchedteam?</h2>";
  output($str);	  
  				
  
break;
default:
	# code...
break;

}
$l=1;
//Show Form with franchise moves
echo "<form method='POST'>";
echo "<p><fieldset>";
echo "<legend>Proposed move:</legend>";
//This bit will need to convert to a loop
echo "<h2>Franchise #$_cp_myunmatchedfranchise has changed from $_cp_myunmatchedfteam to $_cp_myunmatchedteam</h2>";
echo "<input type='hidden' name='franchiseid[$l]' value='$_cp_myunmatchedfranchise'>";
echo "<input type='hidden' name='franchiseold[$l]' value='$_cp_myunmatchedfteam'>";
echo "<input type='hidden' name='franchisenew[$l]' value='$_cp_myunmatchedteam'>";
//End of loop part
echo "</fieldset></p>";
echo "<input type='hidden' name='movefranchise' value='Yes'>";
echo "<input type='submit' value='Submit'>";
echo "</form>";
echo "<br>";

//End of code for No franchise move to be processed
} else {
#$label="Form contents:";
#nz_debug($_POST,$label);	
//Process franchise moves
$franchiseid = $_POST['franchiseid'];
$franchiseold = $_POST['franchiseold'];
$franchisenew = $_POST['franchisenew'];

$franchiseids=array();
$franchiseolds=array();
$franchisenews=array();

$m=0;
$n=0;
$o=0;

foreach ($franchiseid as $value) {
	$m++;
	$franchiseids[$m]=$value;
	#print_r($franchiseids[$m]);
	#echo "<hr>";
}
foreach ($franchiseold as $value) {
	$n++;
	$franchiseolds[$n]=$value;
	#print_r($franchiseolds[$n]);
	#echo "<hr>";
}
foreach ($franchisenew as $value) { 
	$o++;
	$franchisenews[$o]=$value;
	#print_r($franchisenews[$o]);
	#echo "<hr>";
}

	//Need to split team name
	//TODO Make this a function!
	echo "<br />"; 	
	$string=$franchisenews[$o];
   	$last_space = strrpos($string, ' ');
   	$newnickname = substr($string, $last_space);
   	$newcity = substr($string, 0, $last_space);
   	#echo "<p>$last_word</p>";
   	#echo "<p>$first_chunk</p>";
	


for ($y = 1; $y <= $o; $y++) {
	$_cp_sql4444="UPDATE `fc_franchises` SET `team`='$franchisenews[$y]', `city`='$newcity', `nickname`='$newnickname' WHERE `franchise`=?";
	#echo "<p>$_cp_sql4444</p>";
	$_cp_data=$franchiseids[$y];
	$res4444 = nz_pdo_new($_cp_sql4444,$_cp_data,$conn);
	echo "<p>Result - $res4444</p>";
	
  }

//End of processing franchise moves
}


?>
