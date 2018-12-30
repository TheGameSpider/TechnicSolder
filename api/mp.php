<?php
$dbcon = require("./functions/dbconnect.php");
$mp = mysqli_real_escape_string($conn, $_GET['name']);
$result = mysqli_query($conn, "SELECT * FROM `modpacks` WHERE `name` = '" . $mp . "'");
$modpack = mysqli_fetch_array($result);
$buildsres = mysqli_query($conn, "SELECT * FROM `builds` WHERE `modpack` = " . $modpack['id']);
$builds = [];
while($build=mysqli_fetch_array($buildsres)){
	array_push($builds, $build['name']);
}
$response = array(
	"recommended" => $modpack['recommended'],
	"latest" => $modpack['latest']
);
return json_encode($response);
exit();