<?php
header('Content-Type: application/json');
$url = $_SERVER['REQUEST_URI'];
$config = require("../config.php");
$dbcon = require("../dbconnect.php");
if(substr($url,-4)=="/api" || substr($url,-5)=="/api/"){
	print '{"api":"TechnicSolder","version":"v0.0.0.2","stream":"DEV"}';
} else {
	print '{"status":404,"error":"Not Found"}';
}