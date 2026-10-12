
<?php
$connection = mysqli_connect("localhost", "root", "", "course_monitor");

if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>

