<?php
header('Content-Type: application/json');
require("dbconnect.php");
$forge_data = file_get_contents("http://files.minecraftforge.net/maven/net/minecraftforge/forge/promotions.json");
$versions = [];
$forges = [];
$id = 0;
foreach(json_decode($forge_data, true)['promos'] as $forge => $ff) {
	$id++;
	if($forge !== "latest" & $forge !== "recommended") {
		if(strpos($forge, "latest")) {
			$fv = str_replace("-latest", "", $forge);
			$fvs = $ff['version'];
			foreach ($ff['files'] as $file) {
				$fext=$file[0];
				if($fext=="zip"||$fext=="jar") {
					$fn = str_replace("latest", $fvs, $forge);
					$versions[$fv] = "https://files.minecraftforge.net/maven/net/minecraftforge/forge/".$fn."/forge-".$fn."-universal.".$fext;
				}
			}
			$modsq = mysqli_query($conn, "SELECT * FROM `mods` WHERE `name` = 'forge' AND `version` = '".$fvs."'");
			if(mysqli_num_rows($modsq)==0) {
				$forges[$fv] = array(
					"id" => $id,
					"mc" => $fv,
					"name" => $fvs,
					"link" => $versions[$fv]
				);
			}
		}
	}
}
$forges['1.7.10'] = array(
	"id" => 99,
	"mc" => "1.7.10",
	"name" => "10.13.4.1614",
	"link" => "https://files.minecraftforge.net/maven/net/minecraftforge/forge/1.7.10-10.13.4.1614-1.7.10/forge-1.7.10-10.13.4.1614-1.7.10-universal.jar"
);
print_r(json_encode($forges, JSON_PRETTY_PRINT));