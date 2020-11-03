<?php
$user = "banki";
$pass = "o8Q13a45r3I2R18E";
$dbname = "banki";
$dbhost = "localhost";
$db = new mysqli($dbhost, $user, $pass, $dbname);
if ($db->connect_error) die('Connection error(' . $db->connect_errno . ') ' . $db->connect_error);
$db->query("SET NAMES utf8") or die ($db->error);
$db->set_charset('utf8');