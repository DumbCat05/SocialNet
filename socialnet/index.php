<?php
require_once "../includes/db.php";
require_once "../includes/auth.php";

require_login();

$currentUser = get_current_user_account($conn);

$stmt = $conn->prepare("SELECT username, fullname FROM account WHERE id != ? ORDER BY username ASC");
$stmt->bind_param("i", $currentUser["id"]);
$stmt->execute();

$users = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home - SocialNet</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <?php include "../includes/menu.php"; ?>

    <div class="container">
        <h1>Home Page</h1>

        <div class="info-box">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($currentUser["username"]); ?></p>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($currentUser["fullname"]); ?></p>
        </div>

        <h2>Other Users</h2>

        <?php if ($users->num_rows > 0): ?>
            <ul class="user-list">
                <?php while ($user = $users->fetch_assoc()): ?>
                    <li>
                        <a href="/socialnet/profile.php?owner=<?php echo urlencode($user["username"]); ?>">
                            <?php echo htmlspecialchars($user["username"]); ?>
                        </a>
                        - <?php echo htmlspecialchars($user["fullname"]); ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No other users in the system yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
