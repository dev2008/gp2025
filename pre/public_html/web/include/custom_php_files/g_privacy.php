<?php
//Version 11.4.2 
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require_once 'g_functions.php';

//Create basic layout
$str="";
$str.= "<div class='w3-panel w3-theme-d5 w3-text-white w3-round-xxlarge '>";
$str.="<h1>Privacy policy</h1>";
$str.= "<div class='w3-panel w3-theme w3-round-large '>";
output($str);

#Main content
$str="";
$str.="<p>This site complies with our legal obligations under both GDPR and PECR, the basis of our processing is <em>Legitimate interests</em> this approach has been confirmed by using the guidance and tools on the <a href='https://ico.org.uk/for-organisations/gdpr-resources/lawful-basis-interactive-guidance-tool/lawful-basis-assessment-report'>ICO Website</a>.</p>";
$str.="<p>We use cookies only as strictly necessary to enable data transmission over an electronic communication network (the Internet) and for services explicitly requested by the user (e.g. to search for specfic data).</p>";
$str.="<p>Although consent is not strictly required for our data processing we are always happy to discuss any issues with you - we can be contacted at privacy @ this site's domain name.</p>";
output($str);


//End of page
$str="</div>";
output($str);

//Start of footer
require_once 'g_footer.php';
?>
