<?php
header('Content-Type: application/json');
require("dbconnect.php");
$forge_data = file_get_contents("https://files.minecraftforge.net/net/minecraftforge/forge/promotions_slim.json"); // can't find normal promotions.json
$forge_link = "https://maven.minecraftforge.net/net/minecraftforge/forge"; // /1.16.5-36.1.16/forge-1.16.5-36.1.16-installer.jar
$versions = [];
$forges = [];
$id = 0;
foreach (json_decode($forge_data, true)['promos'] as $gameVersion => $forgeVersion) { // key, value
    $id++;
    if (strpos($gameVersion, "latest")) {
        $gameVersion = str_replace("-latest", "", $gameVersion);
        // 1.13 "1.13.2-25.0.219" and later don't have a universal.jar
        if (version_compare($gameVersion, '1.13', '>=')) {
            $suffixExt = 'installer.jar';
        } else {
            $suffixExt = 'universal.jar';
        }
        $versions[$forgeVersion] = $forge_link.'/'.$gameVersion.'-'.$forgeVersion.'/forge-'.$gameVersion.'-'.$forgeVersion.'-'.$suffixExt;

        $modsq = mysqli_query($conn, "SELECT * FROM `mods` WHERE `name` = 'forge' AND `version` = '".$forgeVersion."'");
        if (mysqli_num_rows($modsq)==0) {
            $forges[$gameVersion] = array(
                "id" => $id,
                "mc" => $gameVersion,
                "name" => $forgeVersion,
                "link" => $versions[$forgeVersion]
            );
        }
    }
}
//$forges['1.7.10'] = array(
//    "id" => 99,
//    "mc" => "1.7.10",
//    "name" => "10.13.4.1614",
//    "link" => "https://files.minecraftforge.net/maven/net/minecraftforge/forge/1.7.10-10.13.4.1614-1.7.10/forge-1.7.10-10.13.4.1614-1.7.10-universal.jar"
//);
print_r(json_encode($forges, JSON_PRETTY_PRINT));
