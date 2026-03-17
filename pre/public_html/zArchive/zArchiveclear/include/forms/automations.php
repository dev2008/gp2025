<?php
/*
***********************************************************************************
DaDaBIK (DaDaBIK is a DataBase Interfaces Kreator) https://dadabik.com/
Copyright (C) 2001-2024 Eugenio Tacchini

This program is distributed "as is" and WITHOUT ANY WARRANTY, either expressed or implied, without even the implied warranties of merchantability or fitness for a particular purpose.

This program is distributed under the terms of the DaDaBIK license, which is included in this package (see dadabik_license.txt). For all the details see dadabik_license.txt.

If you are unsure about what you are allowed to do with this license, feel free to contact info@dadabik.com
***********************************************************************************
*/
?>
<h1>Automations</h1>

<p>Automations are operations that DaDaBIK executes automatically when a particular event occurs.


<?php if ($orazio_edition === 1){ 

echo '<span id="confirmation_message_container"><div class="msg_error" id="alert_message"><h4>The feature is <b>DISABLED</b> when you use an '.$orazio_name.' free account.<br>You need to <a href="index.php?function=deploy">publish and deploy</a> this application to enable it.</h4></div></span>';

} ?>


<div class="boxes_admin_outer">
        <div class="boxes_admin">
        
            E-mail automation

        </div>
       
<div style="padding: 10px 15px;">
<p>You can set DaDaBIK to <strong>send an email to one or more addresses when a record is inserted or updated</strong>. 


<?php if ($orazio_edition === 0){ ?>

To enable or disable e-mail automations, you have to set the config $enable_update_notice_email_sending / $enable_insert_notice_email_sending parameters.

<p>Email automation after insert is currently: <?php echo '<strong>'.(($enable_insert_notice_email_sending === 1) ? 'enabled' : 'disabled').'</strong>'; ?> 

<?php
if ($enable_insert_notice_email_sending === 1){
    echo ' - Recipients: To ('.implode(', ', $insert_notice_email_to_recipients_ar).') Cc ('.implode(', ', $insert_notice_email_cc_recipients_ar).') Bcc ('.implode(', ', $insert_notice_email_bcc_recipients_ar).')';
}
?>  

<p>Email automation after update is currently: <?php echo '<strong>'.(($enable_update_notice_email_sending === 1) ? 'enabled' : 'disabled').'</strong>'; ?> 

<?php
if ($enable_update_notice_email_sending === 1){
    echo ' - Recipients: To ('.implode(', ', $update_notice_email_to_recipients_ar).') Cc ('.implode(', ', $update_notice_email_cc_recipients_ar).') Bcc ('.implode(', ', $update_notice_email_bcc_recipients_ar).')';
}
?>  

<?php } ?>

</div>
</div>

<div class="boxes_admin_outer">
        <div class="boxes_admin">
        
            Operational Hooks

        </div>
       
<div style="padding: 10px 15px;">

<p>With operational hooks you can execute <strong>custom PHP code</strong> when a particular event occurs.

<p>DaDaBIK currently supports <strong>before insert</strong>, <strong>after insert</strong>, <strong>before update</strong>, <strong>after update</strong> and <strong>after delete</strong> hooks for each table/view; this means that for each table/view you can enrich the DaDaBIK workflow with your own code.

<p>You are not limited to email sending, you can do whatever you need, for example you can execute additional operations on the database or call an external API.  

<h3>These are the operational hooks currently set</h3>


<?php

$cnt = 0;
if (isset($hooks)){
    foreach ($hooks as $key => $value){

        if (table_exists($key) && ( isset($value['insert']['after']) || isset($value['insert']['before']) || isset($value['update']['after']) || isset($value['update']['before']) || isset($value['delete']['after']))   ){
    
            $cnt++;
        
            echo '<p><h3>'.$key.':</h3> ';
            
            if (isset($value['insert']['after'])){
                echo '• <b>after insert</b>, function to execute: '.$value['insert']['after'].'<br>';
            }
        
            if (isset($value['insert']['before'])){
                echo '• <b>before insert</b>, function to execute: '.$value['insert']['before'].'<br>';
            }
        
            if (isset($value['update']['after'])){
                echo '• <b>after update</b>, function to execute: '.$value['update']['after'].'<br>';
            }
        
            if (isset($value['update']['before'])){
                echo '• <b>before update</b>, function to execute: '.$value['update']['before'].'<br>';
            }
        
            if (isset($value['delete']['after'])){
                echo '• <b>after delete</b>, function to execute: '.$value['delete']['after'].'<br>';
            }
        }   
    }
}

if ($cnt === 0){
    echo '<p>No operational hooks set.';
}

?>

<?php
?>



</div>
</div>

<div class="boxes_admin_outer">
        <div class="boxes_admin">
        
            Custom Buttons

        </div>
       
<div style="padding: 10px 15px;">

<p>DaDaBIK, by default, creates in your pages all the CRUD buttons you need to INSERT, SEARCH, EDIT, DELETE data, to produce Chart and Pivot reports and to execute all the IMPORT/EXPORT operations. However, you may need additional buttons to execute specific operations or implement your workflows. 
<p><b>Custom buttons</b> are buttons that you can add to various parts of your application, which trigger the execution of custom PHP/Javascript code. For example you could add a button on the top of a <i>customers</i> form that changes the value of a field or call an external API using Javascript or send a PDF version of the page to an email address using PHP.

<h3>These are the custom buttons currently set</h3>

<?php
if (isset($custom_buttons) && count($custom_buttons) > 0){
    foreach ($custom_buttons as $buttons_table){

        foreach ($buttons_table as $button){
    
            if ($button['label_type'] === 'language_file'){
                $button['label'] = $normal_messages_ar[$button['label']];
            }
    
            echo '<p> <input type="button" class="btn btn-secondary custom_button" style="'.$button['style'].'" value="'.$button['label'].'"> <b>Callback function:</b> '.$button['callback_function'].' <b>code type:</b> '.$button['type'];
    
        }

    }
}
else{
    echo '<p>No Custom Buttons set.';
}

?>
<?php
?>

</div>
</div>
<?php if ($orazio_edition === 0){ ?>
<div class="boxes_admin_outer">
        <div class="boxes_admin">
        
            Examples and documentation

        </div>
       
<div style="padding: 10px 15px;">

<?php
?>

<p>For more details about automations, <a href="https://dadabik.com/index.php?function=show_documentation" target="_blank">check the Hooks chapter and the Custom Buttons chapter </a> of the documentation.
<p>You can see <i>email automations</i> and <i>operational hooks</i> in action in the <i>email alert</i> and <i>operational hooks</i> chapters of this tutorial:<br><br>
<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/4SnsMUxHZiM?start=6158" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>



</p>

</div>
</div>


<?php } ?>
