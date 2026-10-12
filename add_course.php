
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST"
    && isset($_POST["add_course"])) {

    $userId = (int) $_SESSION["user_id"];
    $courseName = trim($_POST["course_name"]);
    $totalTopics = (int) $_POST["total_topics"];

    if ($courseName !== "" && $totalTopics >= 1 && $totalTopics <= 1000) {

        $sql = "INSERT INTO courses
                (course_name, total_topics, completed_topics, user_id)
                VALUES (?, ?, 0, ?)";

        $stmt = mysqli_prepare($connection, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "sii",
            $courseName,
            $totalTopics,
            $userId
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: index.php");
            exit;
        } else {
            mysqli_stmt_close($stmt);
            die("Course could not be added. Please try again.");
        }

    } else {
        die("Please enter a valid course name and topic count.");
    }
}

header("Location: index.php");
exit;
?>
