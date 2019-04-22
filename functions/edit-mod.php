<?php
session_start();
$config = require("./config.php");
require("dbconnect.php");
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Unauthorized require or login session has expired!");
}
if(substr($_SESSION['perms'],4,1)!=="1") {
	echo 'Insufficient permission!';
	exit();
}
if(empty($_GET['id'])){
	die("Mod not specified.");
}
$result = mysqli_query($conn, "SELECT * FROM `mods` WHERE `id` = ".mysqli_real_escape_string($conn, $_GET['id']));
$mod = mysqli_fetch_array($result);
if($mod['name']!==$_POST['name']){
	mysqli_query($conn, "UPDATE `mods` SET `name` = '".mysqli_real_escape_string($conn, $_POST['name'])."' WHERE `name` = '".mysqli_real_escape_string($conn, $_GET['id'])."'");
}
if($mod['pretty_name']!==$_POST['pretty_name']){
	mysqli_query($conn, "UPDATE `mods` SET `pretty_name` = '".mysqli_real_escape_string($conn, $_POST['pretty_name'])."' WHERE `name` = '".mysqli_real_escape_string($conn, $_GET['id'])."'");
}
if($mod['description']!==$_POST['description']){
	mysqli_query($conn, "UPDATE `mods` SET `description` = '".mysqli_real_escape_string($conn, $_POST['description'])."' WHERE `name` = '".mysqli_real_escape_string($conn, $_GET['id'])."'");
}
if($_POST['submit']=="Save and close") {
	header("Location: ".$config['dir']."lib-mods");
	exit();
}
header("Location: ".$config['dir']."mod?id=".$_POST['name']);
exit();