<?php
$conf = require("config.php");
define('DBHOST', $conf['db-host']);
define('DBUSER', $conf['db-user']);
define('DBPASS', $conf['db-pass']);
define('DBNAME', $conf['db-name']);
$conn = mysqli_connect(DBHOST,DBUSER,DBPASS,DBNAME);
if(!$conn) {
die("Connection failed : " . mysqli_error($conn));
}