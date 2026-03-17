<?php
/////////////////////////////////////////////////////////////////////////////////////////////////
//                                                                                             //
// Copyright (c) 2022 https://stackoverflow.com/users/5507893/deep64blue                       //
//                                                                                             //
// This file gives examples of how to use the PDO wrapper class                                //
//                                                                                             //
// These examples are based on the 'singleton' examples found at                               //
// https://phpdelusions.net/pdo/pdo_wrapper                                                    //
//                                                                                             //
/////////////////////////////////////////////////////////////////////////////////////////////////

//Delete this if not using Dadabik
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }

//Include the database class
require_once 'mydatabase.php';

#Table creation
#To test debug remove 'temporary' from sql statement
$_cp_mysql="CREATE temporary TABLE pdowrapper (id int auto_increment primary key, name varchar(255))";
	try {
		myDB::query("$_cp_mysql");
	} catch (PDOException $e) {
		if (1==$debug_mode) {
			echo "DataBase Error:<br>".$e->getMessage();
			echo "<pre></pre>$_cp_sql</pre>";
		}
		exit ("<h1>****Warning - processing stopped on database error****</h1>");
	} catch (Exception $e) {
		if (1==$mydebug) {
			echo "General Error:<br>".$e->getMessage();
		}	
		exit ("<h1>****Warning - processing stopped on general error****</h1>");
	}


# Prepared statement multiple execution
$_cp_mysql="INSERT INTO pdowrapper VALUES (NULL, ?)";
$stmt = myDB::prepare("$_cp_mysql");
foreach (['Sam','Bob','Joe'] as $name)
{

   	try {
		$stmt->execute([$name]);
	} catch (PDOException $e) {
		if (1==$debug_mode) {
			echo "DataBase Error:<br>".$e->getMessage();
			echo "<pre></pre>$_cp_sql</pre>";
		}
		exit ("<h1>****Warning - processing stopped on database error****</h1>");
	} catch (Exception $e) {
		if (1==$mydebug) {
			echo "General Error:<br>".$e->getMessage();
		}	
		exit ("<h1>****Warning - processing stopped on general error****</h1>");
	}

}
nz_debug(myDB::lastInsertId());
//Expected output
//string(1) "3"

# Prepared statement insert one row
# Used when inserting variables defined earlier in program
$_cp_id=98;
$_cp_name="Fred";
$_cp_data = [
    'id' => $_cp_id,
    'name' => $_cp_name,
];
$_cp_mysql="INSERT INTO pdowrapper (id,name) VALUES (:id, :name)";
$stmt = myDB::prepare("$_cp_mysql");
   	try {
		$stmt->execute($_cp_data);
	} catch (PDOException $e) {
		if (1==$debug_mode) {
			echo "DataBase Error:<br>".$e->getMessage();
			echo "<pre></pre>$_cp_sql</pre>";
		}
		exit ("<h1>****Warning - processing stopped on database error****</h1>");
	} catch (Exception $e) {
		if (1==$mydebug) {
			echo "General Error:<br>".$e->getMessage();
		}	
		exit ("<h1>****Warning - processing stopped on general error****</h1>");
	}
nz_debug(myDB::lastInsertId());
//Expected output
//string(1) "98"

# Prepared statement insert one row
# Data directly defined
$_cp_data = [
    'id' => 99,
    'name' => 'Tim',
];
$_cp_mysql="INSERT INTO pdowrapper (id,name) VALUES (:id, :name)";
$stmt = myDB::prepare("$_cp_mysql");
   	try {
		$stmt->execute($_cp_data);
	} catch (PDOException $e) {
		if (1==$debug_mode) {
			echo "DataBase Error:<br>".$e->getMessage();
			echo "<pre></pre>$_cp_sql</pre>";
		}
		exit ("<h1>****Warning - processing stopped on database error****</h1>");
	} catch (Exception $e) {
		if (1==$mydebug) {
			echo "General Error:<br>".$e->getMessage();
		}	
		exit ("<h1>****Warning - processing stopped on general error****</h1>");
	}
nz_debug(myDB::lastInsertId());
//Expected output
//string(1) "99"


