<?php

//Dev
/*
$host = 'localhost';
$db_name = 'dev-gp';
$user = 'dev-gp';
$pass = 'EgpsSmr@m@LaarWd1eWtH2hde';
$title_application = '*DEV* Gameplan Sports Network *DEV*';
$enable_sql_logging = 0;
$enable_access_logging = 0;
$maintenance_mode = 0;
$debug_mode = 1;
$enable_uploads = 1;
$upload_directory = '/opt/lampp/htdocs/webroot/gp/gp2025/public_html/dev/uploads/';
*/

//Pre

$host = 'localhost';
$db_name = 'pre-gp';
$user = 'pre-gp';
$pass = '4s2u0s2pne!bbynrdhetrasK#';
$title_application = '*PRE* Gameplan Sports Network *PRE*';
$enable_sql_logging = 1;
$enable_access_logging = 1;
$maintenance_message = '<h1>Sorry Gameplan Sports Network is currently undergoing maintenance.</h1>';
$maintenance_mode = 0;
$debug_mode = 1;
$upload_directory = '/opt/lampp/htdocs/GP/gplan24/pre/uploads/';


//Server
/*
$host = 'sdb-78.hosting.stackcp.net';
$db_name = 'gplan8-35303737a645';
$user = 'gplan8-35303737a645';
$pass = 'hnebllfgibsb0n0irghsalEe';
$title_application = 'Gameplan Sports Network';
$enable_sql_logging = 0;
$enable_access_logging = 0;
$maintenance_message = '<h1>Sorry Gameplan Sports Network is currently undergoing maintenance.</h1>';
$maintenance_mode = 0;
$debug_mode = 0;
$upload_directory = '/home/sites/27a/8/826366cb9c/public_html/web/uploads';
*/

//Settings
$serial_number = '572FE7DD';
$dbms_type = 'mysql';
$timezone = 'Europe/London';
$secret_key = 'thedodoisveryextinctitwentextinctin1769thatwasaverysurprisingthing"£$%^&*()';
$dadabik_session_name = 'gpspn2023main131a';
$grid_layout_scrolling = 'grid_scroll';
$results_grid_fixed_header = 1;
$logo_img = 'images/gameplan.jpg';
$prefix_internal_table = 'zdk_';
$prefixes_to_exclude[0]='zda_';
$languages_ar = array ('english');
$number_failed_login_before_blocking = 3;
$grant_permissions_after_table_installation = 2;
$grant_permissions_autoincrement_after_table_installation = 0;
$enable_custom_php_pages = 1;
$enable_delete_all_feature = 0;
$csv_separator = ";";
$treat_blank_as_null = 1;
$null_word = 'NULL';
$null_checkbox = 0;
$enable_uploads = 1;
$date_format = 'literal_english';
$start_year = 1989;
$enable_data_tab_operations = 1;
$force_reload_css_js = 1;
$htmlawed_config = array('safe'=>1, 'deny_attribute'=>'class');
// items of the menu used to choose the number of result records displayed per page, you can add new numbers and/or delete the default ones, don't delete $records_per_page_ar[0]
$records_per_page_ar[0] = 20;
$records_per_page_ar[1] = 30;
$records_per_page_ar[2] = 50;
$records_per_page_ar[3] = 100;

//Bugfix 31/01/2023
if (isset($_GET['width_chart'] )){
	$_GET['width_chart'] = (int)$_GET['width_chart'];
}

if (isset($_GET['height_chart'] )){
	$_GET['height_chart'] = (int)$_GET['height_chart'];
}
?>
