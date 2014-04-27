<?php
$db_host = 'localhost';
$db_user = 'svni2427_nieuwer';
$db_password = '.8Zcay[G!kc{';
$database = 'svni2427_site';

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