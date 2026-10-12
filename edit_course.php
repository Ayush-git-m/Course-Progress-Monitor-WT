<?php
include "db.php";

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET["id"];

$sql = "SELECT * FROM courses WHERE id = ?";
$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$course = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$course) {
    die("Course not found.");
}

if (isset($_POST["update_course"])) {
    $courseName = trim($_POST["course_name"]);
    $totalTopics = (int) $_POST["total_topics"];
    $completedTopics = (int) $_POST["completed_topics"];

    if (
        $courseName !== "" &&
        $totalTopics > 0 &&
        $completedTopics >= 0 &&
        $completedTopics <= $totalTopics
    ) {
        $sql = "UPDATE courses
                SET course_name = ?,
                    total_topics = ?,
                    completed_topics = ?
                WHERE id = ?";

        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "siii",
            $courseName,
            $totalTopics,
            $completedTopics,
            $id
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: index.php");
        exit;
    } else {
        $error = "Please enter valid course details.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>Edit Course</h1>
    <p>Update your course details</p>
</header>

<div class="container">
    <h2>Course Details</h2>

    <?php if (isset($error)) { ?>
        <p style="color: red;">
            <?php echo htmlspecialchars($error); ?>
        </p>
    <?php } ?>

    <form method="POST">
        <label for="course_name">Course Name:</label>
        <input
            type="text"
            id="course_name"
            name="course_name"
            maxlength="100"
            value="<?php echo htmlspecialchars(
                $_POST["course_name"] ?? $course["course_name"]
            ); ?>"
            required
        >

        <label for="total_topics">Total Topics:</label>
        <input
            type="number"
            id="total_topics"
            name="total_topics"
            min="1"
            max="1000"
            value="<?php echo htmlspecialchars(
                (string)($_POST["total_topics"] ?? $course["total_topics"])
            ); ?>"
            required
        >

        <label for="completed_topics">Completed Topics:</label>
        <input
            type="number"
            id="completed_topics"
            name="completed_topics"
            min="0"
            max="1000"
            value="<?php echo htmlspecialchars(
                (string)($_POST["completed_topics"] ?? $course["completed_topics"])
            ); ?>"
            required
        >

        <button type="submit" name="update_course">
            Save Changes
        </button>


        <a href="index.php" class="cancel-btn">Cancel</a>

    </form>
</div>

</body>
</html>

