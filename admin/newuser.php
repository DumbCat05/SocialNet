<?php
require_once "../includes/db.php";

$message = "";
$messageClass = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $fullname = trim($_POST["fullname"] ?? "");
    $password = $_POST["password"] ?? "";
    $description = trim($_POST["description"] ?? "");

    if ($username === "" || $fullname === "" || $password === "") {
        $message = "Username, fullname, and password are required.";
        $messageClass = "error";
    } elseif (!preg_match("/^[a-zA-Z0-9_]{3,50}$/", $username)) {
        $message = "Username must be 3-50 characters and only contain letters, numbers, and underscore.";
        $messageClass = "error";
    } else {
        $check = $conn->prepare("SELECT id FROM account WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $checkResult = $check->get_result();

        if ($checkResult->num_rows > 0) {
            $message = "This username already exists.";
            $messageClass = "error";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO account (username, fullname, password, description) VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("ssss", $username, $fullname, $hashedPassword, $description);

            if ($stmt->execute()) {
                $message = "New user created successfully.";
                $messageClass = "success";
            } else {
                $message = "Failed to create user.";
                $messageClass = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - New User</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Admin Page</h1>
        <h2>Create New User</h2>

        <?php if ($message !== ""): ?>
            <div class="message <?php echo $messageClass; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <label>Username</label>
            <input type="text" name="username" required>

            <label>Full Name</label>
            <input type="text" name="fullname" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label>Description</label>
            <textarea name="description" placeholder="Profile page content"></textarea>

            <button type="submit">Create User</button>
        </form>

        <p>
            <a href="/socialnet/signin.php">Go to Sign In Page</a>
        </p>
    </div>
</body>
</html>
