<?php
/*
***********************************************************************************
This program is distributed "as is" and WITHOUT ANY WARRANTY, either expressed or implied, without even the implied warranties of merchantability or fitness for a particular purpose.

This program is distributed under the terms of the DaDaBIK license, which is included in this package (see dadabik_license.txt). For all the details see dadabik_license.txt.

If you are unsure about what you are allowed to do with this license, feel free to contact info@dadabik.com
***********************************************************************************
*/
?>
<?php
// submit buttons
$submit_buttons_ar = array (
	"insert"    => "Insereaza o alta înregistrare",
	"quick_search"    => "Quick search", // to change
	"search/update/delete" => "Cauta/actualizeaza/sterge înregistrarea",
	"insert_short"    => "Insereaza",
	"search_short" => "Cauta",
	"advanced_search" => "Advanced Search", // to change
	"insert_anyway"    => "Insereaza totusi",
	"search"    => "Cauta înregistrarea",
	"update"    => "Salveaza",
	"ext_update"    => "Actualizeaza-ti profilul",
	"yes"    => "Da",
	"no"    => "Nu",
	"go_back" => "Mergi înapoi",
	"edit" => "Modifica",
	"delete" => "Sterge",
	"details" => "Detalii",
	"insert_as_new" => "Insert as new", // to change
	"multiple_inserts" => "Multiple inserts", // to change
	"change_table" => "Modifica tabelul"
);

