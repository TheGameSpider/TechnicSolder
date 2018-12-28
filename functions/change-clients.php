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
$clients = implode(",",$_GET['client']);
mysqli_query($conn, "UPDATE `modpacks` SET `clients` = '".mysqli_real_escape_string($conn, $clients)."' WHERE `id`=".$_GET['id']);
header("Location: ".$config['dir']."modpack?id=".$_GET['id']);
exit();