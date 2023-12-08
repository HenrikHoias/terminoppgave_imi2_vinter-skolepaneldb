<?php
session_start(); // Starter økten for å spore brukerens påloggingsstatus

include "../db_conn.php"; // Inkluderer filen for databaseforbindelse

// Viderekobler til påloggingssiden hvis brukeren ikke er pålogget
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Funksjon for å bestemme CSS-klassen basert på typen fravær
function getFravaerClass($fravaer) {
    // Returnerer CSS-klasser basert på typen fravær
    if ($fravaer == "Til stede") {
        return "text-success";
    } elseif ($fravaer == "Dokumentert") {
        return "text-warning";  
    } elseif ($fravaer == "Udokumentert") {
        return "text-danger";
    } else {
        return ""; 
    }
}

// Henter brukerens tilgangsnivå fra databasen
$usernameOrMail = $_SESSION['username'];
$sqlUserAccess = "SELECT access FROM users WHERE username = '$usernameOrMail' OR mail = '$usernameOrMail'";
$resultUserAccess = mysqli_query($conn, $sqlUserAccess);

// Håndterer feil hvis brukerens tilgangsnivå ikke kan hentes
if (!$resultUserAccess) {
    die("Feil ved henting av brukerens tilgangsnivå: " . mysqli_error($conn));
}

// Henter brukerdata
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
    if(!$resultClassName) {
        die("Feil ved henting av klasse: " . mysqli_error($conn));
    }

    while ($dataClassName = mysqli_fetch_assoc($resultClassName)) {
        array_push($classNames, $dataClassName['class_name']);
    }
}

// Konverterer klassenavnene til en kommaseparert streng
$classNamesString = implode("', '", $classNames);

// Bestemmer visningsmodus basert på GET-parameteren eller bruker standard 'table'
$viewMode = isset($_GET['view']) ? $_GET['view'] : 'table';

// Behandler POST-forespørsler for å oppdatere fraværsstatus
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["merk-til-stede"])) {
        // Oppdaterer fraværsstatus til 'Til stede' for elever i de gitte klassene
        $updateQuery = "UPDATE `students` SET `absence` = 'Til stede' WHERE class IN ('$classNamesString')";
        mysqli_query($conn, $updateQuery);

        // Nullstiller kommentarene for de gitte klassene
        $deleteCommentsQuery = "UPDATE `students` SET `comment` = NULL WHERE class IN ('$classNamesString')";
        mysqli_query($conn, $deleteCommentsQuery);

        // Viderekobler til fraværssiden med melding og valgt visningsmodus
        header("Location: absence.php?msg=Alle elever er merket til stede&view=$viewMode");
        exit();
    } elseif (isset($_POST["nullstill-fravaer"])) {
        // Nullstiller fraværsstatus for elever i de gitte klassene
        $updateQuery = "UPDATE `students` SET `absence` = 'Ingen' WHERE class IN ('$classNamesString')";
        mysqli_query($conn, $updateQuery);

        // Nullstiller kommentarene for de gitte klassene
        $deleteCommentsQuery = "UPDATE `students` SET `comment` = NULL WHERE class IN ('$classNamesString')";
        mysqli_query($conn, $deleteCommentsQuery);

        // Viderekobler til fraværssiden med melding og valgt visningsmodus
        header("Location: absence.php?msg=Fraværet til alle elever er nullstilt&view=$viewMode");
        exit();
    }
}

// Henter studentdata basert på de gitte klassene
$sqlStudents = "SELECT * FROM students WHERE class IN ('$classNamesString')";
$resultStudents = mysqli_query($conn, $sqlStudents);

