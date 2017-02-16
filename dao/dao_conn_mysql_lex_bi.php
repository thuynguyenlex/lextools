<?php
//Server info:
$db_host="127.0.0.1";
$db_user="root";
$db_pass="";
$db_name="lex_bi";
//mysql_connect("$db_host","$db_name","$db_pass") or die ("could not connect to db mysql:" .$db_name);
//mysql_select_db("$db_name") or die ("no database: " .$db_name);
$mysqli = new mysqli("$db_host", "$db_user", "$db_pass", "$db_name");
if ($mysqli->connect_errno) {
	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
?>
