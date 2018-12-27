<?php
session_start();
$config = require("config.php");
require("dbconnect.php");
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Unauthorized require or login session has expired!");
}
if(substr($_SESSION['perms'],0,1)!=="1") {
	echo 'Insufficient permission!';
	exit();
}
$mpq = mysqli_query($conn, "SELECT `id` FROM `modpacks` ORDER BY `id` DESC LIMIT 1");
$mp = mysqli_fetch_array($mpq);
$mpi =  intval($mp['id'])+1;
mysqli_query($conn, "INSERT INTO modpacks(`name`,`display_name`,`icon`,`icon_md5`,`logo`,`logo_md5`,`background`,`background_md5`,`public`) VALUES ('unnamed-modpack-".$mpi."','Unnamed modpack','http://".$config['host'].$config['dir']."resources/default/icon.png','A5EA4C8FA53984C911A1B52CA31BC008','http://".$config['host'].$config['dir']."resources/default/logo.png','70A114D55FF1FA4C5EEF7F2FDEEB7D03','http://".$config['host'].$config['dir']."resources/default/background.png','88F838780B89D7C7CD10FE6C3DBCDD39',1)");
$mpq = mysqli_query($conn, "SELECT `id` FROM `modpacks` ORDER BY `id` DESC LIMIT 1");
$mp = mysqli_fetch_array($mpq);
$mpi =  intval($mp['id']);
header("Location: ".$config['dir']."modpack?id=".$mpi);
exit();
