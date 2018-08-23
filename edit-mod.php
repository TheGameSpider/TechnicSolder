<?php
session_start();
$config = require("config.php");
$dbcon = require("dbconnect.php");
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Must be logged in!");
}
if(empty($_GET['mod'])){
	die("Mod not specified.");
}
$result = mysqli_query($conn, "SELECT * FROM `mods` WHERE `id` = '".$_GET['mod']."'");
$mod = mysqli_fetch_row($result);
if($mod['pretty_name']!==$_POST['pretty_name']){
	mysqli_query($conn, "UPDATE `mods` SET `pretty_name` = '".$_POST['pretty_name']."' WHERE `id` = '".$_GET['mod']."'");
}
if($mod['link']!==$_POST['link']){
	mysqli_query($conn, "UPDATE `mods` SET `link` = '".$_POST['link']."' WHERE `id` = '".$_GET['mod']."'");
}
if($mod['author']!==$_POST['author']){
	mysqli_query($conn, "UPDATE `mods` SET `author` = '".$_POST['author']."' WHERE `id` = '".$_GET['mod']."'");
}
if($mod['md5']!==$_POST['md5']){
	mysqli_query($conn, "UPDATE `mods` SET `md5` = '".$_POST['md5']."' WHERE `id` = '".$_GET['mod']."'");
}
if($mod['donlink']!==$_POST['donlink']){
	mysqli_query($conn, "UPDATE `mods` SET `donlink` = '".$_POST['donlink']."' WHERE `id` = '".$_GET['mod']."'");
}
if($mod['version']!==$_POST['version']){
	mysqli_query($conn, "UPDATE `mods` SET `version` = '".$_POST['version']."' WHERE `id` = '".$_GET['mod']."'");
}
if($mod['description']!==$_POST['description']){
	mysqli_query($conn, "UPDATE `mods` SET `description` = '".$_POST['description']."' WHERE `id` = '".$_GET['mod']."'");
}

header("Location: mods.php?succ=Mod ".$_POST['pretty_name']." updated!");