<?php
require_once "../includes/db.php";
require_once "../includes/auth.php";

require_login();

$currentUser = get_current_user_account($conn);
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!verify_csrf_token($_POST["csrf_token"] ?? "")) {
        http_response_code(403);
        exit("Invalid CSRF token.");
    }

    $description = $_POST["description"] ?? "";

    $stmt = $conn->prepare("UPDATE account SET description = ? WHERE id = ?");
    $stmt->bind_param("si", $description, $currentUser["id"]);

	if ($stmt->execute()) {
    		$message = "Profile content updated successfully.";
    		$currentUser["description"] = $description;
	} else {
    		$message = "Database error: " . $conn->error;
	}
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Setting - SocialNet</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <?php include "../includes/menu.php"; ?>

    <div class="container">
        <h1>Setting Page</h1>
        <h2>Edit Profile Page Content</h2>

        <?php if ($message !== ""): ?>
            <div class="message success">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="csrf_token"
                value="<?php echo htmlspecialchars(generate_csrf_token(), ENT_QUOTES, "UTF-8"); ?>">

            <label>Description</label>
            <textarea name="description"><?php echo htmlspecialchars($currentUser["description"] ?? "", ENT_QUOTES, "UTF-8"); ?></textarea>

            <button type="submit">Save</button>
        </form>
    </div>
</body>
</html>
