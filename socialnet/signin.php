<?php
require_once "../includes/db.php";

ini_set("session.cookie_httponly", "0");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION["user_id"])) {
    header("Location: /socialnet/index.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $message = "Please enter username and password.";
    } else {
        /*
            WARNING:
            This query is intentionally vulnerable for ATT-5 SQL Injection demo.
            Do not use this version as the final secure version.
        */
        $sql = "SELECT id, username, fullname, password FROM account WHERE username = '$username'";

        $result = $conn->query($sql);

        if (!$result) {
            die("SQL Error: " . htmlspecialchars($conn->error));
        }

        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["fullname"] = $user["fullname"];

            header("Location: /socialnet/index.php");
            exit;
        } else {
            $message = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign In - SocialNet</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <div class="container">
        <h1>SocialNet</h1>
        <h2>Sign In</h2>

        <?php if ($message !== ""): ?>
            <div class="message error">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <label>Username</label>
            <input type="text" name="username" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Sign In</button>
        </form>

        <p>
            No user yet? Create one at
            <a href="/admin/newuser.php">Admin New User Page</a>.
        </p>
    </div>
</body>
</html>
