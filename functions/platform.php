<?php
error_reporting(0);
header('Content-Type: application/json');
echo file_get_contents("http://api.technicpack.net/modpack/".$_GET['slug']."?build=600");