<?php
session_start();
$config = require("./config.php");
require("dbconnect.php");
if(empty($_GET['id'])){
	die("Modpack not specified.");
}
if(empty($_GET['build'])){
	die("Build not specified.");
}
if(empty($_GET['newname'])){
	die("New name not specified.");
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Unauthorized require or login session has expired!");
}
$sql = mysqli_query($conn, "SELECT `name` FROM `builds` WHERE `id` = " .mysqli_real_escape_string($conn, $_GET['build']));
$name = mysqli_fetch_array($sql);
echo $name['name'];
if(substr($_SESSION['perms'],1,1)!=="1") {
	echo 'Insufficient permission!';
	exit();
}
mysqli_query($conn, "INSERT INTO builds(`name`,`minecraft`,`java`,`mods`,`modpack`) SELECT `name`,`minecraft`,`java`,`mods`,`modpack` FROM `builds` WHERE `id` = '".$_GET['build']."'");
$lb = mysqli_fetch_array(mysqli_query($conn, "SELECT `id` FROM `builds` ORDER BY `id` DESC LIMIT 1"))['id'];
mysqli_query($conn, "UPDATE `builds` SET `name` = '".mysqli_real_escape_string($conn, $_GET['newname'])."' WHERE `id` = ".$lb);
mysqli_query($conn, "UPDATE `builds` SET `modpack` = '".mysqli_real_escape_string($conn, $_GET['id'])."' WHERE `id` = ".$lb);
mysqli_query($conn, "UPDATE `modpacks` SET `latest` = '".mysqli_real_escape_string($conn, $name['name'])."' WHERE `id` = ".mysqli_real_escape_string($conn, $_GET['id']));
header("Location: ".$config['dir']."modpack?id=".$_GET['id']);
exit();