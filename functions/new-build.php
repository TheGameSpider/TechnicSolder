<?php
session_start();
$config = require("./config.php");
require("dbconnect.php");
if(empty($_GET['id'])){
	die("Modpack not specified.");
}
if(empty($_GET['name'])){
	die("Name not specified.");
}
if(empty($_GET['type'])){
	die("type not specified.");
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Unauthorized require or login session has expired!");
}
if(substr($_SESSION['perms'],1,1)!=="1") {
	echo 'Insufficient permission!';
	exit();
}
if($_GET['type']=="update") {
	mysqli_query($conn, "INSERT INTO builds(`name`,`minecraft`,`java`,`mods`,`modpack`,`public`) SELECT `name`,`minecraft`,`java`,`mods`,`modpack`,`public` FROM `builds` WHERE `modpack` = '".$_GET['id']."' ORDER BY `id` DESC LIMIT 1");
	mysqli_query($conn, "UPDATE `builds` SET `name` = '".mysqli_real_escape_string($conn, $_GET['name'])."' WHERE `modpack` = ".mysqli_real_escape_string($conn, $_GET['id'])." ORDER BY `id` DESC LIMIT 1");
	mysqli_query($conn, "UPDATE `modpacks` SET `latest` = '".mysqli_real_escape_string($conn, $_GET['name'])."' WHERE `id` = ".mysqli_real_escape_string($conn, $_GET['id']));
	mysqli_query($conn, "UPDATE `builds` SET `public` = 0 WHERE `modpack` = ".mysqli_real_escape_string($conn, $_GET['id'])." ORDER BY `id` DESC LIMIT 1");
	header("Location: ".$config['dir']."modpack?id=".$_GET['id']);
	exit();
} else {
	mysqli_query($conn, "INSERT INTO builds(`name`,`modpack`,`public`) VALUES ('".mysqli_real_escape_string($conn, $_GET['name'])."','".mysqli_real_escape_string($conn, $_GET['id'])."',0)");
	mysqli_query($conn, "UPDATE `modpacks` SET `latest` = '".mysqli_real_escape_string($conn, $_GET['name'])."' WHERE `id` = ".mysqli_real_escape_string($conn, $_GET['id']));
	header("Location: ".$config['dir']."modpack?id=".$_GET['id']);
	exit();
}