<?php
if(empty($_GET['link'])) {
    die("No link provided");
}
$headers=get_headers($_GET['link']);
if(stripos($headers[0],"200 OK")) {
    die("OK");
} else {
    die("ERR");
}
