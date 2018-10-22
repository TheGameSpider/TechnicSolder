<?php
header('Content-Type: application/json');
session_start();
require("dbconnect.php");
if(empty($_GET['id'])){
	die("Build not specified.");
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Unauthorized require or login session has expired!");
}
if(substr($_SESSION['perms'],2,1)!=="1") {
	echo 'Insufficient permission!';
	exit();
}
$bq = mysqli_query($conn, "SELECT * FROM `builds` WHERE `id` = ".mysqli_real_escape_string($conn,$_GET['id']));
$build = mysqli_fetch_array($bq);
mysqli_query($conn, "UPDATE `modpacks` SET `recommended` = '".$build['name']."' WHERE `id` = ".$build['modpack']);
$response = array(
	"name" => $build['name'],
	"mc" => $build['minecraft']
);
echo(json_encode($response));
exit();