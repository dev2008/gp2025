<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require_once 'g_functions.php';
require_once 'mydatabase.php';

$str="<br /><div class='w3-card-4'>";
$str.="<header class='w3-container w3-blue-gray'>";
$str.="<h1>Gameplan Football - Backup Games</h1>";
$str.="</header>";
output($str);

$str="<div class='w3-container w3-pale-blue'>";
output($str);
//Text for middle box
$str="<h2>About to backup games table </h3>";
output($str);
$mytimestamp=$_SESSION['logged_user_infos_ar']["username_user"];
$mytimestamp.=idate("U");
$_cp_sql = "INSERT INTO `f_gamesold` SELECT '$mytimestamp',`id_game`, `league`, `season`, `week`, `team`, `franchise`, `coach`, `qb`, `safe`, `q1`, `q2`, `q3`, `q4`, `ot`, `score`, `fga`, `fgg`, `epa`, `epg`, `cva`, `cvg`, `punts`, `thirdcon`, `thirddowns`, `fourthcon`, `fourthdowns`, `firstd`, `passcmp`, `passatt`, `passyds`, `passlng`, `passlngtd`, `passtd`, `passpct`, `interception`, `hrd`, `skd`, `rush`, `rushyds`, `rushlng`, `rushlngtd`, `rushtd`, `fum`, `qbatt`, `qbyds`, `kr`, `kryds`, `krtd`, `pr`, `pryds`, `prtd`, `form1`, `form2`, `run1`, `run2`, `pass1`, `pass2`, `def1`, `def2`, `homeaway`, `gametype`, `opp_team`, `opp_franchise`, `opp_coach`, `opp_qb`, `opp_safe`, `opp_q1`, `opp_q2`, `opp_q3`, `opp_q4`, `opp_ot`, `opp_score`, `opp_fga`, `opp_fgg`, `opp_epa`, `opp_epg`, `opp_cva`, `opp_cvg`, `opp_punts`, `opp_thirdcon`, `opp_thirddowns`, `opp_fourthcon`, `opp_fourthdowns`, `opp_firstd`, `opp_passcmp`, `opp_passatt`, `opp_passyds`, `opp_passlng`, `opp_passlngtd`, `opp_passtd`, `opp_passpct`, `opp_interception`, `opp_hrd`, `opp_skd`, `opp_rush`, `opp_rushyds`, `opp_rushlng`, `opp_rushlngtd`, `opp_rushtd`, `opp_fum`, `opp_qbatt`, `opp_qbyds`, `opp_kr`, `opp_kryds`, `opp_krtd`, `opp_pr`, `opp_pryds`, `opp_prtd`, `opp_form1`, `opp_form2`, `opp_run1`, `opp_run2`, `opp_pass1`, `opp_pass2`, `opp_def1`, `opp_def2`, `win`, `lose`, `tie` FROM `f_games` WHERE 1";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
$number_of_rows = number_format($result->rowCount() ); 
$str="<div class='w3-container w3-teal'>\n";
$str.="<h2>Backed up $number_of_rows records.</h3>";
$str.="</div>\n";
$str.="<br />\n";
$str.="</div>\n";

output($str);

require_once 'g_footer.php';

?>

