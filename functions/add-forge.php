<?php
header('Content-Type: application/json');
session_start();
$config = require("config.php");
require("dbconnect.php");
if(substr($_SESSION['perms'],5,1)!=="1") {
	echo '{"status":"error","message":"Insufficient permission!"}';
	exit();
}
$link = $_GET['link'];
$version = $_GET['version'];
$mcversion = $_GET['mcversion'];
if($mcversion == "1.7.10-1.7.10") {
	$mcversion = "1.7.10";
}
if(!file_exists("../forges/modpack-".$version)) {
	mkdir("../forges/modpack-".$version);
} else {
	echo '{"status":"error","message":"Folder modpack-'.$version.' already exists!"}';
	exit();
}
if(file_put_contents("../forges/modpack-".$version."/modpack.jar", file_get_contents($link))) {
	$zip = new ZipArchive();
	if ($zip->open("../forges/forge-".$version.".zip", ZIPARCHIVE::CREATE) !== TRUE) {
		echo '{"status":"error","message":"Could not open archive"}';
		exit();
	}
	$path = "../forges/modpack-".$version."/modpack.jar";
	$zip->addEmptyDir('bin');
	if(is_file($path)){
		$zip->addFile($path, "bin/modpack.jar") or die ('{"status":"error","message":"Could not add file to archive"}');
	}
	$zip->close();
	unlink("../forges/modpack-".$version."/modpack.jar");
	rmdir("../forges/modpack-".$version);
	$md5 = md5_file("../forges/forge-".$version.".zip");
	$url = "http://".$config['host'].$config['dir']."forges/forge-".$version.".zip";
	$res = mysqli_query($conn, "INSERT INTO `mods` (`name`,`pretty_name`,`md5`,`url`,`link`,`author`,`description`,`version`,`mcversion`,`filename`,`type`) VALUES ('forge','Minecraft Forge','".$md5."','".$url."','https://minecraftforge.net','LexManos','Minecraft Forge is a common open source API allowing a broad range of mods to work cooperatively together. Is allows many mods to be created without them editing the main Minecraft Code','".$version."','".$mcversion."','forge-".$version.".zip','forge')");
	if($res) {
		echo '{"status":"succ","message":"Mod has been saved."}';
		exit();
	} else {
		echo '{"status":"error","message":"Mod could not be added to database"}';
		exit();
	}
} else {
	echo '{"status":"error","message":"File download failed."}';
	unlink("../forges/modpack-".$version."/modpack.jar");
	rmdir("../forges/modpack-".$version);
	exit();
}