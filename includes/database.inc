<?php
$db_host = 'localhost';
$db_user = 'deb66668_site';
$db_password = 'YKWv5KV5hbn1i0soyYNq3hYLA7sEuKXz';
$database = 'deb66668_svn';

//mysql_connect($db_host, $db_user, $db_password);
//mysql_select_db($database);
//verbinding maken met de server
$verbinding = mysql_connect($db_host,$db_user,$db_password) or die("server");
if (!$verbinding){
    echo "hij doet het niet";
    
    }
//echo "verbinding met de server<BR>";

//verbinding maken met de database
$dbverbinding = mysql_select_db($database) or die("database: ".mysql_error()."");
if (!$dbverbinding){
    echo "hij doet het niet";
    
    }
?>