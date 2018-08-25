<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start();
header('Content-Type: application/json');
$dbcon = require("dbconnect.php");
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Must be logged in!");
}
if(empty($_GET['build'])){
	die("Build not specified");
}
$buildres = mysqli_query($conn, "SELECT * FROM `builds` WHERE `id` = ".$_GET['build']);
$build = mysqli_fetch_array($buildres);
if(!empty($_GET['add'])){
	mysqli_query($conn, "UPDATE `builds` SET `mods` ='".$build['mods'].",".$_GET['add']."' WHERE `id` = ".$_GET['build']);
}
if(!empty($_GET['remove'])){
	$mods = str_replace($_GET['remove'],"",$build['mods']);
	mysqli_query($conn, "UPDATE `builds` SET `mods` ='".$mods."' WHERE `id` = ".$_GET['build']);
	header('Location: buildeditor.php?build='.$_GET['build']);
}
$modslist= explode(',', $build['mods']);
$modnumber = 0;
foreach($modslist as $mod){
	$modres = mysqli_query($conn, "SELECT * FROM `mods` WHERE `id` =".$mod);
	if($modres) {
	$modinfo=mysqli_fetch_array($modres);
	$mods[$modnumber] = array(
						"id" => $modinfo['id'],
						"name" => $modinfo['name'],
						"version" => $modinfo['version'],
						"url" => $modinfo['url'],
						"pretty_name" => $modinfo['pretty_name'],
						"author" => $modinfo['author']
	);
	$modnumber++;
	}
}
$data = [
	"mods"=>$mods
];
print_r(json_encode($data));