// normal messages
$normal_messages_ar = array (
	"cant_edit_record_locked_by" => "You can't edit this record, the record is locked by user: ", // to change
	"lost_locked_not_safe_update" => "You dont't have or have lost your lock on this record, it is not safe to update, please start again the edit", // to change
	"insert_item" => "Insert item", // to change
	"show_all_records" => "Vizualizeaza toate înregistrarile",
	"show_records" => "Show items", // to change
	"ldap_user_dont_update" => "This is an imported LDAP user: his group is the only information which you should change, if needed.", // to change
	"remove_search_filter" => "remove search filter", // to change
	"logout" => "Logout",
	"top" => "Top",
	"last_search_results" => "Ultimele rezultate ale cautarii",
	"show_all" => "Vizualizeaza tot",
	"home" => "Home",
	"select_operator" => "Selecteaza operatorul:",
	"all_conditions_required" => "Toate conditiile cerute",
	"any_conditions_required" => "Cel putin o conditie",
	"all_contacts" => "Toate contactele",
	"removed" => "Sters",
	"please" => "",
	"and_check_form" => "si controleaza formularul.",
	"and_try_again" => "si încearca înca o data.",
	"none" => "nici unul",
	"are_you_sure" => "Esti sigur?",
	"delete_all" => "sterge tot",
	"really?" => "Sigur?",
	"delete_are_you_sure" => "Esti sigur ca vrei sa stergi?",
	"required_fields_missed" => "Nu ai completat câteva câmpuri obligatorii.",
	"alphabetic_not_valid" => "Ai inserat cifre în loc de litere.",
	"numeric_not_valid" => "Ai inserat litere în loc de cifre.",
	"email_not_valid" => "Ai inserat una sau mai multe adrese e-mail incorecte.",
	"timestamp_not_valid" => "The timestamp/s you have inserted is/are not valid.", // to change
	"url_not_valid" => "Ai inserat una sau mai multe adrese URL incorecte.",
	"phone_not_valid" => "Ai inserat unul sau mai multe numere de telefon incorecte.<br>Trebuie sa folosesti formatul \"+(codul national)(prefix)(numar)\" es.. +390523599318, 00390523599318, 0523599318.",
	"date_not_valid" => "Ai inserat una sau mai multe date incorecte.",
	"similar_records" => "Înregistrarile care urmeaza par asemanatoare cu cele pe care vrei sa le inserezi (I'll show max ".$number_duplicated_records." similar items, there could be more).<br>Cum vrei sa procedezi?", // to change
	"similar_records_short" => "Înregistrarile care urmeaza par asemanatoare cu cele pe care vrei sa le inserezi (I'll show max ".$number_duplicated_records." similar items, there could be more).", // to change
	"no_records_found" => "N-a fost gasita nici o înregistrare.",
	"records_found" => "Înregistrari gasite.",
	"number_records" => "Numarul înregistrarilor: ",
	"details_of_record" => "Detaliile înregistrarii",
	"details_of_record_just_inserted" => "Details of the record just inserted", // to change
	"edit_record" => "Modifica înregistrarea",
	"back_to_the_main_table" => "Back to the main table", // to change
	"previous" => "Precedenta",
	"next" => "Urmatoare",
	"edit_profile" => "Actualizeaza-ti informatiile cu privire la profil",
	"i_think_that" => "Cred ca",
	"is_similar_to" => "seamana cu",
	"page" => "Pagina",
	"of" => "din",
	"records_per_page" => "înregistrarile pe pagina",
	"day" => "ziua",
	"month" => "luna",
	"year" => "anul",
	"administration" => "Administrare",
	"create_update_internal_table" => "Creaza sau actualizeaza tabelul intern",
	"other...." => "alt...",
	"insert_record" => "Insereaza o înregistrare noua",
	"search_records" => "Cauta o înregistrare",
	"exactly" => "identica",
	"like" => "asemanatoare",
	"required_fields_red" => "Câmpurile obligatorii sunt rosii.",
	"insert_result" => "Rezultatul inserarii:",
	"record_inserted" => "Înregistrarea a fost inserata corect.",
	"update_result" => "Rezultatul actualizarii:",
	"record_updated" => "Înregistrarea a fost actualizata corect.",
	"profile_updated" => "Profilul a fost actualizat corect.",
	"delete_result" => "Rezultatul stergerii:",
	"record_deleted" => "Înregistrarea a fost stearsa corect.",
	"duplication_possible" => "Exista posibilitatea unei dublari",
	"fields_max_length" => "Ai atins lungimea maxima pentru acest câmp.",
	"current_upload" => "Fisierul actual",
	"delete" => "sterge",
	"total_records" => "Înregistrarile totale",
	"confirm_delete?" => "Confirmi stergerea?",
    "unselect_all" => "Unselect all", // to change
    "select_all" => "Select all", // to change
    "only_elements_this_page_selected_other_pages_kept" => "Only the elements of the current page will be selected. If you selected elements in other pages, such selection will be kept.", // to change
    "all_elements_will_be_unselected_also_other_pages" => "All the elements will be unselected, also elements selected in other pages.", // to change
    "delete_selected" => "Delete selected", // to change
	"is_equal" => "e identic cu",
	"is_different" => "is not equal to", // to change
	"is_not_null" => "is not null", // to change
	"is_not_empty" => "is not empty", // to change
	"contains" => "contine",
	"doesnt_contain" => "doesn't contain", // to change
	"starts_with" => "începe cu",
	"ends_with" => "se termina cu",
	"greater_than" => "mai mult decât",
	"less_than" => "mai putin decât",
    "greater_equal_than" => ">=",
    "less_equal_than" => "<=",
    "between" => "between", // to change
    "between_and" => "and", // to change, used for the between search operator: between .... AND .....
	"export_to_csv" => "Exporta ca CSV",
	"new_insert_executed" => "noua inserare a fost savârsita",
	"new_update_executed" => "noua actualizare a fost savârsita",
	"null" => "Null",
	"is_null" => "este NULL",
	"is_empty" => "este gol",
	"continue" => "continue", // to change
	'current_password' => 'current password', // to change
	'new_password' => 'new password', // to change
	'new_password_repeat' => 'new password (repeat)', // to change
	'password_changed' => 'the password has been changed', // to change
	'change_your_password' => 'change your password', // to change
	'your_info' => 'your info', // to change
	'sort_by' => 'sort by', // to change
	'sort' => 'sort', // to change
	'pie_chart' => 'Pie chart', // to change
'bar_chart' => 'Bar chart', // to change
'line_chart' => 'Line chart', // to change
'doughnut_chart' => 'Doughnut chart', // to change
'show_report' => 'Show chart', // to change
'show_labels' => 'Show labels', // to change
'show_legend' => 'Show legend', // to change
'group_data_by' => 'Aggregate data by', // to change
'x_axis' => 'X-axis', // to change
'y_axis' => 'Y-axis', // to change
'show' => 'show', // to change
'percentage' => '%', // to change
'count' => 'count', // to change
'sum' => 'sum', // to change
'average' => 'average', // to change
'min' => 'min', // to change
'max' => 'max', // to change
'variance' => 'variance', // to change
'standard_deviation' => 'standard deviation', // to change

'simple_report' => 'simple report', // to change
'advanced_sql_report' => 'advanced SQL report', // to change
'type_your_custom_sql_query_here' => 'Type your custom SQL query here: ', // to change
'current_search_filter_is_not_used' => '(The current search filter won\'t be used)', // to change
'advanced_sql_reports_are_disabled' => 'Advanced SQL reports are disabled', // to change
'advanced_sql_report_instructions_first_part' => 'You can write a custom SQL select query, e.g. suppose you have a <b>customers</b> table having an <b>age_customer</b> field, you can show the age composition of your customers using the following query:', // to change
'advanced_sql_report_instructions_query_part' => '<br/><br/><div class=&quot;code_snippet&quot; // to change>SELECT age_customer, count(*) FROM customers GROUP BY age_customer</div><br/><br/>', // to change // DON'T TRANSLATE, LEAVE IT UNCHANGED
'advanced_sql_report_instructions_second_part' => 'Remember, the first field you select will be used for the <b>X axis</b> of the graph, the second field for the <b>Y axis</b>.<br/><br/>Read the documentation for further examples.', // to change
'generate_report' => 'Generate chart', // to change
'use_semicolon_forbidden_omit_trailing_semicolmn' => 'The use of semicolon (;) is not allowed for security reason, you can omit the final semicolon.', // to change
'sql_report_must_start_with_select' => 'The custom SQL report must start with "SELECT "', // to change
'show_embed_code' => 'Show embed code', // to change
'embed_code_instructions' => 'You can copy the code below and paste it in a custom page to embed this chart or grid report; by embedding several chart/grid reports in a page you can easily create a dashboard. Please note that, if the report has been generated after a search, the search filter is not saved in the embed code. If you need to embed a report based on a stable search filter, the best way is to create a VIEW and generate the report starting from it. Also consider that pagination is not available in an embedded grid report, only X records will be displayed, where X is your current <i>items per page</i> setting.', // to change
'produce_pdf' => 'Produce PDF', // to change
'choose_pdf_template' => 'Choose PDF template', // to change
'no_pdf_template' => 'Standard Template', // to change
'show_revisions' => 'Show revisions', // to change,
'hide_revisions' => 'Hide revisions', // to change,
'record_revisions' => 'Record revisions', // to change,
'revisions' => 'Revisions', // to change,
'for_this_table_revisions_not_enabled_no_revisions' => 'For this table, revisions are not enabled or you haven\'t revisions yet.', // to change,
'generate_pivot' => 'Generate pivot', // to change
'you_might_have_additional_rows_admin_set_to' => 'You might have additional rows but the admin set the maximum rows to ', // to change
'add_column' => 'add column', // add column in the pivot report // to change
'remove_this_column' => 'remove this column', // remove column in the pivot report // to change
'advanced_sql_report_instructions_pivot_part' => 'For Pivot Table generation, in addtion, you can use alias (to specify labels) and you can use more than one aggreagete functions, for example: SELECT brand AS ProductBrand, count(*) As Number, AVG(price_product) AS AvgPrice FROM products GROUP BY brand', // to change
"record_inserted_close_window" => "The item has been correctly inserted, you can <a href='#' onclick='window.close();return false;'>close</a> this window.", // to change

"import" => "Import", // to change
'file_type' => 'File type', // to change
'delimiter' => 'Delimiter', // to change
'values_enclosed_by' => 'Values optionally enclosed by', // to change
'load_file' => 'Upload file', // to change
'error_no_file_selected' => 'Error, you have not selected a file to upload.', // to change
'values_enclosed_cannot_be_blank' => 'The parameter "Values optionally enclosed by" cannot be blank, you can leave the default one even if you do not use any enclose character.', // to change
'error_file_type_not_supported_or_different' => 'Error, this file type is not supported or it is not the one you selected in the previous page', // to change
'error_too_much_time_passed' => 'Error, too much time has passed.', // to change
'processing_row' => 'Processing row', // to change
'new_elements_will_be_inserted_to_proceed_click_continue' => 'new elements will be added. To proceed, click "Continue" at the end of the page', // this message will be used with a number, e.g. "5 new elements will be added ... ", // to change
'following_as_example_20_rows' => 'The following are only the first 20 rows of your file.', // to change
'possible_duplication_continue_to_update' => 'Possible duplication, some elements have the same values on the unique fields (the duplication could also be within your file). Here are the duplicated elements. At the moment no elements have been inserted or updated. If you click "Continue" at the end of the page, for these elements, I will update the records with teh new information provided in the file. ', // to change
'elements_have_been_inserted_updated' => 'elements have been inserted/updated.', // this message will be used with a number, e.g. "5 elements have been inserted/updated" // to change
'to_verify_elements_click_continue_filter_set' => 'To verify the elements inserted/updated click on "Continue". I have set a search filter that allows you to see only the inserted/updated elements (you might only see some of them if the administrator has set additional filters).', // to change
'no_element_has_been_inserted' => 'No element has been inserted.', // to change
'error_no_sheet_with_name'=> 'Error, no sheet with name:', // to change
'elements_results_last_import' => 'The elements you see are the result of the last import (you might only see some of them if the administrator has set additional filters). To see all the elements click on "Remove search filter"', // to change
'csv_file_must_be_utf8_encoded' => 'The CSV file must be UTF-8 encoded.', // to change
'hide_show_quick_filters' => 'Hide/Show quick filters', // to change,
'show_search_url' => 'Show search URL', // to change,
'search_url_instructions' => 'This URL executes the same search you made, also adding the sort criteria you applied (if any).', // to change,
"double_click_to_edit" => 'Double click to edit', // to change
'it_seems_you_uploaded_other_files_cancelled' => ' It seems you uploaded some files in another form but you finally did not complete the save/insert process. Those uplaods have been cancelled.', // to change,
'number_uploaded_files' => 'Number of uploaded files: ', // to change,
'file_uploaded_file_will_replace' => 'File uploaded! The file will replace the current one (if any) after saving the form.',// to change
'generic_upload_error' => 'Generic upload error! ', // to change
'collapse_sidebar' => 'Collapse sidebar', // to change

);
$normal_messages_ar['months_short'][1] = 'Jan';
$normal_messages_ar['months_short'][2] = 'Feb';
$normal_messages_ar['months_short'][3] = 'Mar';
$normal_messages_ar['months_short'][4] = 'Apr';
$normal_messages_ar['months_short'][5] = 'May';
$normal_messages_ar['months_short'][6] = 'Jun';
$normal_messages_ar['months_short'][7] = 'Jul';
$normal_messages_ar['months_short'][8] = 'Aug';
$normal_messages_ar['months_short'][9] = 'Sep';
$normal_messages_ar['months_short'][10] = 'Oct';
$normal_messages_ar['months_short'][11] = 'Nov';
$normal_messages_ar['months_short'][12] = 'Dec';

