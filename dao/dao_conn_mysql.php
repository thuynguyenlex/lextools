<?php
//Server info:
$db_host="localhost";
$db_user="root";
$db_pass="";
$db_name="lex_db";
mysql_connect("$db_host","$db_name","$db_pass") or die ("could not connect to db mysql:" .$db_name);
mysql_select_db("$db_name") or die ("no database: " .$db_name);
//new PDO('mysql:host=localhost;dbname=testdb;charset=utf8mb4', 'username', 'password');
?>
