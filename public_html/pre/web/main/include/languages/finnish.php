<?php
/*
***********************************************************************************
This program is distributed "as is" and WITHOUT ANY WARRANTY, either expressed or implied, without even the implied warranties of merchantability or fitness for a particular purpose.

This program is distributed under the terms of the DaDaBIK license, which is included in this package (see dadabik_license.txt). For all the details see dadabik_license.txt.

If you are unsure about what you are allowed to do with this license, feel free to contact eugenio.tacchini@gmail.com
***********************************************************************************
*/
?>
<?php
// submit buttons
$submit_buttons_ar = array (
	"insert"    => "Uuden tietueen lisäys",
	"quick_search"    => "Pikahaku",
	"search/update/delete" => "Etsi/päivitä/poista tietueita",
	"insert_short"    => "Lisää",
	"search_short" => "Etsi",
	"advanced_search" => "Advanced Search", // to change
	"insert_anyway"    => "Lisää kaikesta huolimatta",
	"search"    => "Etsi tietuetta",
	"update"    => "Tallenna",
	"ext_update"    => "Päivitä käyttäjätietosi",
	"yes"    => "Kyllä",
	"no"    => "Ei",
	"go_back" => "Palaa takaisin",
	"edit" => "Muokkaa",
	"delete" => "Poista",
	"details" => "Tiedot",
	"insert_as_new" => "Insert as new", // to change
	"multiple_inserts" => "Multiple inserts", // to change
	"change_table" => "Vaihda taulua"
);

