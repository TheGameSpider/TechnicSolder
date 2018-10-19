<?php
session_start();
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
mysqli_query($conn, "UPDATE `modpacks` SET `name` = '".mysqli_real_escape_string($conn, $_GET['name'])."', `display_name` = '".mysqli_real_escape_string($conn, $_GET['display_name'])."' WHERE `id`=".$_GET['id']);
header('Location: /modpack?id='.$_GET['id']);