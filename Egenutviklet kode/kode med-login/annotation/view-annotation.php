<?php
session_start();
include "../db_conn.php";

// Sjekker om brukeren er logget inn
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Sjekker om 'id'-parameteren er satt og er numerisk
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    echo "Ugyldig ID-parameter.";
    exit();
}

$id = $_GET["id"];

// Sjekker om 'sort' er satt, ellers setter en standard sorteringstype
if (!isset($_GET["sort"])) {
    $sortType = "dateSort"; // Setter en standard sorteringstype hvis den ikke er definert
} else {
    $sortType = $_GET["sort"];
}

// Setter opp sortering basert på 'sort' parameteren
if ($sortType === "subjectSort") {
    $orderBy = "subject_type ASC";
} elseif ($sortType === "dateSort") {
    $orderBy = "STR_TO_DATE(annotation_date, '%d.%m.%Y') DESC";
} else {
    $orderBy = "STR_TO_DATE(annotation_date, '%d.%m.%Y') DESC"; // Standard sorteringstype
}

// Henter informasjon om studenten
$sql = "SELECT * FROM `students` WHERE id = $id LIMIT 1";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

// Sjekker om det er funnet en student med angitt ID
if (!$row) {
    echo "Ingen student funnet med ID: " . $id;
    exit();
}

$usernameOrMail = $_SESSION['username'];

// Henter brukerens tilgangsnivå
$sqlUserAccess = "SELECT access FROM users WHERE username = '$usernameOrMail' OR mail = '$usernameOrMail'";
$resultUserAccess = mysqli_query($conn, $sqlUserAccess);

// Sjekker om det oppstår feil ved henting av tilgangsnivå
if (!$resultUserAccess) {
    die("Feil ved henting av brukerens tilgangsnivå: " . mysqli_error($conn));
}

// Henter brukerdata
$userData = mysqli_fetch_assoc($resultUserAccess);

// Sjekker om det ikke finnes brukerdata for brukernavnet eller e-postadressen
if (!$userData) {
    die("Ingen brukerdata funnet for brukernavnet eller e-postadressen: " . $usernameOrMail);
}

// Deler tilgangsnivået ved bindestrek for å få klassene brukeren har tilgang til
$accessLevels = explode("-", $userData['access']);
$classNames = array();

// Henter klassenavn basert på tilgangsnivået
foreach ($accessLevels as $level) {
    if ($level === '*') {
        $sqlClassName = "SELECT class_name FROM classes";
    } else {
        $sqlClassName = "SELECT class_name FROM classes WHERE id = $level";
    }

    // Utfører spørringen for å hente klassenavn
    $resultClassName = mysqli_query($conn, $sqlClassName);

    // Sjekker om det oppstår feil ved henting av klassenavn
    if (!$resultClassName) {
        die("Feil ved henting av klasse: " . mysqli_error($conn));
    }

    // Legger til klassenavn i arrayet
    while ($dataClassName = mysqli_fetch_assoc($resultClassName)) {
        array_push($classNames, $dataClassName['class_name']);
    }
}

// Sjekker om brukeren har tilgang til studentens side basert på klassen
if (!in_array($row["class"], $classNames)) {
    header("Location: annotation.php?msg=Du har ikke tilgang til denne siden for denne eleven!");
    exit();
}

// Henter alle anmerkninger for studenten basert på 'id' og sortering
$annotationsSql = "SELECT * FROM annotations WHERE student_id = $id ORDER BY $orderBy";
$annotationsResult = mysqli_query($conn, $annotationsSql);
$totalAnnotations = mysqli_num_rows($annotationsResult);