// normal messages
$normal_messages_ar = array (
	"cant_edit_record_locked_by" => "Et voi muokata tietuetta, koska sen on lukinnut käyttäjä: ",
	"lost_locked_not_safe_update" => "Tietue on lukitsematta tai lukitus on poistunut. Päivitys ei ole turvallista. Ole hyvä ja aloita uudelleen.",
	"insert_item" => "Lisää kohde",
	"show_all_records" => "Näytä kaikki tietueet",
	"show_records" => "Näytä tietueet",
	"ldap_user_dont_update" => "Tämä on tuotu LDAP-käyttäjä: hänen ryhmänsä on ainoa tieto, joka mahdollisesti kaipaa päivittämistä.",
	"remove_search_filter" => "tyhjennä hakuehdot",
	"logout" => "Kirjaudu ulos",
	"top" => "Ylös",
	"last_search_results" => "Viimeisimmän haun tulokset",
	"show_all" => "Näytä kaikki",
	"home" => "Aloitus",
	"select_operator" => "Valitse vertailutapa:",
	"all_conditions_required" => "Kaikkien ehtojen on täytyttävä",
	"any_conditions_required" => "Vähintään yhden ehdon on täytyttävä",
	"all_contacts" => "Kaikki yhteystiedot",
	"removed" => "poistettu",
	"please" => "Ole hyvä",
	"and_check_form" => "ja tarkista lomake.",
	"and_try_again" => "ja yritä uudelleen.",
	"none" => "ei lainkaan",
	"are_you_sure" => "Oletko varma?",
	"delete_all" => "poista kaikki",
	"really?" => "Todellako?",
	"delete_are_you_sure" => "Olet poistamassa oheista tietuetta, oletko aivan varma?",
	"required_fields_missed" => "Et ole täyttänyt kaikkia vaadittuja tietoja.",
	"alphabetic_not_valid" => "Sopimattomia merkkejä kentässä, johon hyväksytään vain aakkosia (a-ö).",
	"numeric_not_valid" => "Sopimattomia merkkejä kentässä, johon hyväksytään vain numeroita (0-9).",
	"email_not_valid" => "Antamasi sähköpostiosoite ei ole kelvollinen.",
	"timestamp_not_valid" => "Lisäämäsi aika/ajat eivät ole kelvollisia.",
	"url_not_valid" => "Antamasi url (www-osoite) ei ole kelvollinen.",
	"phone_not_valid" => "Antamasi puhelinnumero ei ole kelvollinen.<br>Käytä muotoa \"+(maatunnus)(aluetunnus)(numero)\" esim. +358401235678, 00358401235678, 8401235678.",
	"date_not_valid" => "Antamasi päivämäärä ei ole kelvollinen.",
	"similar_records" => "Oheiset tietueet ovat yhteneväisiä antamiesi tietojen kanssa  (I'll show max ".$number_duplicated_records." similar items, there could be more).<br>Mitä haluat tehdä?", // to change
	"similar_records_short" => "Oheiset tietueet ovat yhteneväisiä antamiesi tietojen kanssa  (I'll show max ".$number_duplicated_records." similar items, there could be more).", // to change
	"no_records_found" => "Yhtään tietuetta ei löytynyt.",
	"records_found" => "tietuetta löytyi",
	"number_records" => "Tietueiden lukumäärä: ",
	"details_of_record" => "Tietueen tiedot",
	"details_of_record_just_inserted" => "Viimeksi lisätyn tietueen tiedot",
	"edit_record" => "Muokkaa tietuetta",
	"back_to_the_main_table" => "Takaisin päätauluun",
	"previous" => "Edellinen",
	"next" => "Seuraava",
	"edit_profile" => "Päivitä käyttäjätietosi",
	"i_think_that" => "Oletan, että ",
	"is_similar_to" => " on samanlainen kuin ",
	"page" => "Sivu ",
	"of" => " / ",
	"records_per_page" => "tietuetta sivulla",
	"day" => "Päivä",
	"month" => "Kuukausi",
	"year" => "Vuosi",
	"administration" => "Ylläpito",
	"create_update_internal_table" => "Luo tai päivitä sisäinen taulu",
	"other...." => "Muu, mikä? ...",
	"insert_record" => "Lisää uusi tietue",
	"search_records" => "Etsi tietueita",
	"exactly" => "täsmälleen",
	"like" => "melkein kuin",
	"required_fields_red" => "Vaaditut tiedot merkitty punaisella.",
	"insert_result" => "Tallennus:",
	"record_inserted" => "Tietue lisätty ongelmitta.",
	"update_result" => "Päivitys:",
	"record_updated" => "Tietue päivitetty ongelmitta.",
	"profile_updated" => "Käyttäjätietosi on päivitetty ongelmitta.",
	"delete_result" => "Poisto:",
	"record_deleted" => "Tietue poistettu ongelmitta.",
	"duplication_possible" => "Kahdentuminen on mahdollinen",
	"fields_max_length" => "Sisältö yhdessä tai useammassa kentässä on liian pitkä.",
	"current_upload" => "Nykyinen tiedosto",
	"delete" => "poista",
	"total_records" => "Tietueita yhteensä",
	"confirm_delete?" => "Vahvista poisto?",
    "unselect_all" => "Unselect all", // to change
    "select_all" => "Select all", // to change
    "only_elements_this_page_selected_other_pages_kept" => "Only the elements of the current page will be selected. If you selected elements in other pages, such selection will be kept.", // to change
    "all_elements_will_be_unselected_also_other_pages" => "All the elements will be unselected, also elements selected in other pages.", // to change
    "delete_selected" => "Delete selected", // to change
	"is_equal" => "on yhtä kuin",
	"is_different" => "ei ole yhtä kuin",
	"is_not_null" => "ei ole null",
	"is_not_empty" => "ei ole tyhjä",
	"contains" => "sisältää",
	"doesnt_contain" => "doesn't contain", // to change
	"starts_with" => "alussa",
	"ends_with" => "lopussa",
	"greater_than" => ">",
	"less_than" => "<",
    "greater_equal_than" => ">=",
    "less_equal_than" => "<=",
    "between" => "between", // to change
    "between_and" => "and", // to change, used for the between search operator: between .... AND .....
	"export_to_csv" => "Vie CSV-muotoon",
	"new_insert_executed" => "Uusi lisäys suoritettu",
	"new_update_executed" => "Uusi päivitys suoritettu",
	"null" => "Tyhj&auml;",
	"is_null" => "on tyhj&auml;",
	"is_empty" => "arvoa ei asetettu",
	"continue" => "jatka",
	'current_password' => 'nykyinen salasana',
	'new_password' => 'uusi salasana',
	'new_password_repeat' => 'vahvista uusi salasana',
	'password_changed' => 'salasana on vaihdettu',
	'change_your_password' => 'vaihda salasanasi',
	'your_info' => 'omat tietosi',
	'sort_by' => 'lajittelu',
	'sort' => 'lajittele',
	'pie_chart' => 'Piiraskaavio',
'bar_chart' => 'Palkkikaavio',
'line_chart' => 'Viivakaavio',
'doughnut_chart' => 'Donitsikaavio',
'show_report' => 'Näytä kaavio',
'show_labels' => 'Näytä otsikot',
'show_legend' => 'Näytä kuvaus',
'group_data_by' => 'Ryhmittele tiedot:',
'x_axis' => 'X-akseli',
'y_axis' => 'Y-akseli',
'show' => 'näytä',
'percentage' => '%',
'count' => 'laske',
'sum' => 'summa',
'average' => 'keskiarvo',
'min' => 'min',
'max' => 'max',
'variance' => 'vaihtelu',
'standard_deviation' => 'normaali poikkeama',
'of' => '/',
'simple_report' => 'yksinkertainen raportti',
'advanced_sql_report' => 'monipuolinen SQL-raportti',
'type_your_custom_sql_query_here' => 'Kirjoita tähän oma SQL-hakulausekkeesi: ',
'advanced_sql_reports_are_disabled' => 'Monipuoliset SQL-raportit poissa käytöstä',
'advanced_sql_report_instructions_first_part' => 'Voit määritellä oman SQL-hakulausekkeen. Esimerkiksi jos sinulla on <b>asiakkaat</b>-taulu ja siinä <b>ikä</b>-kenttä, voit esittää asiakkaiden ikäjakauman seuraavalla hakulauselleella:',
'advanced_sql_report_instructions_query_part' => '<br/><br/><div class=&quot;code_snippet&quot;>SELECT age_customer, count(*) FROM customers GROUP BY age_customer</div><br/><br/>',// DON'T TRANSLATE, LEAVE IT UNCHANGED
'advanced_sql_report_instructions_second_part' => 'Huomaa, että ensimmäinen nimeämäsi kenttä toimii kaavion <b>X-akselina</b> ja toinen kenttä <b>Y-akselina</b>.<br/>Dokumentaatiosta löydät lisää esimerkkejä.',
'generate_report' => 'Muodosta kaavio',
'use_semicolon_forbidden_omit_trailing_semicolmn' => 'Puolipisteen (;) käyttö on estetty turvallisuussyist, voit jättää puolipisteen pois lopusta.',
'sql_report_must_start_with_select' => 'SQL-hakulausekkeen on alettava komennolla "SELECT "',
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
$normal_messages_ar['months_short'][1] = 'Tammi';
$normal_messages_ar['months_short'][2] = 'Helmi';
$normal_messages_ar['months_short'][3] = 'Maalis';
$normal_messages_ar['months_short'][4] = 'Huhti';
$normal_messages_ar['months_short'][5] = 'Touko';
$normal_messages_ar['months_short'][6] = 'Kesä';
$normal_messages_ar['months_short'][7] = 'Heinä';
$normal_messages_ar['months_short'][8] = 'Elo';
$normal_messages_ar['months_short'][9] = 'Syys';
$normal_messages_ar['months_short'][10] = 'Loka';
$normal_messages_ar['months_short'][11] = 'Marras';
$normal_messages_ar['months_short'][12] = 'Joulu';

// please don't change the indexes (1,2,3,...) if you want your week to start on Sunday, set $weeks_start_on_sunday = 1 in config.php
$normal_messages_ar['days_short'][1] = 'Ma';
$normal_messages_ar['days_short'][2] = 'Ti';
$normal_messages_ar['days_short'][3] = 'Ke';
$normal_messages_ar['days_short'][4] = 'To';
$normal_messages_ar['days_short'][5] = 'Pe';
$normal_messages_ar['days_short'][6] = 'La';
$normal_messages_ar['days_short'][7] = 'Su';
// error messages
$error_messages_ar = array (
	"int_db_empty" => "Virhe. Sisäinen tietokanta on tyhjä.",
	"get" => "Virhe haettaessa muuttujia.",


	"no_functions" => "Virhe. Toimintoja ei valittu.<br>Ole hyvä ja palaa takaisin kotisivulle.",
	"no_unique_key" => "Virhe. Sinulla ei ole yhtään \"primary key\"-kenttää taulussasi.",
	"upload_error" => "Tiedoston latauksessa tapahtui virhe.",
	"no_authorization_update" => "Oikeutesi eivät riitä tämän tietueen muokkaukseen.",
	"no_authorization_delete" => "Oikeutesi eivät riitä tämän tietueen poistamiseen.",
	"no_authorization_view" => "Oikeutesi eivät riitä tämän tietueen tarkasteluun.",
	"deleted_only_authorizated_records" => "Vain ne tietueet poistettiin, joiden poistoon sinulla oli oikeudet.",
	"record_from_which_you_come_no_longer_exists" => "Hakemaasi tietuetta ei ole enää olemassa.",
	"date_not_representable" => "Päivämäärää ei voida esittää DaDaBIKin pp-kk-vv listauksissa, sen arvo on: ",
	"this_record_is_the_last_one" => "Tämä on viimeinen tietue.",
	"this_record_is_the_first_one" => "Tämä on ensimmäinen tietue.",
	"current_password_wrong" => 'nykyinen salasana on väärin',
	"passwords_are_different" => 'salasana ja vahvistus poikkeavat toisistaan',
	"new_password_must_be_different_old" => 'the new password must be different from the current one', // to change
	"new_password_is_empty" => 'uusi salasana on tyhjä',
	"you_cant_live_edit_click_edit" => 'You can\'t live edit this field, please click on the edit icon on your left to edit the entire record.', // to change
	"you_dont_have_enough_permissions_to_edit_field" => 'You don\'t have enough permissions to edit this field.' // to change
	);

//login messages
$login_messages_ar = array(
	"username" => "Käyttäjätunnus",
	"password" => "Salasana",
	"please_authenticate" => "Kirjautuminen",
	"login" => "Kirjaudu sisään",
	"username_password_are_required" => "Käyttäjätunnus ja salasana vaaditaan",
	"pwd_gen_link" => "Luo salasana",
	"incorrect_login" => "Kirjautumistiedoissa virhe, tarkista tunnus ja salasana.",
	"pwd_explain_text" =>"Kirjoita haluamasi salasana ja paina <b>Salaa salasana!</b>.",
	"pwd_explain_text_2" =>"Paina <b>Rekisteröidy</b> ja kirjoita saamasi salattu salasana ao. kenttään.",
	"pwd_suggest_email_sending"=>"Haluatko että sinulle lähetetään salasana sähköpostitse?",
	"pwd_send_link_text" =>"Lähetä salasana sähköpostitse!",
	"pwd_encrypt_button_text" => "Salaa salasana!",
	"pwd_register_button_text" => "Rekisteröi salasana ja poistu",
	"too_many_failed_login_account_blocked" => "Liian monta epäonnistunutta yritystä. Käyttäjätunnuksesi on estetty.",
	"warning_you_still_have" => "VAROITUS! Sinulla on vielä ",
	"attempts_before_account_blocking" => " yritystä ennen kuin käyttäjätunnuksesi estetään.",
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
