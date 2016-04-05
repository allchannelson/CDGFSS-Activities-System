<?php
$connection = mysql_connect('localhost', 'root', '') or 
	trigger_error(mysql_error(), E_USER_ERROR);
mysql_set_charset('utf8', $connection);
?>
