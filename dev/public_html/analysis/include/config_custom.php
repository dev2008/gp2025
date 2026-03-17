<?php
declare(strict_types=1);

$envLoader = dirname(__FILE__,1) . '/custom_php_files/env_loader.php';
require_once $envLoader;
Env::load();

$host     = Env::get('DB_HOST','localhost');
$db_name  = Env::get('DB_NAME','');
$user     = Env::get('DB_USER','');
$pass     = Env::get('DB_PASS','');
$title_application = Env::get('TITLE_APP','Gameplan Analysis');

$enable_sql_logging    = Env::int('SQL_LOG',0);
$enable_access_logging = Env::int('ACCESS_LOG',0);
$maintenance_mode      = Env::int('MAINTENANCE_MODE',0);
$debug_mode            = Env::int('DEBUG_MODE',0);
$enable_uploads        = Env::int('ENABLE_UPLOADS',1);
$upload_directory      = Env::get('UPLOAD_DIR', __DIR__.'/uploads/');
$timezone              = Env::get('TIMEZONE','Europe/London');
$dadabik_session_name  = Env::get('DADABIK_SESSION_NAME','gpspn2023analysis31a');

//Settings
$serial_number = '572FE7DD';
$dbms_type = 'mysql';
$timezone = 'Europe/London';
$secret_key = '2020rnubonepdrasedhnyraatv1969teisehesdeuatsostirlihukbswpt1984';
$dadabik_session_name = 'gpspn2023analysis31a';
$prefix_internal_table = 'yda_';
$prefixes_to_exclude[0]='zdk_';
$languages_ar = array ('english');
$grant_permissions_after_table_installation = 1;
$grant_permissions_autoincrement_after_table_installation = 0;
$enable_custom_php_pages = 1;
$enable_custom_button_functions = 1;
$enable_delete_all_feature = 0;
$number_failed_login_before_blocking = 5;
$enable_data_tab_operations = 0;
$csv_separator = ",";
$null_word = 'n/a';
$date_format = 'literal_english';
$date_format_edit = 'J-M-Y';
$date_time_format_edit = 'J-M-Y H:i:S';
$autoconf['default']['search_operators'] = 'contains/doesnt_contain/is_equal/is_different/starts_with/ends_with/greater_than/less_than/greater_equal_than/less_equal_than/is_null/is_not_null/is_empty/is_not_empty/between';
$htmlawed_config = array('safe'=>1, 'deny_attribute'=>'class');
$force_reload_css_js = 1;
$enable_forgotten_password = 1;
?>
