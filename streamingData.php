<?php
$results = shell_exec('GET https://rapidapi.com/movie-of-the-night-movie-of-the-night-default/api/streaming-availability');
$arrayCode = json_decode($results);
var_dump($arrayCode);
?>

