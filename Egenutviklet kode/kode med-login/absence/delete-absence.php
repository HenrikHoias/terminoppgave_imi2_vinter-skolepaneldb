<?php
session_start(); // Starter økten for å spore brukerens påloggingsstatus

include "../db_conn.php"; // Inkluderer filen for databaseforbindelse

// Viderekobler til påloggingssiden hvis brukeren ikke er pålogget
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Henter brukernavnet eller e-postadressen fra sesjonen
$usernameOrMail = $_SESSION['username'];

// Henter tilgangsnivået for brukeren fra databasen
$sqlUserAccess = "SELECT access FROM users WHERE username = '$usernameOrMail' OR mail = '$usernameOrMail'";
$resultUserAccess = mysqli_query($conn, $sqlUserAccess);

// Håndterer feil hvis tilgangsnivået ikke kan hentes
if (!$resultUserAccess) {
    die("Feil ved henting av brukerens tilgangsnivå: " . mysqli_error($conn));
}

// Henter brukerdata fra resultatet av spørringen
$userData = mysqli_fetch_assoc($resultUserAccess);

// Håndterer feil hvis ingen brukerdata er funnet for brukernavnet eller e-postadressen
if (!$userData) {
    die("Ingen brukerdata funnet for brukernavnet eller e-postadressen: " . $usernameOrMail);
}

// Deler tilgangsnivåene og lager et array med klasse-navn
$accessLevels = explode("-", $userData['access']);
$classNames = array();

// Henter klassenavn basert på tilgangsnivået og legger dem til i arrayet
foreach ($accessLevels as $level) {
    if ($level === '*') {
        $sqlClassName = "SELECT class_name FROM classes";
    } else {
        $sqlClassName = "SELECT class_name FROM classes WHERE id = $level";
    }
    $resultClassName = mysqli_query($conn, $sqlClassName);

    // Håndterer feil hvis klassenavnet ikke kan hentes
    if (!$resultClassName) {
        die("Feil ved henting av klasse: " . mysqli_error($conn));
    }

    while ($dataClassName = mysqli_fetch_assoc($resultClassName)) {
        array_push($classNames, $dataClassName['class_name']);
    }
}

// Henter ID fra GET-parametern
$id = $_GET["id"];

// Sjekker om brukeren har tilgang til å slette den valgte eleven og dens anmerkninger
$sqlCheckAccess = "SELECT * FROM `students` WHERE id = $id LIMIT 1";
$resultCheckAccess = mysqli_query($conn, $sqlCheckAccess);
$rowCheckAccess = mysqli_fetch_assoc($resultCheckAccess);

// Viderekobler og gir feilmelding hvis brukeren ikke har tilgang
if (!$rowCheckAccess || !in_array($rowCheckAccess["class"], $classNames)) {
    header("Location: absence.php?msg=Du har ikke tilgang til å slette denne eleven og tilhørende anmerkninger!");
    exit();
}

// Sletter anmerkninger tilknyttet eleven
$deleteAnnotationsSql = "DELETE FROM `annotations` WHERE student_id = $id";
$deleteAnnotationsResult = mysqli_query($conn, $deleteAnnotationsSql);

// Sletter eleven fra databasen
$deleteStudentSql = "DELETE FROM `students` WHERE id = $id";
$deleteStudentResult = mysqli_query($conn, $deleteStudentSql);

// Viderekobler med melding om vellykket sletting eller skriver ut feilmelding
if ($deleteStudentResult && $deleteAnnotationsResult) {
    header("Location: absence.php?msg=Eleven og tilhørende anmerkninger er slettet fra registeret");
} else {
    echo "Feil: " . mysqli_error($conn);
}
?>
