<?php
/*
***********************************************************************************
DaDaBIK (DaDaBIK is a DataBase Interfaces Kreator) https://dadabik.com/
Copyright (C) 2001-2020 Eugenio Tacchini

This program is distributed "as is" and WITHOUT ANY WARRANTY, either expressed or implied, without even the implied warranties of merchantability or fitness for a particular purpose.

This program is distributed under the terms of the DaDaBIK license, which is included in this package (see dadabik_license.txt). For all the details see dadabik_license.txt.

If you are unsure about what you are allowed to do with this license, feel free to contact info@dadabik.com
***********************************************************************************
*/
?>
<?php

/* IF YOU ARE EDITING A PREPACKAGED APP, THE FILE YOU WANT TO MODIFY IS custom_functions_prepackaged_app.php */

/*

How to use $_POST $_GET and $_COOKIE variables. DaDaBIK automatically escapes all the values coming from $_POST $_GET and $_COOKIE (e.g. on MySQL tes't will become tes\'t), pleaes consider that when you use those values. For databaes queries, in particular, if you use prepared statements (and you should), you should unescape those values (using the DaDaBIK function unescape() ) before using them in your bind_param_db functinos.
Don't use unescape() on values that have not been escaped.

When you define a custom function, please don't add spaces or tabs in front of the definition,

e.g.
function print_customer_details(

is OK, but

    function print_customer_details(
    
is not OK, the form configurator parser can't read it correctly.

*/

// CUSTOM BUTTONS
$cnt = 0;
/* add custom buttons here (see documentation for details) */

// Starting from DaDaBIK 10, you can write the code related to your custom buttons, custom functions, hooks etc in separated files. For custom buttons, use the file custom_functions/custom_buttons.php. If you prefer the old approach, however, you can still write all the code in one file (custom_functions.php).
require('include/custom_functions/custom_buttons.php');

// CALCULATED FIELDS FUNCTIONS

/*

Each calculated field function receives in input an associative array $parameters_ar containing all the values coming from the INSERT / UPDATE form.
Please note that:
- select_multiple and file fields don't work correctly as input for a calculated field function
- for select_single_radio fields, if the user didn't select any option, the corresponding key in $parameters_ar won't be set

Each calculated field function must return the value to assign to the calculated field and it is automatically executed every time an insert or update operation is executed and every time one of the input value is modified in the INSERT / UPDATE form.

In this simple example (dadabik_calculate_total_price_product) we have a field total_price_product whose value is calculated as the sum of price_product + tax_product.

Please note that we check if the input values are NULL or empty ( '' ) before performing the addition. This is particularly useful in the insert form: when you open an insert form, total_price_product is immediately calculated according to the values of price_product and tax_product you have in the form, but if you have just opened the insert form, they would be empty.

Please also note that if you need to use a field value, the correspondent field cannot be disabled in the edit form

Using as input a field from the form that is also a calculated field can lead to unexpected results.


*/

/*
function dadabik_calculate_total_price_product($parameters_ar){
	
	if ( $parameters_ar['price_product'] !== '' && !is_null($parameters_ar['price_product']) && $parameters_ar['tax_product'] !== '' && !is_null($parameters_ar['tax_product'])){
		return ($parameters_ar['price_product'] + $parameters_ar['tax_product']);
	}
	else{
		return NULL;
	}
	
}
*/

// Starting from DaDaBIK 10, you can write the code related to your custom buttons, custom functions, hooks etc in separated files. For calculated fields functions, use the file custom_functions/calculated_fields_functions.php. If you prefer the old approach, however, you can still write all the code in one file (custom_functions.php).
require('include/custom_functions/calculated_fields_functions.php');


// CUSTOM VALIDATION FUNCTIONS


// each custom validation function receives in input an array $parameters_ar containing all the values coming from the form posted, including the fields values
// each custom validation function must return the boolean true or the boolean false.
// in this simple exeample we just check if the field quantity_product value is between 0 and 30
// you also have to specify the error message in your language file (see /include/languages), using as a key nameofthefunction_not_valid; in this example, you would add a sentence to the $normal_messages_ar array having key = dadabik_validate_quantity_product_not_valid 
// why you have in inputs all the $_POST values? Because the validation rule of a field could depend on the value of the others
// please note that only NOT NULL and NOT empty field values are validated, if you are looking for a method to check requiredness, you can simply set a field as required in the form configurator.   
// please also note that if you need to use a field value, the correspondent field cannot be disabled in the edit form and cannot be a calculated field (which is always disabled)

/*
function dadabik_validate_quantity_product($parameters_ar){
	
	
	if ($parameters_ar['quantity_product'] >= 0 && $parameters_ar['quantity_product'] <= 30){
		return true;
	}
	else{
		return false;
	}
	
}
*/

