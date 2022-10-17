<?php
$results = shell_exec('GET https://rapidapi/fluis.lacasse/api/baseballapi/');
$arrayCode = json_decode($results);
var_dump($arrayCode);
?>