// please don't change the indexes (1,2,3,...) if you want your week to start on Sunday, set $weeks_start_on_sunday = 1 in config.php
$normal_messages_ar['days_short'][1] = 'Mon';
$normal_messages_ar['days_short'][2] = 'Tue';
$normal_messages_ar['days_short'][3] = 'Wed';
$normal_messages_ar['days_short'][4] = 'Thu';
$normal_messages_ar['days_short'][5] = 'Fri';
$normal_messages_ar['days_short'][6] = 'Sat';
$normal_messages_ar['days_short'][7] = 'Sun';
// error messages
$error_messages_ar = array (
	"int_db_empty" => "Eroare, baza de date interna e goala.",
	"get" => "Eroare în variabile get.",
	"no_functions" => "Eroare, nici o functie n-a fost selectata<br>Întoarce-te la homepage.",
	"no_unique_key" => "Eroare, nici o cheie unica n-a fost selectata în tabel.",
	"upload_error" => "În timpul de încarcare al fisierului s-a produs o eroare.",
	"no_authorization_update" => "Nu ai autorizatia sa modifici aceasta înregistrare.",
	"no_authorization_delete" => "Nu ai autorizatia sa stergi aceasta înregistrare.",
	"no_authorization_view" => "Nu ai autorizatia sa vizualizezi aceasta înregistrare.",
	"deleted_only_authorizated_records" => "Numai înregistrarile pentru care ai o autorizatie au fost sterse.",
	"record_from_which_you_come_no_longer_exists" => "Înregistrarea de care ati venit nu mai exista.",
	"date_not_representable" => "A date value in this record can't be represented with DaDaBIK day-month-year listboxes, the value is: ", // to change
	"this_record_is_the_last_one" => "Aceasta înregistrare e ultima.",
	"this_record_is_the_first_one" => "Aceasta înregistrare e cea dintâi.",
	"current_password_wrong" => 'the current password is wrong', // to change
	"passwords_are_different" => 'the two passwords are different', // to change
	"new_password_must_be_different_old" => 'the new password must be different from the current one', // to change
	"new_password_is_empty" => 'the new password is empty', // to change,
	"you_cant_live_edit_click_edit" => 'You can\'t live edit this field, please click on the edit icon on your left to edit the entire record.', // to change
	"you_dont_have_enough_permissions_to_edit_field" => 'You don\'t have enough permissions to edit this field.' // to change
	);

