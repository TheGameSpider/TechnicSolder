<?php
session_start();
if (!$_SESSION['user']||$_SESSION['user']=="") {
    die('Unathorized request or login session has expired!');
}
if (!$_GET['id']) {
    die('ID not provided');
}
$config = require_once("config.php");
require_once("dbconnect.php");
$q = mysqli_query($conn, "SELECT `filename` FROM `mods` WHERE `id` = ".mysqli_real_escape_string($conn,$_GET['id']));
$fileName = mysqli_fetch_array($q)['filename'];
$fileInfo = pathinfo("../mods/".$fileName);
$exisingzip = new ZipArchive();
$exisingzip->open("../mods/".$fileName);
$exisingzip->extractTo("../mods/mods-".$fileName."/tmp/");
$exisingzip->close();
$attachment_location = "../mods/mods-".$fileName."/tmp/mods/".$fileInfo['filename'].".jar";
header("Content-Type: application/java-archive");
header("Content-Transfer-Encoding: Binary");
header("Content-Length:".filesize($attachment_location));
header("Content-Disposition: attachment; filename=".$fileInfo['filename'].".jar");
readfile($attachment_location);
unlink($attachment_location);
rmdir("../mods/mods-".$fileName."/tmp/mods");
rmdir("../mods/mods-".$fileName."/tmp");
rmdir("../mods/mods-".$fileName);
die();
