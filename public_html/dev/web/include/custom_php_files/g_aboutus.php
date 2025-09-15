<?php
//Version 11.4.2 
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require_once 'g_functions.php';

//Create basic layout
$str="";
$str.= "<div class='w3-panel w3-theme-d5 w3-text-white w3-round-xxlarge '>";
$str.="<h1>Who are we?</h1>";
$str.= "<div class='w3-panel w3-theme w3-round-large '>";
output($str);

#Main content
$str="";
$str.="<p>This site is owned and operated by <a href='https://www.milnesconsultancy.co.uk/'>Milnes Consultancy Ltd</a>, a family owned IT Consultancy.</p>";
$str.="<p>Two members of the family are avid players of the games run by <a href='https://www.pbmsports.co.uk/index.htm'>Danny McConnell</a> and <a href='https://www.sidetracks.co.uk'>Peter Calcraft</a> and this site is designed to aid peoples enjoyment of them by providing historical records and providing context for current seasons and turns.</p>";
$str.="<p>All information on this site is extracted from official turns by our own home grown software and then displayed using the <a href='https://dadabik.com'>Dadabik framework</a>. Our code is open source and hosted on GitHub, please contact alan @ this site's domain name for access.</p>";
$str.="<p><em>Please note our stat extraction system is not flawless, in the event of a disrepancy between your turn and this site then your turn is correct and this site is wrong!</em></p>";

output($str);


//End of page
$str="</div>";
output($str);

//Start of footer
require_once 'g_footer.php';
?>
