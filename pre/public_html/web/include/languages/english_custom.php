<?php

/*
Add here the translations you want to modify, what you set here will override what is set in english.php. You can do the same for the other languages as well, for example adding an italian_custom.php or a french_custom.php file.

E.g. in english.php you have this sentence, that is showed when a record is updated:

"record_updated" => "Item correctly updated.",

if you don't like it, you can override the translation adding to the engliish_custom_template.php file the following line:

$normal_messages_ar['record_updated'] = 'The record has been correctly updated.';

You can also change the sentence according to the table you are managing, for example, the following sentence is used to override the original one only if we are usign the "quotes" table

if (isset($table_name) && $table_name === 'quotes'){
	$normal_messages_ar['record_inserted'] = 'quote correctly inserted, please edit it to add the products included in this quote. ';
} 


*/