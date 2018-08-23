<?php
$config = require("config.php");
$fileName = $_FILES["file1"]["name"];
$fileTmpLoc = $_FILES["file1"]["tmp_name"];
$fileType = $_FILES["file1"]["type"];
$fileSize = $_FILES["file1"]["size"]; 
$fileErrorMsg = $_FILES["file1"]["error"]; 
if (!$fileTmpLoc) {
    echo "ERROR: Please browse for a file before clicking the upload button.";
    exit();
}
if($fileType !== "application/x-zip-compressed") {
	echo "ERROR: Not application/x-zip-compressed.";
    exit();
}
if(move_uploaded_file($fileTmpLoc, $config['mirror_location'].$fileName)){
    echo "$fileName upload is complete";
} else {
    echo "move_uploaded_file function failed";
}
?>