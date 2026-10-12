
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

include "db.php";


// Get all courses from database
$userId = (int) $_SESSION["user_id"];

$sql = "SELECT * FROM courses
        WHERE user_id = ?
        ORDER BY id ASC";

$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$courses = [];
$totalCourses = 0;
$completed = 0;
$inProgress = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $courses[] = $row;
    $totalCourses++;

    if ($row["completed_topics"] >= $row["total_topics"]) {
        $completed++;
    } else {
        $inProgress++;
    }
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Course Progress Monitor</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<header>
    <h1>Course Progress Monitor</h1>
    <p>Track your learning journey!</p>
</header>

<div class="container">

    <!-- Dashboard Summary -->
    <h2>My Dashboard</h2>

    <div class="cards">

        <div class="card">
            <h3>Total Courses</h3>
            <p><?php echo $totalCourses; ?></p>
        </div>

        <div class="card">
            <h3>Completed</h3>
            <p><?php echo $completed; ?></p>
        </div>

        <div class="card">
            <h3>In Progress</h3>
            <p><?php echo $inProgress; ?></p>
        </div>

    </div>


    <!-- Update Existing Course Progress -->
    <h2>Update Progress</h2>

    <form action="update_progress.php" method="POST">

        <label for="course_id">Select Course:</label>

        <select name="course_id" id="course_id"
                onchange="updateTopicInfo()" required>

            <?php foreach ($courses as $course) { ?>

                <option
                    value="<?php echo $course['id']; ?>"
                    data-total="<?php echo $course['total_topics']; ?>"
                    data-done="<?php echo $course['completed_topics']; ?>">

                    <?php echo htmlspecialchars($course['course_name']); ?>

                </option>

            <?php } ?>

        </select>

        <label for="completed_topics">Completed Topics:</label>

        <input type="number"
               name="completed_topics"
               id="completed_topics"
               min="0"
               required
               <?php echo empty($courses) ? 'disabled' : ''; ?>>

        <p id="topicInfo"></p>

        <button type="submit"
                name="update_progress"
                <?php echo empty($courses) ? 'disabled' : ''; ?>>

            Update Progress

        </button>

    </form>


    <!-- Add New Course -->
    <h2>Add New Course</h2>

    <form action="add_course.php" method="POST">

        <label for="course_name">Course Name:</label>

        <input type="text"
               name="course_name"
               id="course_name"
               maxlength="100"
               placeholder="e.g. HTML, CSS, PHP"
               required>

        <label for="total_topics">Total Topics:</label>

        <input type="number"
               name="total_topics"
               id="total_topics"
               min="1"
               max="1000"
               placeholder="e.g. 10"
               required>

        <button type="submit" name="add_course">
            Add Course
        </button>

    </form>
<h2>My Courses</h2>

<?php if (empty($courses)) { ?>
    <p>No courses added yet. Add your first course above!</p>
<?php } ?>

<div class="course-list">

<?php foreach ($courses as $course) { ?>

    <?php
    $total = (int) $course["total_topics"];
    $done = (int) $course["completed_topics"];
    $progress = ($total > 0)
        ? round(($done / $total) * 100)
        : 0;
    ?>

    <div class="course">

        <div class="course-header">
            <h3><?php echo htmlspecialchars($course["course_name"]); ?></h3>
            <span><?php echo $progress; ?>% Complete</span>
        </div>

        <progress value="<?php echo $progress; ?>" max="100"></progress>

        <p>
            <?php echo $done; ?> out of <?php echo $total; ?> topics completed
        </p>

        <div class="course-actions">
            <a class="edit-btn"
               href="edit_course.php?id=<?php echo (int) $course['id']; ?>">
                Edit Course
            </a>

            <form action="delete_course.php" method="POST"
                  onsubmit="return confirm('Are you sure you want to delete this course?');">

                <input type="hidden" name="course_id"
                       value="<?php echo (int) $course['id']; ?>">

                <button type="submit" name="delete_course" class="delete-btn">
                    Delete Course
                </button>
            </form>
        </div>

    </div>

<?php } ?>

</div>
<script>
function updateTopicInfo() {

    var select = document.getElementById("course_id");
    var input = document.getElementById("completed_topics");
    var info = document.getElementById("topicInfo");

    if (select.options.length === 0) {
        info.innerText = "Please add a course first.";
        return;
    }

    var option = select.options[select.selectedIndex];

    var total = Number(option.dataset.total);
    var done = Number(option.dataset.done);

    input.max = total;
    input.value = done;

    info.innerText = "Completed: " + done + " / " + total +
                     " topics. You can enter 0 to " + total + ".";
}

updateTopicInfo();
</script>

</body>
</html>
