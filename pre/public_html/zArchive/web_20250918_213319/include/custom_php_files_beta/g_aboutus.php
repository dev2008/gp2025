<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once('include/custom_php_files/g_functions.php');
$_cp_myname=$_SESSION['logged_user_infos_ar']["username_user"];
echo "<br />";
echo "<div class='w3-card-4'>";

echo "<header class='w3-container $mycolour1'>";
if ('db5000273827.hosting-data.io' == $host){
		echo "<h1>Welcome to gameplan.org.uk</h1>";
		echo " <h1>$_cp_myname logged on to ** UAT **</h1>";
		echo "<br />";
	}
		elseif ('localhost' == $host) {
		echo "<h1>Welcome to gameplan.org.uk";
		echo " <h1>$_cp_myname</em>  logged on to ** DEV **</h1>";
		echo "<br />";
	}	else {
		echo "<h1>Welcome to gameplan.org.uk</h1>";
	}
echo "</header>";

//Main section
echo "<div class='w3-container $mycolour2'>";
//Start of content
echo "<div class='w3-panel $mycolour3 w3-card-4 w3-round-xxlarge'>";



echo "<h2>Who are we?</h2>";


echo "<p>This site is owned and operated by <a href='https://www.milnesconsultancy.co.uk/'>Milnes Consultancy Ltd</a>, a family owned IT Consultancy.</p>";

echo "<p>Two members of the family are avid players of the games run by <a href='https://www.pbmsports.co.uk/index.htm'>Danny McConnell</a> and <a href='https://www.sidetracks.co.uk'>Peter Calcraft</a> and this site is designed to aid peoples enjoyment of them by providing historical records and providing context for current seasons and turns.</p>";

echo "<p>All information on this site is extracted from official turns by our own home grown software and then displayed using the <a href='https://dadabik.com'>Dadabik framework</a>. Our code is open source and hosted on GitHub, please contact alan @ this site's domain name for access.</p>";

echo "<p><em>Please note our stat extraction system is not flawless, in the event of a disrepancy between your turn and this site then your turn is correct and this site is wrong!</em></p>";

//end of content
echo "<br />";
echo "</div>";
#if ($current_id_group==1) {
#echo "<pre>";
#print_r($_SESSION);
#echo "</pre>";
#}


#Footer
echo "<br />";
#Close yellow section
echo "</div>";
echo "<footer class='w3-container $mycolour1'>";
$_cp_sql1 = "SELECT `modification_time` FROM `f_games` WHERE 1 ORDER BY `modification_time` DESC LIMIT 1";
#$str="$_cp_sql1";        
#output($str);


$res1 = execute_db($_cp_sql1, $conn);
        while($row = fetch_row_db($res1)){
			$_cp_updated = $row[0];
        }

$str="<div class='w3-right-align'>System was last updated on ";
$changeDate = date("d-m-Y H:i", strtotime($_cp_updated));
$str.= "$changeDate";

$str.= "</div>\n";
$str.="</footer>\n";
$str.="</div>\n";        

output($str);
echo "</div>";

echo "</footer>";
#End of card class
echo "</div>";
?>
