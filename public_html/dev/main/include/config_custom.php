<?php
declare(strict_types=1);

$envLoader = dirname(__FILE__, 1) . '/custom_php_files/env_loader.php';
if (!is_readable($envLoader)) {
    throw new RuntimeException("Env loader missing at $envLoader");
}
require_once $envLoader;
Env::load();

$env = Env::get('APP_ENV', 'dev');
$isProd = $env === 'prod';

$host               = Env::get('DB_HOST', 'localhost');
$db_name            = Env::get('DB_NAME', '');
$user               = Env::get('DB_USER', '');
$pass               = Env::get('DB_PASS', '');

$title_application  = Env::get('TITLE_APP', 'Gameplan Sports Network');

$enable_sql_logging    = Env::int('SQL_LOG', 0);
$enable_access_logging = Env::int('ACCESS_LOG', 0);
$maintenance_mode      = Env::int('MAINTENANCE_MODE', 0);
$maintenance_message   = Env::get('MAINTENANCE_MSG', '<h1>Maintenance</h1>');
$debug_mode            = Env::int('DEBUG_MODE', $isProd ? 0 : 1);

$enable_uploads     = Env::int('ENABLE_UPLOADS', 1);
$upload_directory   = Env::get('UPLOAD_DIR', __DIR__ . '/uploads/');

$serial_number      = '572FE7DD';
$dbms_type          = 'mysql';
$timezone           = Env::get('TIMEZONE', 'Europe/London');
$secret_key         = Env::get('SECRET_KEY') ?: 'change_me';          // supply via environment if you prefer
$dadabik_session_name = Env::get('DADABIK_SESSION_NAME', 'gpspn2023main131a');

$grid_layout_scrolling = 'grid_scroll';
$results_grid_fixed_header = 1;
$logo_img = 'images/gameplan.jpg';
$prefix_internal_table = 'zdk_';
$prefixes_to_exclude[0]='zda_';
$languages_ar = array('english');
$number_failed_login_before_blocking = 3;
$grant_permissions_after_table_installation = 2;
$grant_permissions_autoincrement_after_table_installation = 0;
$enable_custom_php_pages = 1;
$enable_delete_all_feature = 0;
$csv_separator = ";";
$treat_blank_as_null = 1;
$null_word = 'NULL';
$null_checkbox = 0;
$date_format = 'literal_english';
$start_year = 1989;
$enable_data_tab_operations = 1;
$force_reload_css_js = 1;
$htmlawed_config = array('safe'=>1, 'deny_attribute'=>'class');
$records_per_page_ar = [20,30,50,100];

// Optional: apply timezone immediately
@date_default_timezone_set($timezone);
