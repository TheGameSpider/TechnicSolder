<?php
header('Content-Type: application/json');
session_start();
require("dbconnect.php");
if(empty($_GET['id'])){
	die("Build not specified.");
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("You need to be logged in!");
}
$bq = mysqli_query($conn, "SELECT * FROM `builds` WHERE `id` = ".$_GET['id']);
$build = mysqli_fetch_array($bq);
mysqli_query($conn, "UPDATE `modpacks` SET `recommended` = '".$build['name']."' WHERE `id` = ".$build['modpack']);
$response = array(
	"name" => $build['name'],
	"mc" => $build['minecraft']
);
echo(json_encode($response));
exit();