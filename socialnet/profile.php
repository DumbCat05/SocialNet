<?php
require_once "../includes/db.php";
require_once "../includes/auth.php";
require_once "../includes/friends.php";

require_login();

$currentUser = get_current_user_account($conn);

if (!$currentUser) {
    header("Location: /socialnet/signin.php");
    exit;
}

// Default: view own profile
$owner = $currentUser["username"];

// Friend profile must use csrf_token in URL
if (isset($_GET["csrf_token"])) {
    $tokenOwner = verify_profile_view_token($_GET["csrf_token"]);

    if ($tokenOwner === false) {
        http_response_code(403);
        exit("Invalid or expired CSRF token.");
    }

    $owner = $tokenOwner;
}

// Block manual attack like profile.php?owner=user2
if (isset($_GET["owner"]) && $_GET["owner"] !== $currentUser["username"]) {
    http_response_code(403);
    exit("Direct profile access is not allowed.");
}

// Safe prepared statement
$stmt = $conn->prepare(
    "SELECT id, username, fullname, description 
     FROM account 
     WHERE username = ?"
);

$stmt->bind_param("s", $owner);
$stmt->execute();

$result = $stmt->get_result();
$profileUser = $result->fetch_assoc();

if (!$profileUser) {
    http_response_code(404);
    exit("Profile owner not found.");
}

// Authorization check
$isOwnProfile = ((int)$currentUser["id"] === (int)$profileUser["id"]);
$isFriend = are_friends($conn, $currentUser["id"], $profileUser["id"]);

if (!$isOwnProfile && !$isFriend) {
    http_response_code(403);
    exit("You are not allowed to view this profile.");
}
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

        <div class="info-box">
            <p>
                <strong>Profile Owner:</strong>
                <?php echo htmlspecialchars($profileUser["username"], ENT_QUOTES, "UTF-8"); ?>
            </p>

            <p>
                <strong>Full Name:</strong>
                <?php echo htmlspecialchars($profileUser["fullname"], ENT_QUOTES, "UTF-8"); ?>
            </p>
        </div>

        <h2>Profile Content</h2>

        <div class="profile-content">
            <?php echo nl2br(htmlspecialchars($profileUser["description"] ?? "", ENT_QUOTES, "UTF-8")); ?>
        </div>
    </div>
</body>
</html>