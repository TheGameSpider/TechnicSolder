<?php
header('Content-Type: application/json');
session_start();
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die('{"status":"error","message":"Login session has expired"}');
}
if(substr($_SESSION['perms'],3,1)!=="1") {
	echo '{"status":"error","message":"Insufficient permission!"}';
	exit();
}
$config = require("config.php");
require("dbconnect.php");
$fileName = $_FILES["fiels"]["name"];
$fileJarInTmpLocation = $_FILES["fiels"]["tmp_name"];
if (!$fileJarInTmpLocation) {
    echo '{"status":"error","message":"File is too big! Check your post_max_size (current value '.ini_get('post_max_size').') andupload_max_filesize (current value '.ini_get('upload_max_filesize').') values in '.php_ini_loaded_file().'"}';
    exit();
}
include('toml.php');
function slugify($text) {
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);
  //$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
  $text = preg_replace('~[^-\w]+~', '', $text);
  $text = trim($text, '-');
  $text = preg_replace('~-+~', '-', $text);
  $text = strtolower($text);
  if (empty($text)) {
    return 'n-a';
  }
  return $text;
}
$fileNameTmp = explode("-",slugify($fileName));
array_pop($fileNameTmp);
$fileNameShort=implode("-",$fileNameTmp);
$fileNameZip=$fileNameShort.".zip";
$fileName=$fileNameShort.".jar";
$fileJarInFolderLocation="../mods/mods-".$fileNameShort."/".$fileName;
$fileZipLocation="../mods/".$fileNameZip;
$fileInfo=array();
if(!file_exists("../mods/mods-".$fileNameShort)) {
	mkdir("../mods/mods-".$fileNameShort);
} else {
	echo '{"status":"error","message":"Folder mods-'.$fileNameShort.' already exists!"}';
	exit();
}
function processFile($zipExists, $md5) { 
	global $fileName;
	global $fileNameZip;
	global $fileNameShort;
	global $fileJarInFolderLocation;
	global $fileZipLocation;
	global $conn;
	global $warn;
	global $fileInfo;
	$legacy=false;
	$mcmod=array();
	$result = @file_get_contents("zip://".realpath($fileJarInFolderLocation)."#META-INF/mods.toml"); 
	if (!$result) { 
		# fail 1.14+ mod check
		$result = file_get_contents("zip://".realpath($fileJarInFolderLocation)."#mcmod.info"); 
		if (!$result) { 
			# fail legacy mod check
			$warn['b'] = true;
			$warn['level'] = "warn";
			$warn['message'] = "File does not contain mod info. Manual configuration required.";
		} else {
			# is legacy mod
			$legacy=true;
			$mcmod = json_decode(preg_replace('/\r|\n/','',trim($result)),true)[0];
			if(!$mcmod['modid']||!$mcmod['name']||!$mcmod['description']||!$mcmod['version']||!$mcmod['mcversion']||!$mcmod['url']||!$mcmod['authorList']) {
				$warn['b'] = true;
				$warn['level'] = "info";
				$warn['message'] = "There is some information missing in mcmod.info.";
			}
		}
	} else { # is 1.14+ mod
		$legacy=false;
		$mcmod = parseToml($result);
		//error_log(json_encode($mcmod, JSON_PRETTY_PRINT));
		if(!$mcmod['mods']['modId']||!$mcmod['mods']['displayName']||!$mcmod['mods']['description']||!$mcmod['mods']['version']||!$mcmod['mods']['displayURL']||!($mcmod['mods']['author'] && $mcmod['mods']['authors'])) {
			$warn['b'] = true;
			$warn['level'] = "info";
			$warn['message'] = "There is some information missing in mcmod.info.";
		}
	} 
	if ($zipExists) { // while we could put a file check here, it'd be redundant (it's checked before).
		// cached zip
	} else {
		$zip = new ZipArchive();
		if ($zip->open($fileZipLocation, ZIPARCHIVE::CREATE) !== TRUE) {
			echo '{"status":"error","message":"Could not open archive"}';
			exit();
		}
		$zip->addEmptyDir('mods');
		if(is_file($fileJarInFolderLocation)){
			$zip->addFile($fileJarInFolderLocation, "mods/".$fileName) or die ('{"status":"error","message":"Could not add file $key"}');
		}
		$zip->close();
	}
	if ($legacy) {
		if(!$mcmod['name']) {
			$pretty_name = mysqli_real_escape_string($conn, $fileNameShort);
		} else {
			$pretty_name = mysqli_real_escape_string($conn, $mcmod['name']);
		}
		if(!$mcmod['modid']) {
			$name = slugify($pretty_name);
		} else {
			if(@preg_match("^[a-z0-9]+(?:-[a-z0-9]+)*$", $mcmod['modid'])) {
				$name = $mcmod['modid'];
			} else {
				$name = slugify($mcmod['modid']);
			}
		}
		$link = $mcmod['url'];
		$author = mysqli_real_escape_string($conn, implode(', ', $mcmod['authorList']));
		$description = mysqli_real_escape_string($conn, $mcmod['description']);
		$version = $mcmod['version'];
		$mcversion = $mcmod['mcversion'];
	} else {
		if(!$mcmod['mods']['displayName']) {
			$pretty_name = mysqli_real_escape_string($conn, $fileNameShort);
		} else {
			$pretty_name = mysqli_real_escape_string($conn, $mcmod['mods']['displayName']);
		}
		if(!$mcmod['mods']['modId']) {
			$name = slugify($pretty_name);
		} else {
			if(preg_match("^[a-z0-9]+(?:-[a-z0-9]+)*$", $mcmod['mods']['modId'])) {
				$name = $mcmod['mods']['modId'];
			} else {
				$name = slugify($mcmod['mods']['modId']);
			}
		}
		$link = empty($mcmod['mods']['displayURL'])? $mcmod[0]['displayURL'] : $mcmod['mods']['displayURL'];
		$authorRoot=empty($mcmod[0]['authors'])? $mcmod[0]['author'] : $mcmod[0]['authors'];
		$authorMods=empty($mcmod['mods']['authors'])? $mcmod['mods']['author'] : $mcmod['mods']['authors'];
		$author = mysqli_real_escape_string($conn, empty($authorRoot)? $authorMods : $authorRoot);
		$description = mysqli_real_escape_string($conn, $mcmod['mods']['description']);
		$mcversion=$mcmod['dependencies.'.$mcmod['mods']['modId']]['versionRange'];
		if (empty($mcversion)) { //if there is no dependency specified, get from filename
			// THIS SHOULD NEVER BE NECESSARY, BUT SOME MODS (OptiFine) DON'T HAVE A MINECRAFT DEPENDENCY LISTED
			$divideDash=explode('-', $fileNameShort);
			$mcversion=$divideDash[1].'.'.$divideDash[2]; // we get modname-1-16-5-1-1-1.jar. we don't know if it is 1-16 or 1-16-5, so it's safer to assume 1-16
		}
		$version = $mcmod['mods']['version'];
		if ($version == "\${file.jarVersion}" ) {
			$tmpFilename=explode('-', $fileNameShort);
			array_shift($tmpFilename);
			$tmpFilename = implode('.', $tmpFilename);
			$version=$tmpFilename;
		}
		if (empty($version)) { //if there is no dependency specified, get from filename
			// THIS SHOULD NEVER BE NECESSARY, BUT SOME MODS (OptiFine) DON'T HAVE A MINECRAFT DEPENDENCY LISTED
			$divideDash=explode('-', $fileNameShort);
			$version=end($divideDash); // we get modname-1-16-5-1-1-1.jar. just take the last - as we don't know.
		}
	}
	if ($zipExists) {
		// cached zip, use given md5. (md5 is not checked empty(); should always be given if cached!)
	} else {
		$md5 = md5_file("../mods/".$fileInfo['filename'].".zip");
	}
	//$url = "http://".$config['host'].$config['dir']."mods/".$fileInfo['filename'].".zip";
	$res = mysqli_query($conn, "INSERT INTO `mods` (`name`,`pretty_name`,`md5`,`url`,`link`,`author`,`description`,`version`,`mcversion`,`filename`,`type`) VALUES ('".$name."','".$pretty_name."','".$md5."','','".$link."','".$author."','".$description."','".$version."','".$mcversion."','".$fileNameZip."','mod')");
	if($res) {
		if(@$warn['b']==true) {
			if($warn['level']=="info") {
				echo '{"status":"info","message":"'.$warn['message'].'","modid":'.mysqli_insert_id($conn).'}';
			} else {
				echo '{"status":"warn","message":"'.$warn['message'].'","modid":'.mysqli_insert_id($conn).'}';
			}
			
		} else {
			echo '{"status":"succ","message":"Mod has been uploaded and saved.","modid":'.mysqli_insert_id($conn).'}';
		}
	} else {
		echo '{"status":"error","message":"Mod could not be added to database"}';
	}
}

