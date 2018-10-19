<?php
session_start();
require("dbconnect.php");
if(empty($_GET['id'])){
	die("Mod not specified.");
}
if(empty($_GET['bid'])){
	die("Build not specified.");
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Unauthorized require or login session has expired!");
}
if(substr($_SESSION['perms'],1,1)!=="1") {
	echo 'Insufficient permission!';
	exit();
}
$modsq = mysqli_query($conn, "SELECT `mods` FROM `builds` WHERE `id` = ".mysqli_real_escape_string($conn, $_GET['bid']));
$mods = mysqli_fetch_array($modsq);
$modslist = explode(',', $mods['mods']);
$nmodlist = array_diff($modslist, [$_GET['id']]);
$modslist = implode(',', $nmodlist);
mysqli_query($conn, "UPDATE `builds` SET `mods` = '".$modslist."' WHERE `id` = ".mysqli_real_escape_string($conn, $_GET['bid']));
exit();
