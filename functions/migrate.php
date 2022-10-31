<?php
session_start();
$config = require("./config.php");
require("dbconnect.php");
if (!$_SESSION['user']||$_SESSION['user']=="") {
    die("Unauthorized request or login session has expired!");
}
if (empty($_POST['db-pass'])) {
    die("error");
}
if(empty($_POST['db-name'])) {
    die("error");
}
if(empty($_POST['db-user'])) {
    die("error");
}
if(empty($_POST['db-host'])) {
    die("error");
}
if(empty($_POST['solder-orig'])) {
    die("error");
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
    die("error");
}
$conn2 = mysqli_connect($_POST['db-host'],$_POST['db-user'],$_POST['db-pass'],$_POST['db-name']);
if(!$conn2) {
    die("error");
}
mysqli_query($conn, "TRUNCATE `modpacks`");
mysqli_query($conn, "TRUNCATE `builds`");
mysqli_query($conn, "TRUNCATE `clients`");
mysqli_query($conn, "TRUNCATE `mods`");
// ----- MODPACKS ----- \\
$res = mysqli_query($conn2, "SELECT `name`,`slug`,`status`,`latest_build_id`,`recommended_build_id` FROM `modpacks`");
while($row = mysqli_fetch_array($res)) {
    $latest = mysqli_fetch_array(mysqli_query($conn2,"select `version` FROM `builds` WHERE `id` = ".$row['latest_build_id']))['version'];
    $recommended = mysqli_fetch_array(mysqli_query($conn2,"select `version` FROM `builds` WHERE `id` = ".$row['recommended_build_id']))['version'];
    if($row['status'] == "public") {
        $public = 1;
    } else {
        $public = 0;
    }
    mysqli_query($conn, "INSERT INTO `modpacks` (`display_name`,`name`,`public`,`latest`,`recommended`,`icon`) VALUES ('".$row['name']."','".$row['slug']."',".$public.",'".$latest."','".$recommended."','http://demo.solder.cf/TechnicSolder/resources/default/icon.png')");
}
// ----- BUILDS ----- \\
$res = mysqli_query($conn2, "SELECT `modpack_id`,`version`,`minecraft_version`,`status`,`java_version`,`required_memory` FROM `builds`");
while($row = mysqli_fetch_array($res)) {
    if($row['status'] == "public") {
        $public = 1;
    } else {
        $public = 0;
    }
    mysqli_query($conn, "INSERT INTO `builds` (`modpack`,`name`,`public`,`minecraft`,`java`,`memory`) VALUES ('".$row['modpack_id']."','".$row['version']."',".$public.",'".$row['minecraft_version']."','".$row['java_version']."','".$row['memory']."')");
}
// ----- CLIENTS ----- \\
$res = mysqli_query($conn2, "SELECT `title`,`token` FROM `clients`");
while($row = mysqli_fetch_array($res)) {
    mysqli_query($conn,"INSERT INTO `clients` (`name`,`UUID`) VALUES ('".$row['title']."','".$row['token']."')");
}
// ----- MODS ----- \\
$res = mysqli_query($conn2, "SELECT * FROM `releases`");
while($row = mysqli_fetch_array($res)) {
    $url = "http://".$config['host'].$config['dir']."mods/".end(explode("/",$row['path']));
    $packageres = mysqli_query($conn2, "SELECT * FROM `packages` WHERE `id` = ".$row['package_id']);
    $package = mysqli_fetch_array($packageres);
    mysqli_query($conn,"INSERT INTO `mods` (`type`,`url`,`version`,`md5`,`filename`,`name`,`pretty_name`,`author`,`link`,`donlink`,`description`) VALUES ('mod','".$url."','".$row['version']."','".$row['md5']."','".end(explode("/",$row['path']))."','".$package['slug']."','".$package['name']."','".$package['author']."','".$package['website_url']."','".$package['donation_url']."','".$package['description']."')");
    copy($_POST['solder-orig']."/storage/app/public/".$row['path'], dirname(dirname(__FILE__))."/mods/".end(explode("/",$row['path'])));
}
// ----- BUILD_RELEASE ----- \\
$res = mysqli_query($conn2, "SELECT * FROM `build_release`");
while($row = mysqli_fetch_array($res)) {
    $mods = [];
    $mres = mysqli_query($conn, "SELECT `mods` FROM `builds` WHERE `id` = ".$row['build_id']);
    $ma = mysqli_fetch_array($mres);
    $ml = explode(',', $ma['mods']);
    if(count($ml)>0) {
        array_push($mods, implode(',',$ml));
    }
    array_push($mods, $row['release_id']);
    mysqli_query($conn, "UPDATE `builds` SET `mods` = '". implode(',',$mods)."' WHERE `id` = ".$row['build_id']);
}
