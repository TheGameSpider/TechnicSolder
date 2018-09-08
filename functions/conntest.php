<?php
define('DBHOST', $_POST['db-host']);
define('DBUSER', $_POST['db-user']);
define('DBPASS', $_POST['db-pass']);
define('DBNAME', $_POST['db-name']);
$conn = mysqli_connect(DBHOST,DBUSER,DBPASS,DBNAME);
if(!$conn) {
	die('error');
} else {
	die('success');
}