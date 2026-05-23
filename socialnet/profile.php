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

$owner = $_GET["owner"] ?? "";

if ($owner === "") {
    $owner = $currentUser["username"];
}

$owner = trim($owner);

// If viewing another user's profile, require valid CSRF token
if ($owner !== $currentUser["username"]) {
    $csrfToken = $_GET["CSRF"] ?? "";

    if (!verify_profile_view_token($owner, $csrfToken)) {
        http_response_code(403);
        exit("Invalid or expired CSRF token.");
    }
}

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