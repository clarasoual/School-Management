<?php
require 'vendor/autoload.php';

$mongoHost = getenv('MONGO_HOST') ?: 'localhost';
$mongoPort = getenv('MONGO_PORT') ?: '27017';
$client = new MongoDB\Client("mongodb://$mongoHost:$mongoPort");
$db = $client->school_management;
?>