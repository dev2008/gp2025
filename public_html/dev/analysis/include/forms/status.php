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
<p><h1>Status</h1>

<p>When your application is in <strong>maintenance</strong> mode, only admin users (and, optionally, some other users) can access it.
The config parameters debug_mode and display_sql will be automatically enabled and the page execution time will be printed in the page's footer. The login page will still be available and, for HTTP API, the tokens generation will still be available.


<p>This application is currently in status:
<form id="status_settings_form"><select id="status_installation">
<option <?php if ($status_installation === 'available') echo ' selected'; ?>>available</option>
<option <?php if ($status_installation === 'maintenance') echo ' selected'; ?>>maintenance</option>
</select>


<br><br>Non-admin users who can access the application when in maintenance mode:<br>
<input type="text" id="users_maintenance_installation" size="80" value="<?php  echo $users_maintenance_installation; ?>"><br>Type here the usernames separated by comma e.g. "bob,alice,anna,marco". If you leave it empty, only admin users will be allowed to access it. In the remote case that one of the users you have in your users table <strong><?php echo $users_table_name; ?></strong> has a username containing a comma, this feature can produce unpredictable results, please double check before using it.<br><br>

If you are changing/replacing files (e.g. your custom functions files), the safest option is to entirely shut down the website (e.g. you can "suspend it" if you are using Plesk) because the execution of a page during a file transfer can lead to a partial execution of the code.<br><br>
<input type="submit" value="Save" class="btn btn-primary">
</form>


</p>

