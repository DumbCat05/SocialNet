<?php
require_once "../includes/db.php";
require_once "../includes/auth.php";

require_login();

$currentUser = get_current_user_account($conn);

if (!$currentUser) {
    header("Location: /socialnet/signin.php");
    exit;
}

$owner = $currentUser["username"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!verify_csrf_token($_POST["csrf_token"] ?? "")) {
        http_response_code(403);
        exit("Invalid CSRF token.");
    }

    $owner = trim($_POST["owner"] ?? "");

    if ($owner === "") {
        $owner = $currentUser["username"];
    }
}

if ($owner !== $currentUser["username"]) {
    http_response_code(403);
    exit("You are not allowed to view this profile.");
}

$stmt = $conn->prepare(
    "SELECT username, fullname, description FROM account WHERE username = ?"
);

$stmt->bind_param("s", $owner);
$stmt->execute();

$result = $stmt->get_result();
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

        <form method="POST" action="/socialnet/profile.php">
            <input type="hidden" name="csrf_token"
                   value="<?php echo htmlspecialchars(generate_csrf_token(), ENT_QUOTES, "UTF-8"); ?>">

            <input type="text" name="owner" placeholder="Enter username">
            <button type="submit">View Profile</button>
        </form>

        <?php if ($profileUser): ?>
            <div class="info-box">
                <p><strong>Profile Owner:</strong> <?php echo htmlspecialchars($profileUser["username"], ENT_QUOTES, "UTF-8"); ?></p>
                <p><strong>Full Name:</strong> <?php echo htmlspecialchars($profileUser["fullname"], ENT_QUOTES, "UTF-8"); ?></p>
            </div>

            <h2>Profile Content</h2>

            <div class="profile-content">
                <?php echo nl2br(htmlspecialchars($profileUser["description"] ?? "", ENT_QUOTES, "UTF-8")); ?>
            </div>
        <?php else: ?>
            <div class="message error">
                Profile owner not found.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>