// Håndterer feil hvis studentdata ikke kan hentes
if (!$resultStudents) {
    die("Feil ved henting av elever: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fravær</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
        crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../style.css"/>
    <style>
        @media (max-width: 575px) {
            .btn-group {
                display: none;
            }
        }
    </style>
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
            <a href="" class="nav-link" style="display: inline-block; margin-right: 20px;">Fravær</a>
            <a href="../annotation/annotation.php" class="nav-link" style="display: inline-block; margin-right: 20px;">Anmerkninger</a>
            <a href="" class="nav-link" style="display: inline-block;">Timeplan</a>
        </div>
        <div class="d-flex align-items-center">
            <div class="dropdown fs-5">
                <a class="nav-link dropdown-toggle-no-caret" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="margin-right: 20px;">
                    <i class="fas fa-bars"></i>
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="../index.php">Forside</a>
                    <a class="dropdown-item" href="">Fravær</a>
                    <a class="dropdown-item" href="../annotation/annotation.php">Anmerkninger</a>
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
                    <a class="dropdown-item" href="../logout.php">Logg ut</a>
                </div>
            </div>
        </div>
    </div>
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
        <div class="btn-group mb-3">
            <a href="absence.php?view=table" class="btn btn-dark <?php if ($viewMode === 'table') echo 'active'; ?>"><i class="fa fa-th-list"></i> Tabellvisning</a>
            <a href="absence.php?view=group" class="btn btn-dark <?php if ($viewMode === 'group') echo 'active'; ?>"><i class="fa fa-th"></i> Gruppevisning</a>
        </div>
        
        <?php if ($viewMode === 'table'): ?>
            <!-- Tabellvisning -->
            <form method="POST">
                <table class="table table-hover text-center">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Fornavn</th>
                            <th scope="col">Etternavn</th>
                            <th scope="col">Klasse</th>
                            <th scope="col">Fravær</th>
                            <th scope="col">Handling</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($resultStudents)): ?>
                            <tr>
                                <td><?php echo $row["id"] ?></td>
                                <td><?php echo $row["first_name"] ?></td>
                                <td><?php echo $row["last_name"] ?></td>
                                <td><?php echo $row["class"] ?></td>
                                <td class="<?php echo getFravaerClass($row["absence"]); ?>"><?php echo $row["absence"] ?></td>
                                <td class="action-icons">
                                    <?php
                                    if (!empty($row["comment"])) {
                                        echo '<a class="link-dark" onclick="showComment(\''
                                            . $row["comment"] . '\')" data-bs-toggle="modal" data-bs-target="#commentModal"><i class="fa fa-comment fs-5"></i></a>';
                                    }
                                    ?>
                                    <a href="edit-absence.php?id=<?php echo $row["id"] ?>" class="link-dark"><i
                                            class="fa fa-edit fs-5"></i></a>
                                    <a href="delete-absence.php?id=<?php echo $row["id"] ?>" class="link-dark"><i
                                            class="fa fa-trash fs-5"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <a href="create-absence.php" class="btn btn-dark mb-3">Legg til elev</a>
                <button type="submit" name="merk-til-stede" class="btn btn-dark mb-3">Merk alle til stede</button>
                <button type="submit" name="nullstill-fravaer" class="btn btn-dark mb-3">Nullstill fravær</button>
            </form>
        <?php else: ?>
            <!-- Gruppevisning -->
            <form method="POST">
                <div class="row">
                    <?php while ($row = mysqli_fetch_assoc($resultStudents)): ?>
                        <div class="col-md-3 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $row["first_name"] . ' ' . $row["last_name"]; ?></h5>
                                    <p class="card-text">Klasse: <?php echo $row["class"]; ?></p>
                                    <p class="<?php echo getFravaerClass($row["absence"]); ?>"><k class="text-dark">Fravær: </k><?php echo $row["absence"]; ?></p>
                                    <div class="action-icons">
                                        <?php
                                        if (!empty($row["comment"])) {
                                            echo '<a class="link-dark" onclick="showComment(\''
                                                . $row["comment"] . '\')" data-bs-toggle="modal" data-bs-target="#commentModal"><i class="fa fa-comment fs-5"></i></a>';
                                        }
                                        ?>
                                        <a href="edit-absence.php?id=<?php echo $row["id"] ?>" class="link-dark"><i
                                                class="fa fa-pencil-square fs-5"></i></a>
                                        <a href="delete-absence.php?id=<?php echo $row["id"] ?>" class="link-dark"><i
                                                class="fa fa-trash fs-5"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                <a href="create-absence.php" class="btn btn-dark mb-3">Legg til elev</a>
                <button type="submit" name="merk-til-stede" class="btn btn-dark mb-3">Merk alle til stede</button>
                <button type="submit" name="nullstill-fravaer" class="btn btn-dark mb-3">Nullstill fravær</button>
            </form>
        <?php endif; ?>

        <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="commentModalLabel">Kommentar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="comment-text"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Lukk</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <script>
        function showComment(commentText) {
            document.getElementById("comment-text").textContent = commentText;
        }
    </script>
    <script>
        // Funksjon for å endre URL til "absence.php?view=group" når skjermvinduet er under 575 piksler
        function changeURLForGroupTable() {
            var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
            var currentURL = window.location.href;

            // Sjekk om skjermvinduet er under 575 piksler og 'view' ikke er allerede "group"
            if (screenWidth < 575 && !currentURL.includes("view=group")) {
                // Endre URL til "absence.php?view=group"
                window.location.href = "absence.php?view=group";
            }
        }

        // Kjør funksjonen ved lasting og når vinduet endres
        window.onload = changeURLForGroupTable;
        window.onresize = changeURLForGroupTable;
    </script>

    <script src="js/logo_merge.js"></script> <!-- id="logo" for index, id="logoB" for absence og annotation -->
</body>

</html>
