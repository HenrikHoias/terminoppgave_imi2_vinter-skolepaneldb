<?php
include "db_conn.php";
$id = $_GET["id"];
$sql = "DELETE FROM `students` WHERE id = $id";
$result = mysqli_query($conn, $sql);

if ($result) {
  header("Location: index.php?msg=Eleven er slettet fra registeret");
} else {
  echo "Failed: " . mysqli_error($conn);
}
