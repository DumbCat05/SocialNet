<?php
require_once "../includes/db.php";
require_once "../includes/auth.php";
require_once "../includes/friends.php";

require_login();

$currentUser = get_current_user_account($conn);
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $requestId = intval($_POST["request_id"] ?? 0);
    $action = $_POST["action"] ?? "";

    if ($requestId > 0 && ($action === "accept" || $action === "reject")) {
        $newStatus = ($action === "accept") ? "accepted" : "rejected";

        $stmt = $conn->prepare(
            "UPDATE friend_request
             SET status = ?
             WHERE id = ? AND receiver_id = ? AND status = 'pending'"
        );

        $stmt->bind_param("sii", $newStatus, $requestId, $currentUser["id"]);

        if ($stmt->execute()) {
            $message = "Friend request updated.";
        } else {
            $message = "Failed to update friend request.";
        }
    }
}

$incomingStmt = $conn->prepare(
    "SELECT fr.id, a.username, a.fullname
     FROM friend_request fr
     JOIN account a ON fr.sender_id = a.id
     WHERE fr.receiver_id = ? AND fr.status = 'pending'
     ORDER BY fr.created_at DESC"
);
$incomingStmt->bind_param("i", $currentUser["id"]);
$incomingStmt->execute();
$incomingRequests = $incomingStmt->get_result();

$friendsStmt = $conn->prepare(
    "SELECT a.username, a.fullname
     FROM friend_request fr
     JOIN account a
        ON (
            (fr.sender_id = a.id AND fr.receiver_id = ?)
            OR
            (fr.receiver_id = a.id AND fr.sender_id = ?)
        )
     WHERE fr.status = 'accepted'
     ORDER BY a.username ASC"
);
$friendsStmt->bind_param("ii", $currentUser["id"], $currentUser["id"]);
$friendsStmt->execute();
$friends = $friendsStmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Friends - SocialNet</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <?php include "../includes/menu.php"; ?>

    <div class="container">
        <h1>Friends Page</h1>

        <?php if ($message !== ""): ?>
            <div class="message success">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <h2>Incoming Friend Requests</h2>

        <?php if ($incomingRequests->num_rows > 0): ?>
            <ul class="user-list">
                <?php while ($request = $incomingRequests->fetch_assoc()): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($request["username"]); ?></strong>
                        - <?php echo htmlspecialchars($request["fullname"]); ?>

                        <form method="post" action="" style="display:inline;">
                            <input type="hidden" name="request_id" value="<?php echo $request["id"]; ?>">
                            <button type="submit" name="action" value="accept">Accept</button>
                            <button type="submit" name="action" value="reject">Reject</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No incoming friend requests.</p>
        <?php endif; ?>

        <h2>Your Friends</h2>

        <?php if ($friends->num_rows > 0): ?>
            <ul class="user-list">
                <?php while ($friend = $friends->fetch_assoc()): ?>
                    <li>
                        <?php $profileToken = create_profile_view_token($friend["username"]); ?>

                        <a href="/socialnet/profile.php?owner=<?php echo urlencode($friend["username"]); ?>&CSRF=<?php echo urlencode($profileToken); ?>">
                            <?php echo htmlspecialchars($friend["username"], ENT_QUOTES, "UTF-8"); ?>
                        </a>
                        - <?php echo htmlspecialchars($friend["fullname"]); ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>You have no friends yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