//login messages
$login_messages_ar = array(
	"username" => "username",
	"password" => "parola",
	"please_authenticate" => "Trebuie sa te identifici pentru a proceda",
	"login" => "login",
	"username_password_are_required" => "Puneti username si parola",
	"pwd_gen_link" => "Create password",
	"incorrect_login" => "Numele sau parola sunt gresite",
	"pwd_explain_text" =>"Insereaza un cuvânt ca sa fie folosit drept parola a acestui utilizator si apasa <b>Cripteaza!</b> ca sa fie criptat.",
	"pwd_explain_text_2" =>"Apasa <b>Înregistreaza</b> ca sa fie scris în formularul urmator",
	"pwd_suggest_email_sending"=>"daca vrei poti sa-ti trimiti un mesaj ca sa-ti amintesti parola fara criptare",
	"pwd_send_link_text" =>"Trimite!",
	"pwd_encrypt_button_text" => "Cripteaza!",
	"pwd_register_button_text" => "Înregistreaza parola si închide fereastra",
	"too_many_failed_login_account_blocked" => "Too many failed attempts, your account has been blocked.", // to change
	"warning_you_still_have" => "Warning, you still have just ", // to change
	"attempts_before_account_blocking" => " attempts before your account is blocked.", // to change
	"verification_code" => "Verification code", // to change
	"verification_code_is_required" => "Verification code is required", // to change
	"incorrect_verification_code" => "The verification code is not correct", // to change
	"enable_two-factor_authentication" => "Enable Two-Factor Authentication", // to change
	"two-factor_authentication" => "Two-Factor Authentication", // to change
);

