<?php

$host = 'localhost'; // Database host
$dbname = 'water_america_project'; // Database name (change if testing)
$username = 'root'; // Database username
$password = ''; // Database password

try 
{
    // Create a new PDO instance to connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode to exception

} 
catch (PDOException $e) 
{
    // Display error message if the connection fails
    die("Database connection failed: " . $e->getMessage());
}

?>