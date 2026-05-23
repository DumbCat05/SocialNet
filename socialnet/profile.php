<?php
require_once "../includes/db.php";
require_once "../includes/auth.php";

require_login();

$owner = $_GET["owner"] ?? "";

if ($owner === "") {
    $owner = $_SESSION["username"];
}

/*
    WARNING:
    This query is intentionally vulnerable for ATT-4 SQL Injection demo.
    Do not use this version as the final secure version.
*/
$sql = "SELECT username, fullname, description FROM account WHERE username = '$owner'";

$result = $conn->query($sql);

if (!$result) {
    die("SQL Error: " . htmlspecialchars($conn->error));
}

$profileUser = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile - SocialNet</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <?php include "../includes/menu.php"; ?>

    <div class="container">
        <h1>Profile Page</h1>

        <?php if ($profileUser): ?>
            <div class="info-box">
                <p><strong>Profile Owner:</strong> <?php echo htmlspecialchars($profileUser["username"]); ?></p>
                <p><strong>Full Name:</strong> <?php echo htmlspecialchars($profileUser["fullname"]); ?></p>
            </div>

            <h2>Profile Content</h2>

            <div class="profile-content">
                <?php echo $description; ?>
            </div>
        <?php else: ?>
            <div class="message error">
                Profile owner not found.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