// Starting from DaDaBIK 10, you can write the code related to your custom buttons, custom functions, hooks etc in separated files. For custom validation functions, use the file custom_functions/custom_validation_functions.php. If you prefer the old approach, however, you can still write all the code in one file (custom_functions.php).
require('include/custom_functions/custom_validation_functions.php');


// CUSTOM FORMATTING FUNCTIONS


// each custom formatting function receives in input the value to display $value and the value of the unique field of the record processed ($id)
// each custom validation function must return the formatted value
// in this simple exeample we just format the field last_name_customer to be displayed in red
// if the value is a select_single/select_multiple fields having more than one linked fields, $value is an array, where $value[0] is the value of the first linked field, $value[1] the value of the second and so on 
/*
function dadabik_format_last_name_customer($value, $id){

	return '<span style="color:red">'.$value.'</span>';
	
	
}
*/

// Starting from DaDaBIK 10, you can write the code related to your custom buttons, custom functions, hooks etc in separated files. For custom formatting functions, use the file custom_functions/custom_formatting_functions.php. If you prefer the old approach, however, you can still write all the code in one file (custom_functions.php).
require('include/custom_functions/custom_formatting_functions.php');


// CUSTOM DISPLAY / REQUIRED FUNCTIONS 

/* 
each custom required function receives in input an array $parameters_ar containing all the values coming from the current form, including the fields values.
Please note that:
- select_multiple and file fields don't work correctly as input for a calculated field function
- for select_single_radio fields, if the user didn't select any option, the corresponding key in $parameters_ar won't be set
- if you need to use a field value, the correspondent field cannot be disabled in the edit form and cannot be a calculated field (which is always disabled)

each custom required function must return an associative array with two elements: 'required' and 'show' and for each element the boolean value true or the boolean value false.

in this simple exeample, we have created a function for the field "state", which is display and required only if the country is "USA".

About the display (true/false) value: consider that this function just hides/displays the field in the form, but the PERMISSIONS tab in the admin section is still the place where you decide if a field will be considered or not for INSERT and UPDATE operations. For example, in this "state" case, for the field state you should set YES for both CREATE and EDIT so that insert (or update) operations will use the "state" field value (yes, they will use it even if the field is hidden, but this shouldn't be a problem, if the user didn't fill it, it will be empty or null; it can be a problem if the user fills a field with a value and then makes the field disappear: even if it is not in the form, the field will pass its value to the insert or update process).  

One more thing to consider: if the field is a calculated field, the "required" element will not be taken into consideration, the "show" elment will be used to show/hide the field in the form, but even if the field is hidden, its value will be computed anyway during insert and update operations.

To guarantee backward compatibility (before v. 10 this function only controlled requiredness), this function can also returns, instead of an associative array, just a boolean value (true or false): in this case, such value controls the requiredness of a field. 
*/

/*
function dadabik_display_required_state_customer($parameters_ar){
	
	
	if ($parameters_ar['country_customer'] === 'USA'){
		$a['show'] = true;
		$a['required'] = true;
	}
	else{
		$a['show'] = false;
		$a['required'] = false;
	}
	
	return $a;
	
}
*/

// Starting from DaDaBIK 10, you can write the code related to your custom buttons, custom functions, hooks etc in separated files. For custom required functions, use the file custom_functions/custom_required_functions.php. If you prefer the old approach, however, you can still write all the code in one file (custom_functions.php).
require('include/custom_functions/custom_required_functions.php');


// CUSTOM DEFAULT VALUE FUNCTIONS


// A custom default value function doesn't receive anything in input
// each default value function must return the default value to set
// in this simple exeample we provide, for the field date_order, as a default, the current date
/*
function dadabik_set_default_date_order(){

	return date('Y-m-d');
	
	
}
*/

// Starting from DaDaBIK 10, you can write the code related to your custom buttons, custom functions, hooks etc in separated files. For custom default value functions, use the file custom_functions/custom_default_value_functions.php. If you prefer the old approach, however, you can still write all the code in one file (custom_functions.php).
require('include/custom_functions/custom_default_value_functions.php');


