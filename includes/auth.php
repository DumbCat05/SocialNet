<?php
ini_set("session.cookie_httponly", "0");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login() {
    if (!isset($_SESSION["user_id"])) {
        header("Location: /socialnet/signin.php");
        exit;
    }
}

function get_current_user_account($conn) {
    if (!isset($_SESSION["user_id"])) {
        return null;
    }

    $id = $_SESSION["user_id"];

    $stmt = $conn->prepare("SELECT id, username, fullname, description FROM account WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
?>