// to change, all the messages below

// Link "Register" in the login form
$login_messages_ar['register'] = 'Register';

// Registration form messages
$login_messages_ar['create_your_account'] = 'Create your account';
$login_messages_ar['email'] = 'email';
$login_messages_ar['first_name'] = 'first name';
$login_messages_ar['last_name'] = 'last name';
$login_messages_ar['registration_form_checkbox_1'] = 'Accept <a href="example_terms.html" target="_blank">terms and conditions</a>'; // to change
$login_messages_ar['registration_form_checkbox_2'] = 'Accept <a href="example_terms.html" target="_blank">terms and conditions</a>'; // to change
$login_messages_ar['registration_form_checkbox_3'] = 'Accept <a href="example_terms.html" target="_blank">terms and conditions</a>'; // to change

// form submit buttons
$login_messages_ar['submit_register_new_account'] = 'Submit and register a new account';
$login_messages_ar['back_to_login'] = 'Back to login';

// registration creation and confirmation messages and errors
$login_messages_ar['account_created_please_confirm_via_email'] = 'Account created, you will receive a confirmation email containing an activation link. Please click on the link to activate your account.';
$login_messages_ar['email_confirmed_login'] = 'Your account has been activated, you can now login: ';
$login_messages_ar['account_created_login'] = 'Your account has been created, you can now login: ';
$login_messages_ar['confirmation_link_expired_resent'] = 'The confirmation link has expired, a new link has been sent to your email address.';
$login_messages_ar['confirmation_link_not_correct_account_not_activated'] = 'This confirmation link is not correct, your account cannot be activated.';
$login_messages_ar['your_email_not_confirmed_yet'] = 'Your email has not been confirmed yet.';
$login_messages_ar['email_already_in_use'] = 'This email is already in use.'; // to change
$login_messages_ar['username_already_in_use'] = 'This username is already in use.'; // to change
$login_messages_ar['registration_email_subject'] = "Please confirm your registration"; // to change
$login_messages_ar['registration_email_content'] = "Hello,\nsomeone (hopefully you) has registered an account at ".$site_url_to_display.". To complete your registration click on this link within 24h:"; // to change

