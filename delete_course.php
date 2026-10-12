
<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST"
    && isset($_POST["delete_course"])) {

    $id = (int) $_POST["course_id"];

    if ($id > 0) {

        $sql = "DELETE FROM courses WHERE id = ?";

        $stmt = mysqli_prepare($connection, $sql);

        mysqli_stmt_bind_param($stmt, "i", $id);

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
    }

    header("Location: index.php");
    exit;
}

header("Location: index.php");
exit;
?>