// Funksjon for å tildele en CSS-klasse basert på faget
function subjectColor($subjectType) {
    switch($subjectType) {
        case 'Engelsk':
            return 'callout-blue';
        case 'Mattematikk':
            return 'callout-green';
        case 'Norsk':
            return 'callout-orange';
        default:
            return 'callout-default';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anmerkning Historikk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <!-- Legg til Bootstrap-ikonbiblioteket -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../style.css"/>
    <style>
        .callout-orange {
            border-left: 5px solid #FFA500; /* Oransj for Norsk */
        }

        .callout-green {
            border-left: 5px solid #28a745; /* Grønn for Mattematikk */
        }

        .callout-blue {
            border-left: 5px solid #007bff; /* Blå for Engelsk */
        }

        .callout-default {
            border-left: 5px solid #ccc; /* Standard ellers */
        }
        @media screen and (max-width: 768px) {
            #small-btn-back {
                display: none;
            }

            p[style="margin-right:100px;"] {
                margin-right: 0 !important; /* Legg til !important for å overstyre andre stiler */
            }
        }
        @media screen and (min-width: 768px) {
            #big-btn-back {
                display: none;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-light justify-content-center fs-3 mb-5" style="background-color: #1099B9; color: #FFF;">
        Anmerkninger
    </nav>

    <div class="container">
        <?php
        if (isset($_GET["msg"])) {
                $msg = $_GET["msg"];
                echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                    ' . $msg . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            }
        ?>
        <a href="annotation.php" id="big-btn-back" class="btn btn-outline-dark w-100 mb-3"><i class="bi bi-arrow-left"></i> Tilbake</a> 
        <div class="d-flex align-items-center mb-4">
            <a href="annotation.php" id="small-btn-back" class="btn btn-outline-dark"><i class="bi bi-arrow-left"></i> Tilbake</a> 
            <div class="mx-auto text-center">
                <h3>Anmerkning Historikk</h3>
                <p class="text-muted">Anmerkninger for <?php echo $row["first_name"] . ' ' . $row["last_name"]?> i klasse <?php echo $row["class"]?></p>
            </div>
            <div>
                <p style="margin-right:100px;"></p>
            </div> <!-- Tomt rom til høyre for at overskriften skal være sentrert -->
        </div>
        <div class="mx-auto text-center">
                <p class="text-muted">Sorter etter:</p>
        </div>
        <div class="btn-group mb-3 d-flex">
            <a href="view-annotation.php?id=<?php echo $row["id"] ?>&sort=subjectSort" class="btn btn-outline-dark" id="fagnavn-btn"><i class="fa-solid fa-arrow-down-a-z"></i> Fagnavn</a>
            <a href="view-annotation.php?id=<?php echo $row["id"] ?>&sort=dateSort" class="btn btn-outline-dark"><i class="fas fa-calendar-alt"></i> Dato</a>
        </div>

        <div class="container">
        <?php
        if (mysqli_num_rows($annotationsResult) > 0) {
            while ($annotationRow = mysqli_fetch_assoc($annotationsResult)) {
        ?>
                <div class="card mb-3 <?php echo subjectColor($annotationRow["subject_type"]); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $annotationRow["subject_type"]; ?></h5>
                        <p class="card-text"><?php echo $annotationRow["annotation_text"]; ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="card-text"><small class="text-muted">Dato: <?php echo $annotationRow["annotation_date"]; ?></small></p>
                            <div>
                                <a href="edit-annotation.php?id=<?php echo $annotationRow['annotation_id']; ?>" class="btn btn-dark"><i class="bi bi-pencil"></i> Endre</a>
                                <a href="delete-annotation.php?id=<?php echo $annotationRow['annotation_id']; ?>" class="btn btn-dark"><i class="bi bi-trash"></i> Slett</a>
                            </div>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo '<div class="alert alert-info" role="alert">Ingen anmerkninger tilgjengelig for denne studenten.</div>';
        }
        ?>
    </div>
    </div>

    <div class="mx-auto text-center mt-5">
        <h3>Anmerkninger totalt: <?php echo $totalAnnotations; ?></h3>
    </div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous">
    </script>

    <footer class="bg-dark text-light text-center py-3" style="margin-top:200px;">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> | Nulla facilisi. Mauris nulla diam, auctor at neque ut, convallis finibus erat. In porttitor id nulla id ullamcorper.</p>
        </div>
    </footer>
</body>

</html>