// Link "Forgotten password" in the login form
$login_messages_ar['forgotten_passowrd'] = 'Forgotten password';

// forgotten password form
$login_messages_ar['enter_email_will_get_temporary_password'] = 'Enter your email, you will receive your username and a temporary new password';
// form submit button
$login_messages_ar['submit'] = 'Submit';

// forgotten password confirmation messages and email
$login_messages_ar['if_email_is_user_temporary_password_sent'] = 'If this email address corresponds to an existing user, you will receive a message with a temporary password.';
// email subject
$login_messages_ar['your_temporary_password'] = 'Your temporary password';
// email body
$login_messages_ar['temporary_password_email_content_part_1'] = "Someone (hopefully you) has requested a new password to access ".$site_url_to_display."\n\nIf YOU requested the new password, everything is fine, here is your new temporary password (valid for five minutes only). Please note that email is not a secure communication channel for passwords so immediately change your main password after logging-in and never use - as your main password - the  temporary passwords we sent.";
$login_messages_ar['temporary_password_email_content_part_2'] = "If you can't access your account using this temporary password we sent, it means someone else accessed your account first, please contact your system administrator.\n\nIf you did not request the new password, it means someone else might be trying to access your account: please login as soon as possible using your OLD password (this make the temporary password invalid) and contact your system administrator. If you can't login using your old password, it means that someone else probably already accessed your account using the new temporary password, please contact your system administrator.";

$login_messages_ar['intro_2fa_secret_page'] = '<h2>Important: This page is displayed only once for security reasons.</h2><p>Please complete the setup instructions before leaving the page, as you will not be able to access this information again.</p><p><b>Download an Authentication App:</b> Visit your app store (Google Play/App Store) and download an authentication app, such as Google Authenticator or Authy.<br><br><b>Scan the QR Code:</b> Use the app to scan the QR code displayed below. This will link your account to the authentication app.<br><br><b>Future Logins:</b> The next time you log in, you will be prompted to enter a verification code generated by your authentication app.</p>'; // to change
?>
