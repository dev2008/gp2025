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



echo "<h2>Privacy policy</h2>";


echo "<p>This site complies with our legal obligations under both GDPR and PECR, the basis of our processing is <em>Legitimate interests</em> in that it is necessary for the running of our website. This has been confirmed by using the guidance tool on the <a href='https://ico.org.uk/for-organisations/gdpr-resources/lawful-basis-interactive-guidance-tool/lawful-basis-assessment-report'>ICO Website</a>.</p>";

echo "<p>We use cookies only as strictly necessary to enable data transmission over an electronic communication network (the Internet) and for services explicitly requested by the user (e.g. to search for specfic data).</p>";

echo "<p>Although consent is not strictly required for our data processing we are always happy to discuss any issues with you - we can be contacted at privacy @ this site's domain name.</p>";




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
