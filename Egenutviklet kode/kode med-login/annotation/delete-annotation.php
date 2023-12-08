<?php
session_start();
include "../db_conn.php";

// Sjekker om brukeren er logget inn
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$usernameOrMail = $_SESSION['username'];

// Sjekker om 'id'-parameteren er satt i URL
if (isset($_GET['id'])) {
    $annotationId = $_GET['id'];
    $username = $_SESSION['username'];

    // Henter brukerens tilgangsnivå
    $userAccessQuery = "SELECT access FROM users WHERE username = '$usernameOrMail' OR mail = '$usernameOrMail'";
    $result = mysqli_query($conn, $userAccessQuery);
    $row = mysqli_fetch_assoc($result);
    $userAccess = explode("-", $row['access']); // Del access-verdien ved bindestrek

    // Henter student_id for anmerkningen
    $studentIdQuery = "SELECT student_id FROM annotations WHERE annotation_id = $annotationId";
    $result = mysqli_query($conn, $studentIdQuery);
    $row = mysqli_fetch_assoc($result);
    $studentId = $row['student_id'];

    // Henter klassens ID til studenten
    $classQuery = "SELECT class FROM students WHERE id = $studentId";
    $result = mysqli_query($conn, $classQuery);
    $row = mysqli_fetch_assoc($result);
    $class = $row['class'];

    // Henter klassens ID fra klassenavnet
    $classIdQuery = "SELECT id FROM classes WHERE class_name = '$class'";
    $result = mysqli_query($conn, $classIdQuery);
    $row = mysqli_fetch_assoc($result);
    $classId = $row['id'];

    // Sjekker om brukeren har tilgang til klassen
    $hasAccess = false;
    foreach ($userAccess as $access) {
        if ($access === $classId || $access === '*') {
            $hasAccess = true;
            break;
        }
    }

    // Sletter anmerkningen hvis brukeren har tilgang, ellers vis feilmelding
    if ($hasAccess) {
        $deleteSql = "DELETE FROM annotations WHERE annotation_id = $annotationId";
        $result = mysqli_query($conn, $deleteSql);

        if ($result) {
            header("Location: {$_SERVER['HTTP_REFERER']}&msg=Anmerkningen hos medeleven er slettet");
            exit();
        } else {
            echo "Feil ved sletting av anmerkning: " . mysqli_error($conn);
        }
    } else {
        header("Location: annotation.php?msg=Du kan ikke slette denne anmerkningen!");
        exit();
    }

    mysqli_close($conn);
} else {
    echo "Ugyldig id";
    exit();
}
?>
