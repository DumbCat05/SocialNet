<?php

ini_set("session.use_strict_mode", "1");
ini_set("session.cookie_httponly", "1");
ini_set("session.cookie_samesite", "Strict");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function generate_csrf_token() {
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }

    return $_SESSION["csrf_token"];
}

function verify_csrf_token($token) {
    if (empty($_SESSION["csrf_token"]) || empty($token)) {
        return false;
    }

    return hash_equals($_SESSION["csrf_token"], $token);
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