<?php
session_start();
$config = require("config.php");
$dbcon = require("dbconnect.php");
if(empty($_GET['build'])){
	die("Build not specified.");
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("You need to be logged in!");
}
$buildres = mysqli_query($conn, "select * from `builds` WHERE `id` =".$_GET['build']);
$build = mysqli_fetch_array($buildres);
$modpackres = mysqli_query($conn, "select * from `modpacks` WHERE `id` =".$build['modpack']);
$modpack = mysqli_fetch_array($modpackres);
mysqli_query($conn, "UPDATE `modpacks` SET `latest` = '".$build['name']."' , `recommented` = '".$build['name']."' WHERE `id` = '".$modpack['id']."'");
header('Location: index.php?pack='.$modpack['name'].'&succ=Build released!');