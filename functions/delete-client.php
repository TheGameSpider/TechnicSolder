<?php
header('Content-Type: application/json');
session_start();
require("dbconnect.php");
if(empty($_GET['id'])){
	die("Id not specified.");
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Unauthorized require or login session has expired!");
}
if(substr($_SESSION['perms'],6,1)!=="1") {
	echo 'Insufficient permission!';
	exit();
}
mysqli_query($conn, "DELETE FROM `clients` WHERE `id` = '".mysqli_real_escape_string($conn,$_GET['id'])."'");
exit();