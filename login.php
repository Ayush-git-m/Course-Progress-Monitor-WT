
<?php
session_start();
include "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $sql = "SELECT id, name, email, password FROM users WHERE email = ?";
    $stmt = mysqli_prepare($connection, $sql);

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if ($user && password_verify($password, $user["password"])) {

        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user_name"] = $user["name"];

        header("Location: index.php");
        exit;

    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Course Progress Monitor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Welcome Back!</h1>
    <p>Login to your Course Progress Monitor</p>
</header>

<div class="container">

    <h2>Login</h2>

    <?php if (isset($_GET["registered"])) { ?>
        <p style="color: green;">
            Registration successful! Please log in.
        </p>
    <?php } ?>

    <?php if ($error != "") { ?>
        <p style="color: red;">
            <?php echo htmlspecialchars($error); ?>
        </p>
    <?php } ?>

    <form method="POST" action="login.php">

        <label for="email">Email:</label>
        <input
            type="email"
            id="email"
            name="email"
            required
        >

        <label for="password">Password:</label>
        <input
            type="password"
            id="password"
            name="password"
            required
        >

        <button type="submit">Login</button>

        <p>
            Don't have an account?
            <a href="register.php">Register here</a>
        </p>

    </form>

</div>
</body>
</html>
