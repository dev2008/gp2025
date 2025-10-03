<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }

function nz_dumpform() {
    #print '<pre> POST - '  . print_r($_POST, true) . '</pre>'; 
    #print '<pre> GET - '  . print_r($_GET, true) . '</pre>'; 
    print '<pre> SESSION - '  . print_r($_SESSION, true) . '</pre>'; 
}



$league = "NFLAR";

echo "<div class='w3-card w3-padding'>";
echo "<b>Processing log $league</b>";
echo "<div id='log' class='w3-small w3-padding'";
echo "style='white-space:pre-wrap;background:#fff;border:1px solid #ccc;height:260px;overflow:auto;'></div>";

nz_dumpform();


echo "</div>";

