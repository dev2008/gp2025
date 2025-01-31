<?php
///////////////////////////////////////////////////////////////////////////////////////////////////////
//                                                                                                   //
//  Copyright (c) 2022 https://stackoverflow.com/users/5507893/deep64blue                            //
//                                                                                                   //
//  This is a PDO wrapper class, it can be used with any system however it was designed for          //
//  projects using the Dadabik framework https://dadabik.com                                         //
//                                                                                                   //
//  This class is based on the work of https://stackoverflow.com/users/285587/your-common-sense      //
//  Check out his website at https://phpdelusions.net                                                //
//  The core of this class is based on the 'singleton' example found at                              //
//  https://phpdelusions.net/pdo/pdo_wrapper                                                         //
//                                                                                                   //
///////////////////////////////////////////////////////////////////////////////////////////////////////

//Dadabik sets the db values, for other systems change value as required
$_cp_host=$host;
$_cp_db=$db_name;
$_cp_user=$user;
$_cp_pass=$pass;
$_cp_char='utf8';
//Dadabik sets $debug_mode, for other systems change value as required
//1 = display detailed errors; 0 = don't display details 
$mydebug=$debug_mode;

//alternatively delete above lines and define directly here
define('myDB_HOST', $_cp_host);
define('myDB_NAME', $_cp_db);
define('myDB_USER', $_cp_user);
define('myDB_PASS', $_cp_pass);
define('myDB_CHAR', $_cp_char);

class myDB
{
    protected static $instance = null;

    protected function __construct() {}
    protected function __clone() {}

    public static function instance()
    {
        if (self::$instance === null)
        {
            $opt  = array(
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => FALSE,
            );
            $dsn = 'mysql:host='.myDB_HOST.';dbname='.myDB_NAME.';charset='.myDB_CHAR;
            self::$instance = new PDO($dsn, myDB_USER, myDB_PASS, $opt);
        }
        return self::$instance;
    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(self::instance(), $method), $args);
    }

    public static function run($sql, $args = [])
    {
        if (!$args)
        {
             return self::instance()->query($sql);
        }
        $stmt = self::instance()->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
}


function build_table($array){
// start table
$html = "<table  class='w3-table w3-striped w3-bordered'>";
// header row
$html .= "<tr class='w3-amber'>";
foreach($array[0] as $key=>$value){
	$html .= "<th>" . htmlspecialchars($key) . "</th>";
}
$html .= '</tr>';

// data rows
$j=0;
foreach( $array as $key=>$value){
	if (($j % 2) == 1) {
		$html .= "<tr class='w3-pale-red'>";
	} else {
		$html .= "<tr  class='w3-pale-yellow'>";
	}	
foreach($value as $key2=>$value2){
	$html .= "<td>" . htmlspecialchars($value2) . "</td>";
}
$html .= "</tr>";
$j++;
}

// finish table and return it

$html .= "</table>";
return $html;
}
