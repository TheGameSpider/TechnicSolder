<?php
error_reporting(0);
header('Content-Type: application/json');
echo file_get_contents($_GET['link']);