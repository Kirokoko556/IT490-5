#!/usr/bin/php
<?php

$mydb = new mysqli('127.0.0.1','carter','abcde','IT490');

if ($mydb->errno != 0)
{
        echo "Failed to connect to database: ". $mydb->error . PHP_EOL;
        exit(0);
}

echo "Successfully connected to database".PHP_EOL;

$query = "select * from users;";

$response = $mydb->query($query);
if ($mydb->errno != 0)
{
        echo "Failed to execute query:".PHP_EOL;
        echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
        exit(0);
}

?>
