<?php
session_start();
$config = require("config.php");
require("dbconnect.php");
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Unauthorized require or login session has expired!");
}
if(substr($_SESSION['perms'],0,1)!=="1") {
	echo 'You do not have permission to create modpacks!';
	exit();
}
if(substr($_SESSION['perms'],1,1)!=="1") {
	echo 'You do not have permission to create builds!';
	exit();
}
$mpdname = mysqli_real_escape_string($conn, $_POST['display_name']);
$mpname = mysqli_real_escape_string($conn, $_POST['name']);
$bmods = mysqli_real_escape_string($conn, $_POST['modlist']);
$bjava = mysqli_real_escape_string($conn, $_POST['java']);
$bmemory = mysqli_real_escape_string($conn, $_POST['memory']);
$bforge = mysqli_real_escape_string($conn, $_POST['versions']);
mysqli_query($conn, "INSERT INTO modpacks(`name`,`display_name`,`icon`,`icon_md5`,`logo`,`logo_md5`,`background`,`background_md5`,`public`,`recommended`,`latest`) VALUES ('".$mpname."','".$mpdname."','http://".$config['host'].$config['dir']."resources/default/icon.png','A5EA4C8FA53984C911A1B52CA31BC008','http://".$config['host'].$config['dir']."resources/default/logo.png','70A114D55FF1FA4C5EEF7F2FDEEB7D03','http://".$config['host'].$config['dir']."resources/default/background.png','88F838780B89D7C7CD10FE6C3DBCDD39',1,'1.0','1.0')");
$mpq = mysqli_query($conn, "SELECT `id` FROM `modpacks` ORDER BY `id` DESC LIMIT 1");
$mp = mysqli_fetch_array($mpq);
$mpi =  intval($mp['id']);
$fq = mysqli_query($conn, "SELECT `mcversion` FROM `mods` WHERE `id` = ". $bforge);
$f = mysqli_fetch_array($fq);
$minecraft =  $f['mcversion'];
mysqli_query($conn, "INSERT INTO builds(`name`,`modpack`,`public`,`mods`,`java`,`memory`,`minecraft`) VALUES ('1.0','".$mpi."',1,'".$bforge.",".$bmods."','".$bjava."','".$bmemory."','".$minecraft."')");
header("Location: ".$config['dir']."modpack?id=".$mpi);
exit();
