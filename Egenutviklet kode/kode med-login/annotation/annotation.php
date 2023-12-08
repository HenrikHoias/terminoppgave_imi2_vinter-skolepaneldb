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

// Konverterer klassenavnene til en kommaseparert streng
$classNamesString = implode("', '", $classNames);

// Henter visningsmodus fra GET-parametern eller bruker standardmodus "table"
$viewMode = isset($_GET['view']) ? $_GET['view'] : 'table';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
        crossorigin="anonymous">

    <!-- Font Awesome CSS -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../style.css"/>

    <title>Anmerkninger</title>
</head>

<body>
<nav class="fs-6 mb-5 text-dark custom-navlinks">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="../index.php">
            <div>
                <img id="logo" src="../images/eleva_alt_text_logo.svg" alt="Eleva" class="px-2"></img>
            </div>
        </a>
        <div class="d-flex d-lg-flex fs-6">
            <a href="../index.php" class="nav-link" style="display: inline-block; margin-right: 20px;">Forside</a>
            <a href="../absence/absence.php" class="nav-link" style="display: inline-block; margin-right: 20px;">Fravær</a>
            <a href="" class="nav-link" style="display: inline-block; margin-right: 20px;">Anmerkninger</a>
            <a href="" class="nav-link" style="display: inline-block;">Timeplan</a>
        </div>
        <div class="d-flex align-items-center">
            <div class="dropdown fs-5">
                <a class="nav-link dropdown-toggle-no-caret" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="margin-right: 20px;">
                    <i class="fas fa-bars"></i>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="../index.php">Forside</a>
                    <a class="dropdown-item" href="../absence/absence.php">Fravær</a>
                    <a class="dropdown-item" href="">Anmerkninger</a>
                    <a class="dropdown-item" href="">Timeplan</a>
                </div>
            </div>

            <div class="dropdown fs-5">
                <a class="nav-link dropdown-toggle-no-caret" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="margin-right: 20px;">
                    <i class="fas fa-circle-question"></i>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="../files/sluttbruker_eleva.pdf">Opplæringsmateriell</a>
                    <a class="dropdown-item" href="../index.php#kontakt">Kontakt oss</a>
                </div>
            </div>

            <div class="dropdown fs-5">
                <a class="nav-link dropdown-toggle-no-caret" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user"></i>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item"><?php echo $_SESSION['username'] ?></a>
                    <a class="dropdown-item" href="logout.php">Logg ut</a>
                </div>
            </div>
        </div>
    </div>
</nav>
<div class="container">
    <?php
    // Sjekker om det er en melding (msg) sendt via GET-parametern
    if (isset($_GET["msg"])) {
        $msg = $_GET["msg"]; // Henter meldingen fra GET-parametern
        // Skriver ut en advarsel (alert) med meldingen
        echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                ' . $msg . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
    }
    ?>
    <div class="text-center mb-4">
        <p class="text-muted">Trykk på + tegnet for å legge til en anmerkning</p>
    </div>

    <div class="row">
    <?php
        // SQL-spørring for å hente elever i klassene fra klassenavnene
        $sql = "SELECT * FROM `students` WHERE class IN ('$classNamesString')";
        $result = mysqli_query($conn, $sql);

        // Itererer gjennom resultatet av SQL-spørringen for å vise informasjon om hver elev
        while ($row = mysqli_fetch_assoc($result)) {
            $studentId = $row["id"];

            // SQL-spørring for å telle antall anmerkninger for den aktuelle eleven
            $annotationsCountSql = "SELECT COUNT(*) as count FROM annotations WHERE student_id = $studentId";
            $annotationsCountResult = mysqli_query($conn, $annotationsCountSql);
            $annotationsCountRow = mysqli_fetch_assoc($annotationsCountResult);
            $annotationsCount = $annotationsCountRow["count"];
        ?>
            <!-- Viser informasjonen om hver elev i en Bootstrap-kort (card) -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row["first_name"] . ' ' . $row["last_name"]; ?></h5>
                        <p class="card-text">Klasse: <?php echo $row["class"]; ?></p>
                        <p class="card-title text-muted">Anmerkninger:</p>
                        <h3 class="card-text"><?php echo $annotationsCount; ?></h3>
                        <div class="action-icons">
                            <!-- Lenker for å vise og opprette anmerkninger for den aktuelle eleven -->
                            <a href="view-annotation.php?id=<?php echo $row["id"] ?>&sort=subjectSort" class="link-dark"><i class="fa fa-inbox fs-5"></i></a>
                            <a href="create-annotation.php?id=<?php echo $row["id"] ?>" class="link-dark"><i class="fa fa-plus-square fs-5"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
    crossorigin="anonymous">
</script>

<script>
    function updateLogo() {
        var logo = document.getElementById("logo");
        if (window.innerWidth < 365) {
            logo.src = "../images/eleva_alt_logo.svg";
        } else {
            logo.src = "../images/eleva_alt_text_logo.svg";
        }
    }

    window.onload = updateLogo;
    window.onresize = updateLogo;
</script>

<footer class="text-light text-center py-3" style="margin-top:200px;background-color:#01364C;">
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> | Nulla facilisi. Mauris nulla diam, auctor at neque ut, convallis finibus erat. In porttitor id nulla id ullamcorper.</p>
    </div>
</footer>

</body>

</html>
