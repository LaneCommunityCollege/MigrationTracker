<?php
//Database Configuration Information
$host = 'localhost';
$dbuser = '';
$db = '';
$dbpw = '';

//replace host pattern
//should contain a regex matching all valid hostnames
$valid_urls = '/http:\/\/(www\.)?(lanecc\.edu|www2dev\.lanecc\.edu)/';

$db = new PDO("mysql:host=$host;dbname=$db", $dbuser, $dbpw);
?>
