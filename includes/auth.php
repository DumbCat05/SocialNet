<?php

ini_set("session.use_strict_mode", "1");
ini_set("session.cookie_httponly", "1");
ini_set("session.cookie_samesite", "Strict");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function create_profile_view_token($owner) {
    if (!isset($_SESSION["profile_view_tokens"])) {
        $_SESSION["profile_view_tokens"] = [];
    }

    // Remove expired tokens
    foreach ($_SESSION["profile_view_tokens"] as $token => $data) {
        if ($data["expires"] < time()) {
            unset($_SESSION["profile_view_tokens"][$token]);
        }
    }

    // Token has extra random string and is hard to guess/decode
    $token = "pv_" . bin2hex(random_bytes(16)) . "_" . bin2hex(random_bytes(16));

    $_SESSION["profile_view_tokens"][$token] = [
        "owner" => $owner,
        "expires" => time() + 600
    ];

    return $token;
}

function verify_profile_view_token($token) {
    if (
        empty($token) ||
        !isset($_SESSION["profile_view_tokens"]) ||
        !isset($_SESSION["profile_view_tokens"][$token])
    ) {
        return false;
    }

    $data = $_SESSION["profile_view_tokens"][$token];

    if ($data["expires"] < time()) {
        unset($_SESSION["profile_view_tokens"][$token]);
        return false;
    }

    return $data["owner"];
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