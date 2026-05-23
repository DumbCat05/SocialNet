<?php
require_once "../includes/db.php";
require_once "../includes/auth.php";

require_login();
?>

<!DOCTYPE html>
<html>
<head>
    <title>About - SocialNet</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <?php include "../includes/menu.php"; ?>

    <div class="container">
        <h1>About Page</h1>

        <p><strong>Student Name:</strong> Nguyễn Viết Nguyên Bình</p>
        <p><strong>Student Number:</strong> 20239523</p>

        <p>This is a simple Social Network web application built with PHP, MySQL, Nginx, and Linux.</p>
    </div>
</body>
</html>
