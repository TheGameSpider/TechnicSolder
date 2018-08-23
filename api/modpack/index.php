<?php
header('Content-Type: application/json');
$url = $_SERVER['REQUEST_URI'];
if (strpos($url, '&k') !== false) {
    $url = substr($url, 0, strpos($url, "&k"));
}
if (strpos($url, '?k') !== false) {
    $url = substr($url, 0, strpos($url, "?k"));
}
if (strpos($url, '&cid') !== false) {
    $url = substr($url, 0, strpos($url, "&cid"));
}
if (strpos($url, '?cid') !== false) {
    $url = substr($url, 0, strpos($url, "?cid"));
}

$config = require("../../config.php");
require("../../dbconnect.php");
if(substr($url,-12)=="/api/modpack" || substr($url,-13)=="/api/modpack/" || substr($url,-25)=="/api/modpack?include=full" || substr($url,-26)=="/api/modpack/?include=full"){
	$modpacks = array();
	$result = mysqli_query($conn, "SELECT * FROM `modpacks`");
	if(substr($url,-25)=="/api/modpack?include=full" || substr($url,-26)=="/api/modpack/?include=full") {
				while($modpack=mysqli_fetch_array($result)){
					$buildsres = mysqli_query($conn, "SELECT * FROM `builds` WHERE `modpack` = ".$modpack['id']);
						$builds = [];
					while($build=mysqli_fetch_array($buildsres)){
						array_push($builds, $build['name']);
					}
						$mn = $modpack['name'];
						$mpn = $modpack['pretty_name'];
						$url = $modpack['url'];
						$icon = $modpack['icon'];
						$icon_md5 = $modpack['icon_md5'];
						$logo = $modpack['logo'];
						$logo_md5 = $modpack['logo_md5'];
						$background = $modpack['background'];
						$background_md5 = $modpack['background_md5'];
						$recommented = $modpack['recommented'];
						$latest = $modpack['latest'];
						
						$modpacks[$mn] = array(
							"name" => $mn,
							"display_name" => $mpn,
							"url" => $url,
							"icon" => $icon,
							"icon_md5" => $icon_md5,
							"logo" => $logo,
							"logo_md5" => $logo_md5,
							"background" => $background,
							"background_md5" => $background_md5,
							"recommented" => $recommented,
							"latest" => $latest,
							"builds" => $builds
						);
					
				}
	} else {
		while($modpack=mysqli_fetch_array($result)){
			$mn = $modpack['name'];
			$mpn = $modpack['pretty_name'];
			$modpacks[$mn] = $mpn;
		}
	}
	$data = [
		"modpacks" => $modpacks,
		"mirror_url" => $config['mirror_url']
	];
	print_r(json_encode($data));
	exit();
}
// substr($url,0-$len2)=="/api/modpack/".$modpack['name']."?include=mods" || substr($url,0-1-$len2)=="/api/modpack/".$modpack['name']."/?include=mods"
$result = mysqli_query($conn, "SELECT * FROM `modpacks`");
while($modpack=mysqli_fetch_array($result)){
	$buildsres = mysqli_query($conn, "SELECT * FROM `builds` WHERE `modpack` = ".$modpack['id']);
	$len=strlen($modpack['name'])+strlen("/api/modpack/");
	$len2=strlen($modpack['name'])+strlen("/api/modpack/?include=mods");
	if(

	substr($url,0-$len)=="/api/modpack/".$modpack['name'] ||
	substr($url,0-1-$len)=="/api/modpack/".$modpack['name']."/" ||
	substr($url,0-$len2)=="/api/modpack/".$modpack['name']."?include=mods" ||
	substr($url,0-1-$len2)=="/api/modpack/".$modpack['name']."/?include=mods"
	
	){
		$mn = $modpack['name'];
		$mpn = $modpack['pretty_name'];
		$url = $modpack['url'];
		$icon = $modpack['icon'];
		$icon_md5 = $modpack['icon_md5'];
		$logo = $modpack['logo'];
		$logo_md5 = $modpack['logo_md5'];
		$background = $modpack['background'];
		$background_md5 = $modpack['background_md5'];
		$recommented = $modpack['recommented'];
		$latest = $modpack['latest'];
		$builds = [];
		while($build=mysqli_fetch_array($buildsres)){
			array_push($builds, $build['name']);
		}
		
		$data = array(
			"name" => $mn,
			"display_name" => $mpn,
			"url" => $url,
			"icon" => $icon,
			"icon_md5" => $icon_md5,
			"logo" => $logo,
			"logo_md5" => $logo_md5,
			"background" => $background,
			"background_md5" => $background_md5,
			"recommended" => $recommented,
			"latest" => $latest,
			"builds" => $builds
		);
		print_r(json_encode($data));
	}
	
	while($build=mysqli_fetch_array($buildsres)){
		$len=strlen($build['name'])+strlen($modpack['name'])+strlen("/api/modpack//");
		if(
		substr($url,0-14-$len)=="/api/modpack/".$modpack['name']."/".$build['name']."/?include=mods" ||
		substr($url,0-13-$len)=="/api/modpack/".$modpack['name']."/".$build['name']."?include=mods" ||
		substr($url,0-$len)=="/api/modpack/".$modpack['name']."/".$build['name'] ||
		substr($url,0-1-$len)=="/api/modpack/".$modpack['name']."/".$build['name']."/"
		){
			$mv = $build['minecraft'];
			$forge = null;
			$java = $build['java'];
			$memory = $build['memory'];
			$mods = [];
			$modslist= explode(',', $build['mods']);
			$modnumber = 0;
			foreach($modslist as $mod) {
				if($mod!==""){
				$modsres = mysqli_query($conn, "SELECT * FROM `mods` WHERE `id` = ".$mod);
				$modinfo=mysqli_fetch_array($modsres);
				if(
				substr($url,0-14-$len)=="/api/modpack/".$modpack['name']."/".$build['name']."/?include=mods" ||
				substr($url,0-13-$len)=="/api/modpack/".$modpack['name']."/".$build['name']."?include=mods"
				) {
					$mods[$modnumber] = array(
						"name" => $modinfo['name'],
						"version" => $modinfo['version'],
						"md5" => $modinfo['md5'],
						"url" => $modinfo['url'],
						"pretty_name" => $modinfo['pretty_name'],
						"author" => $modinfo['author'],
						"description" => $modinfo['description'],
						"link" => $modinfo['link'],
						"donate" => $modinfo['donink']
				);
				} else {
					$mods[$modnumber] = array(
						"name" => $modinfo['name'],
						"version" => $modinfo['version'],
						"md5" => $modinfo['md5'],
						"url" => $modinfo['url']
					);
				}
				$modnumber++;
			}}
			$data = [
				"minecraft" => $mv,
				"forge" => $forge,
				"java" => $java,
				"memory" => $memory,
				"mods" => $mods
			];
			print_r(json_encode($data));
		}
	}
}
