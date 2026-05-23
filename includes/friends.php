<?php
function are_friends($conn, $userId1, $userId2) {
    if ($userId1 == $userId2) {
        return true;
    }

    $stmt = $conn->prepare(
        "SELECT id FROM friend_request
         WHERE status = 'accepted'
         AND (
            (sender_id = ? AND receiver_id = ?)
            OR
            (sender_id = ? AND receiver_id = ?)
         )"
    );

    $stmt->bind_param("iiii", $userId1, $userId2, $userId2, $userId1);
    $stmt->execute();

    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function get_friend_status($conn, $currentUserId, $targetUserId) {
    if ($currentUserId == $targetUserId) {
        return "self";
    }

    $stmt = $conn->prepare(
        "SELECT sender_id, receiver_id, status FROM friend_request
         WHERE
            (sender_id = ? AND receiver_id = ?)
            OR
            (sender_id = ? AND receiver_id = ?)
         LIMIT 1"
    );

    $stmt->bind_param("iiii", $currentUserId, $targetUserId, $targetUserId, $currentUserId);
    $stmt->execute();

    $result = $stmt->get_result();
    $request = $result->fetch_assoc();

    if (!$request) {
        return "none";
    }

    if ($request["status"] === "accepted") {
        return "friends";
    }

    if ($request["status"] === "pending") {
        if ($request["sender_id"] == $currentUserId) {
            return "pending_sent";
        } else {
            return "pending_received";
        }
    }

    return "none";
}
?>
