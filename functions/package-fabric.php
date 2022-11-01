<?php
header('Content-Type: application/json');
session_start();
$config = require("config.php");
require("dbconnect.php");
if (substr($_SESSION['perms'], 5, 1)!=="1") {
    echo '{"status":"error","message":"Insufficient permission!"}';
    exit();
}

//$link = $_GET['link'];
$version = $_GET['loader'];
$mcversion = $_GET['version'];
if (!file_exists("../forges/modpack-".$version)) {
    mkdir("../forges/modpack-".$version);
} else {
    echo '{"status":"error","message":"Folder modpack-'.$version.' already exists!"}';
    exit();
}
if (file_put_contents("../forges/modpack-".$version."/version.json", file_get_contents("https://meta.fabricmc.net/v2/versions/loader/".$mcversion."/".urlencode($version)."/profile/json"))) {
    $zip = new ZipArchive();
    if ($zip->open("../forges/fabric-".$version.".zip", ZIPARCHIVE::CREATE) !== TRUE) {
        echo '{"status":"error","message":"Could not open archive"}';
        exit();
    }
    $path = "../forges/modpack-".$version."/version.json";
    $zip->addEmptyDir('bin');
    if (is_file($path)) {
        $zip->addFile($path, "bin/version.json") or die ('{"status":"error","message":"Could not add file to archive"}');
    }
    $zip->close();
    unlink("../forges/modpack-".$version."/version.json");
    rmdir("../forges/modpack-".$version);
    $md5 = md5_file("../forges/fabric-".$version.".zip");
    $url = "http://".$config['host'].$config['dir']."forges/fabric-".urlencode($version).".zip";
    $res = mysqli_query($conn, "INSERT INTO `mods` (`name`,`pretty_name`,`md5`,`url`,`link`,`author`,`description`,`version`,`mcversion`,`filename`,`type`) VALUES 
                           ('forge','Fabric (alpha)','".$md5."','".$url."','https://fabricmc.net/','FabricMC Team', 'Fabric is a lightweight, experimental modding toolchain for Minecraft.', '".$version."','f".$mcversion."','fabric-".$version.".zip','forge')");
    if ($res) {
        echo '{"status":"succ","message":"Mod has been saved."}';
        exit();
    } else {
        echo '{"status":"error","message":"Mod could not be added to database"}';
        exit();
    }
} else {
    echo '{"status":"error","message":"File download failed."}';
    unlink("../forges/modpack-".$version."/version.json");
    rmdir("../forges/modpack-".$version);
    exit();
}
