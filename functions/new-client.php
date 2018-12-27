<?php
session_start();
$config = require("./config.php");
require("dbconnect.php");
if(empty($_GET['name'])){
	die("Name not specified.");
}
if(empty($_GET['uuid'])){
	die("UUID not specified.");
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Unauthorized require or login session has expired!");
}
if(substr($_SESSION['perms'],6,1)!=="1") {
	echo 'Insufficient permission!';
	exit();
}
mysqli_query($conn, "INSERT INTO clients(`name`,`UUID`) VALUES ('".mysqli_real_escape_string($conn, $_GET['name'])."', '".mysqli_real_escape_string($conn, $_GET['uuid'])."')");
header("Location: ".$config['dir']."clients");
exit();