<?php
header('Content-Type: application/json');
$url = $_SERVER['REQUEST_URI'];
$config = require("../../config.php");

if(substr($url,0-strlen($config['api_key']))==$config['api_key'] || substr($url,0-1-strlen($config['api_key']))==$config['api_key']."/"){
	print '{"valid":"Key validated.","name":"APIkey","cteated_at":"TechnicSolder"}';
} else {
	print '{"error":"Invalid key provided."}';
}