// HOOKS (Operational hooks work with DaDaBIK Enterprise/Platinum only, layout hooks work with DaDaBIK Pro as well )
/*

OPERATIONAL HOOKS 

A DaDaBIK hook is a feature that allows you to write some PHP code to be called under certain circumstances.
DaBIK currently supports after insert, after update, after delete, before insert, before update and before delete hooks. In the example below we define an after insert hook on the table accounts, setting the custom function that DaDaBIK has to execute: dadabik_send_notice_after_accounts_insert. We then write the actual code of the function dadabik_send_notice_after_accounts_insert, that retrieves the name of the account just inserted and send it via email to a specific address.
Please note that:

- you can use any name for hooks functions, but the name MUST start with dadabik_ 

- if you need to execute operations on the database, you have to add the global $conn; code line (as in the example)

- the after insert, update and delete hook functions receive as parameter the value of the primary key field of the record just inserted (if it is an autoincrement field), updated or deleted, the before delete hook functions receive as parameter the value of the primary key field of the record the user wants to delete, the before update hook function receives as input the value of the PK of the field in update and (second parameter of the function) an associative array containing all the values the user filled in the update form (where the array element's key is the name of the field), the before insert hook function receives as input an associative array containing all the values the user filled in the insert form (where the array element's key is the name of the field).

- the before insert hook doesn't work when a record is automatically inserted using the "other..." option from a dropdown menu (select_single field)

- the delete hooks don't work with "delete all" operation.

- for the before delete hooks, your function can optionally returns 'dont_delete' to prevent the deletion of the record (the execution of an after delete hook, if any, will also be prevented): in this case you also need to add a custom error messages in your language file, if the name of the funciton is, for example, dadabik_check_delete_customer, you have to add this entry in your language file
$error_messages_ar['dadabik_check_delete_customer_dont_delete'] = '.... write your error message here ....';

- You don't have to worry about transactions in your code: the after insert, update and delete hook functions are executed inside a transaction that starts before the insert, update and delete operations are executed and ends after theh hook functions execution; the before update hook is also inside the same transaction which starts before its execution.


 
*/
/*
$hooks['accounts']['insert']['after'] = 'dadabik_send_notice_after_accounts_insert';

function dadabik_send_notice_after_accounts_insert($id_account)
{
    global $conn;

    // get the name from the ID
    $sql = "SELECT name_account FROM accounts WHERE id_account = :id_account";
    
    $res_prepare = prepare_db($conn, $sql);

    $res_bind = bind_param_db($res_prepare, ':id_account', $id_account);
    
    $res = execute_prepared_db($res_prepare,0);
    
    $row = fetch_row_db($res_prepare)
    
    mail ('john@mysite.com', 'New account inserted', 'A new account ('.$row['name_account'].') has been added.');
    
}
*/

/*

An example of $function_user_values (see $function_user_values parameter in config.php)
function dadabik_capitalize($value)
{
    return mb_strtoupper($value);
}

*/

// Starting from DaDaBIK 10, you can write the code related to your custom buttons, custom functions, hooks etc in separated files. For operational hooks, use the file custom_functions/operational_hooks.php. If you prefer the old approach, however, you can still write all the code in one file (custom_functions.php).
require('include/custom_functions/operational_hooks.php');


/*
LAYOUT HOOKS 
For layout hooks, see the documentation
*/

// Starting from DaDaBIK 10, you can write the code related to your custom buttons, custom functions, hooks etc in separated files. For layout hooks, use the file custom_functions/layout_hooks.php. If you prefer the old approach, however, you can still write all the code in one file (custom_functions.php).
require('include/custom_functions/layout_hooks.php');


// CUSTOM FILTERS (ONLY WORK WITH DaDaBIK Enterprise/Platinum)

/*
For each table/view, you can define a function that returns a filter (in SQL jargon, a "where clause") to use for such table/view. The filter is applied IN ADDITION to any filter already applied on the table (for example is added to a search filter).

The name of the function must start with "dadabik_"

In the example below, for the table "customers", DaDBIK will  always only show customers having the field "approved_customer" = 1. The function can be very simple, like in the example, but IT can contain any PHP custom code and can execute queries as well.

Plese note that, when you compose your WHERE CLAUSE, you should follow the following rules:

- each field and table name must be surrounded by quotes; you can use the $quote variable, which automatically add the correct quote according to your DBMS

- you must escape the values you use in your query using the dadabik function escape(); however, if you use a value coming from POST, GET or COOKIE, the value is already escaped by DaDaBIK so you don't have to use escape()


*/


/*
Custom filter examples

$custom_filters['customers'] = 'dadabik_custom_filter_customers';

function dadabik_custom_filter_customers()
{
    global $quote;
    return $quote.'approved_customer'.$quote.' = 1';
}

function dadabik_custom_filter_customers()
{
    global $quote;
    
    $myvalue = .... some code to get the value.....
    
    return $quote.'approved_customer'.$quote.' = '.escape($myvalue);
}

function dadabik_custom_filter_customers()
{
    global $quote;
    
    return $quote.'approved_customer'.$quote.' = '.$_POST['myvalue'];
}

*/

// Starting from DaDaBIK 10, you can write the code related to your custom buttons, custom functions, hooks etc in separated files. For custom filter functions , use the file custom_functions/custom_filter_functions.php. If you prefer the old approach, however, you can still write all the code in one file (custom_functions.php).
require('include/custom_functions/custom_filter_functions.php');


// CUSTOM STARTUP FUNCTION
/* you can define a custom funciton to execute each time a DaDaBIK page is run */

/*
Example:

$custom_startup_function = 'dadabik_startup';

function dadabik_startup()
{
    ... my custom code here ....
}
*/





?>
