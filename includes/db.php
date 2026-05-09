<?php
$host = "localhost";
$dbname = "socialnet";
$dbuser = "socialnet_user";
$dbpass = "SocialNetPass123!";

$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . htmlspecialchars($conn->connect_error));
}

$conn->set_charset("utf8mb4");
?>
