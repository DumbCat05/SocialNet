<?php
require_once "../includes/db.php";
require_once "../includes/auth.php";
require_once "../includes/friends.php";

require_login();

$currentUser = get_current_user_account($conn);
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $targetUserId = intval($_POST["target_user_id"] ?? 0);

    if ($targetUserId > 0 && $targetUserId != $currentUser["id"]) {
        $status = get_friend_status($conn, $currentUser["id"], $targetUserId);

        if ($status === "none") {
            $stmt = $conn->prepare(
                "INSERT INTO friend_request (sender_id, receiver_id, status)
                 VALUES (?, ?, 'pending')"
            );
            $stmt->bind_param("ii", $currentUser["id"], $targetUserId);

            if ($stmt->execute()) {
                $message = "Friend request sent.";
            } else {
                $message = "Failed to send friend request.";
            }
        } else {
            $message = "A friend request or friendship already exists.";
        }
    }
}

$stmt = $conn->prepare(
    "SELECT id, username, fullname
     FROM account
     WHERE id != ?
     ORDER BY username ASC"
);
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

        <?php if ($message !== ""): ?>
            <div class="message success">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="info-box">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($currentUser["username"]); ?></p>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($currentUser["fullname"]); ?></p>
        </div>

        <h2>Other Users</h2>

        <?php if ($users->num_rows > 0): ?>
            <ul class="user-list">
                <?php while ($user = $users->fetch_assoc()): ?>
                    <?php $status = get_friend_status($conn, $currentUser["id"], $user["id"]); ?>

                    <li>
                        <strong><?php echo htmlspecialchars($user["username"]); ?></strong>
                        - <?php echo htmlspecialchars($user["fullname"]); ?>

                        <?php if ($status === "friends"): ?>
                            <?php $profileToken = create_profile_view_token($friend["username"]); ?>

                            <a href="/socialnet/profile.php?csrf_token=<?php echo urlencode($profileToken); ?>">
                                View Profile
                            </a>
                        <?php elseif ($status === "pending_sent"): ?>
                            <span>Friend request sent</span>
                        <?php elseif ($status === "pending_received"): ?>
                            <a href="/socialnet/friends.php">Respond to request</a>
                        <?php else: ?>
                            <form method="post" action="" style="display:inline;">
                                <input type="hidden" name="target_user_id" value="<?php echo $user["id"]; ?>">
                                <button type="submit">Add Friend</button>
                            </form>
                        <?php endif; ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No other users in the system yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
