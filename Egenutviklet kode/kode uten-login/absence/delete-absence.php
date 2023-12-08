<?php
include "../db_conn.php";
$id = $_GET["id"];

// Sletter anmerkninger assosiert med eleven i "annotations"
$deleteAnnotationsSql = "DELETE FROM `annotations` WHERE student_id = $id";
$deleteAnnotationsResult = mysqli_query($conn, $deleteAnnotationsSql);

// Sletter eleven fra elevtabellen i "students"
$deleteStudentSql = "DELETE FROM `students` WHERE id = $id";
$deleteStudentResult = mysqli_query($conn, $deleteStudentSql);

if ($deleteStudentResult && $deleteAnnotationsResult) {
  header("Location: absence.php?msg=Eleven og tilhørende anmerkninger er slettet fra registeret");
} else {
  echo "Failed: " . mysqli_error($conn);
}
?>
