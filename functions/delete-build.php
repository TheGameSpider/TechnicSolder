<?php
header('Content-Type: application/json');
session_start();
require("dbconnect.php");
if(empty($_GET['id']) || empty($_GET['pack'])){
    die("Build not specified.");
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
    die("Unauthorized request or login session has expired!");
}
if(substr($_SESSION['perms'],1,1)!=="1") {
    die("Insufficient permission!");
}
mysqli_query($conn, "DELETE FROM `builds` WHERE `id` = '".mysqli_real_escape_string($conn,$_GET['id'])."'");
$bq = mysqli_query($conn, "SELECT * FROM `builds` WHERE `modpack` = '".mysqli_real_escape_string($conn, $_GET['pack'])."' AND `public` = 1 ORDER BY `id` DESC LIMIT 1");
if($bq) {
    $build = mysqli_fetch_array($bq);
    //mysqli_query($conn, "UPDATE `modpacks` SET `latest` = '".$build['name']."' WHERE `id` = '".$build['modpack']."'");
    $response = array(
        "exists" => true,
        "name" => $build['name'],
        "mc" => $build['minecraft']
    );
} else {
    $response = array(
        "exists" => false
    );
}
$lpq = mysqli_query($conn, "SELECT `name`,`modpack`,`public` FROM `builds` WHERE `public` = 1 AND `modpack` = ".mysqli_real_escape_string($conn, $_GET['pack'])." ORDER BY `id` DESC");
$latest_public = mysqli_fetch_array($lpq);
mysqli_query($conn, "UPDATE `modpacks` SET `latest` = '".$latest_public['name']."' WHERE `id` = ".mysqli_real_escape_string($conn, $_GET['pack']));
echo json_encode($response);
exit();