if(move_uploaded_file($fileJarInTmpLocation, $fileJarInFolderLocation)){
	$fileInfo = pathinfo($fileJarInFolderLocation);
	if(file_exists($fileZipLocation)) {
		$md5_1 = md5_file($fileJarInFolderLocation);
		$md5_2 = md5_file("zip://".realpath($fileZipLocation)."#mods/".$fileName);
		if($md5_1 !== $md5_2) {
			echo '{"status":"error","message":"File with name \''.$fileName.'\' already exists!","md51":"'.$md5_1.'","md52":"'.$md5_2.'","zip":"'.$fileJarInFolderLocation.'"}';
			//exit();
		} else {
			$fq = mysqli_query($conn, "SELECT `id` FROM `mods` WHERE `filename` = '".$fileNameZip."'");
			if(mysqli_num_rows($fq)==1){
				echo '{"status":"info","message":"This mod is already in the database.","modid":'.mysqli_fetch_array($fq)['id'].'}';
			} else {
				processFile(true, $md5_1); // use existing zip
			}	
		}
	} else {
		processFile(false, ''); // create zip
	}
	unlink($fileJarInFolderLocation);
	rmdir("../mods/mods-".$fileNameShort);

} else {
    echo '{"status":"error","message":"Permission denied! Please open SSH and run \'chown -R www-data '.addslashes(dirname(dirname(get_included_files()[0]))).'\'"}';
}
?>
