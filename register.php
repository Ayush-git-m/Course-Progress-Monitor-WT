<?php
session_start();
include "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if ($name == "" || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || strlen($password) < 8) {

        $error = "Enter valid details. Password must be at least 8 characters.";

    } else {

        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {

            $error = "This email is already registered.";

        } else {

            mysqli_stmt_close($stmt);
            $stmt = null;

            // Check whether this is the first user
            $result = mysqli_query($connection, "SELECT COUNT(*) AS total FROM users");
            $row = mysqli_fetch_assoc($result);
            $isFirstUser = ((int)$row["total"] === 0);

            // Securely hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (name, email, password)
                    VALUES (?, ?, ?)";

            $stmt = mysqli_prepare($connection, $sql);
            mysqli_stmt_bind_param(
                $stmt,
                "sss",
                $name,
                $email,
                $hashedPassword
            );

            if (mysqli_stmt_execute($stmt)) {

                $newUserId = mysqli_insert_id($connection);

                // Give the existing courses to the first account
                if ($isFirstUser) {
                    $sql = "UPDATE courses
                            SET user_id = ?
                            WHERE user_id IS NULL";

                    $updateStmt = mysqli_prepare($connection, $sql);
                    mysqli_stmt_bind_param($updateStmt, "i", $newUserId);
                    mysqli_stmt_execute($updateStmt);
                    mysqli_stmt_close($updateStmt);
                }

                mysqli_stmt_close($stmt);

                header("Location: login.php?registered=1");
                exit;

            } else {
                $error = "Registration failed. Please try again.";
            }
        }

        if ($stmt !== null) {
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Course Progress Monitor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Create Account</h1>
    <p>Start tracking your learning journey!</p>
</header>

<div class="container">

    <h2>Register</h2>

    <?php if ($error != "") { ?>
        <p style="color: red;">
            <?php echo htmlspecialchars($error); ?>
        </p>
    <?php } ?>

    <form method="POST" action="register.php">

        <label for="name">Full Name:</label>
        <input
            type="text"
            id="name"
            name="name"
            maxlength="100"
            required
        >

        <label for="email">Email:</label>
        <input
            type="email"
            id="email"
            name="email"
            maxlength="150"
            required
        >

        <label for="password">Password:</label>
        <input
            type="password"
            id="password"
            name="password"
            minlength="8"
            required
        >

        <button type="submit">Create Account</button>

        <p>Already have an account? <a href="login.php">Login here</a></p>

    </form>

</div>

</body>
</html>