<?php
$myserver=$_SERVER['SERVER_NAME'];
$mybrowser=$_SERVER['HTTP_USER_AGENT'];
$mypage=$_SERVER['SCRIPT_FILENAME'];
echo "<!doctype html>";
echo "<html>";
echo "<head>";
echo "<title>WebRoot @ $myserver</title>";
echo "</head>";
echo "<body>";
if(isset($_GET['debug'])) {
	echo "<p>You are accessing <b>$mypage</b> using <em>$mybrowser</em>.</p>";
	print '<pre> POST - '  . print_r($_SERVER, true) . '</pre>'; 
	phpinfo();
} else {
	echo "<p>This page is under development --NZ</p>";
}
