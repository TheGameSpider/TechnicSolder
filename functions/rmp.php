<?php
session_start();
$config = require("./config.php");
require("dbconnect.php");
if(empty($_GET['id'])){
	die("Modpack not specified.");
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Unauthorized require or login session has expired!");
}
if(substr($_SESSION['perms'],0,1)!=="1") {
	echo 'Insufficient permission!';
	exit();
}
mysqli_query($conn, "DELETE FROM `builds` WHERE `modpack` = '".mysqli_real_escape_string($conn,$_GET['id'])."'");
mysqli_query($conn, "DELETE FROM `modpacks` WHERE `id` = '".mysqli_real_escape_string($conn,$_GET['id'])."'");
header("Location: ".$config['dir']."dashboard");
exit();