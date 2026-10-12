
<?php
include "db.php";

if (isset($_POST["update_progress"])) {

    $id = (int) $_POST["course_id"];
    $completed = (int) $_POST["completed_topics"];

    $sql = "UPDATE courses
            SET completed_topics = ?
            WHERE id = ?
            AND ? <= total_topics";

    $stmt = mysqli_prepare($connection, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "iii",
        $completed,
        $id,
        $completed
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    header("Location: index.php");
    exit;
}
?>