# Getting rows in a loop
$_cp_mysql="SELECT * FROM pdowrapper";
	try {
		$stmt = myDB::run("$_cp_mysql");
	} catch (PDOException $e) {
		if (1==$debug_mode) {
			echo "DataBase Error:<br>".$e->getMessage();
			echo "<pre></pre>$_cp_sql</pre>";
		}
		exit ("<h1>****Warning - processing stopped on database error****</h1>");
	} catch (Exception $e) {
		if (1==$mydebug) {
			echo "General Error:<br>".$e->getMessage();
		}	
		exit ("<h1>****Warning - processing stopped on general error****</h1>");
	}
while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
{
    echo "<p>";
    echo $row['name'];
    echo "</p>";    
}
//Expected output
/*
Sam
Bob
Joe
Fred
Tim
*/

# Getting one row
$id  = 1;
$_cp_mysql="SELECT * FROM pdowrapper WHERE id=?";

	try {
		$row = myDB::run("$_cp_mysql", [$id])->fetch();
	} catch (PDOException $e) {
		if (1==$debug_mode) {
			echo "DataBase Error:<br>".$e->getMessage();
			echo "<pre></pre>$_cp_sql</pre>";
		}
		exit ("<h1>****Warning - processing stopped on database error****</h1>");
	} catch (Exception $e) {
		if (1==$mydebug) {
			echo "General Error:<br>".$e->getMessage();
		}	
		exit ("<h1>****Warning - processing stopped on general error****</h1>");
	}
nz_debug($row);
//Expected output
/*
array (
  'id' => '1',
  'name' => 'Sam',
)
*/

# Getting single field value
$id=1;
$_cp_mysql="SELECT name FROM pdowrapper WHERE id=?";
	try {
		$name = myDB::run("$_cp_mysql", [$id])->fetchColumn();
	} catch (PDOException $e) {
		if (1==$debug_mode) {
			echo "DataBase Error:<br>".$e->getMessage();
			echo "<pre></pre>$_cp_sql</pre>";
		}
		exit ("<h1>****Warning - processing stopped on database error****</h1>");
	} catch (Exception $e) {
		if (1==$mydebug) {
			echo "General Error:<br>".$e->getMessage();
		}	
		exit ("<h1>****Warning - processing stopped on general error****</h1>");
	}
nz_debug($name);
//Expected output
//string(3) "Sam"

# Getting array of rows
$_cp_mysql="SELECT name, id FROM pdowrapper";
	try {
		$all = myDB::run("$_cp_mysql")->fetchAll(PDO::FETCH_KEY_PAIR);
	} catch (PDOException $e) {
		if (1==$debug_mode) {
			echo "DataBase Error:<br>".$e->getMessage();
			echo "<pre></pre>$_cp_sql</pre>";
		}
		exit ("<h1>****Warning - processing stopped on database error****</h1>");
	} catch (Exception $e) {
		if (1==$mydebug) {
			echo "General Error:<br>".$e->getMessage();
		}	
		exit ("<h1>****Warning - processing stopped on general error****</h1>");
	}

nz_debug($all);
//Expected output
/*
array (
  'Sam' => '1',
  'Bob' => '2',
  'Joe' => '3',
)
*/

# Getting array of rows and making into table
$_cp_mysql="SELECT * from pdowrapper";
	try {
		$all = myDB::run("$_cp_mysql")->fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		if (1==$debug_mode) {
			echo "DataBase Error:<br>".$e->getMessage();
			echo "<pre></pre>$_cp_sql</pre>";
		}
		exit ("<h1>****Warning - processing stopped on database error****</h1>");
	} catch (Exception $e) {
		if (1==$mydebug) {
			echo "General Error:<br>".$e->getMessage();
		}	
		exit ("<h1>****Warning - processing stopped on general error****</h1>");
	}
echo build_table($all);


# Update
$new = 'Sue';
$_cp_mysql="UPDATE pdowrapper SET name=? WHERE id=?";
	try {
		$stmt = myDB::run("$_cp_mysql", [$new, $id]);
	} catch (PDOException $e) {
		if (1==$debug_mode) {
			echo "DataBase Error:<br>".$e->getMessage();
			echo "<pre></pre>$_cp_sql</pre>";
		}
		exit ("<h1>****Warning - processing stopped on database error****</h1>");
	} catch (Exception $e) {
		if (1==$mydebug) {
			echo "General Error:<br>".$e->getMessage();
		}	
		exit ("<h1>****Warning - processing stopped on general error****</h1>");
	}
nz_debug($stmt->rowCount());
//Expected output
//int(1)


?>
