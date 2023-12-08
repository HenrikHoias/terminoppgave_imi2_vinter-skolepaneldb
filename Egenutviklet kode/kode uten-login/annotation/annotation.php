<?php
include "../db_conn.php";

// Legg til en variabel for å lagre visningsmodus (tabell eller gruppevisning)
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
<nav class="navbar justify-content-between fs-4 custom-navbar">
    <img src="../images/eleva_text_logo.svg" alt="Eleva" class="px-2">Anmerkninger
    <i class="fa fa-bars px-4"></i>
</nav>
<nav class="fs-6 mb-5 text-dark custom-navlinks">
    <a href="../index.php" class="nav-link" style="display: inline-block; margin-right: 20px;">Forside</a>
    <a href="../absence/absence.php" class="nav-link" style="display: inline-block; margin-right: 20px;">Fravær</a>
    <a href="annotation.php" class="nav-link" style="display: inline-block; margin-right: 20px;">Anmerkninger</a>
    <a href="" class="nav-link" style="display: inline-block;">Timeplan</a>
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
        <div class="text-center mb-4">
            <p class="text-muted">Trykk på + tegnet for å legge til en anmerkning</p>
        </div>

        <div class="row">
            <?php
            $sql = "SELECT * FROM `students`";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                $studentId = $row["id"];
                $annotationsCountSql = "SELECT COUNT(*) as count FROM annotations WHERE student_id = $studentId";
                $annotationsCountResult = mysqli_query($conn, $annotationsCountSql);
                $annotationsCountRow = mysqli_fetch_assoc($annotationsCountResult);
                $annotationsCount = $annotationsCountRow["count"];
            ?>
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row["first_name"] . ' ' . $row["last_name"]; ?></h5>
                            <p class="card-text">Klasse: <?php echo $row["class"]; ?></p>
                            <p class="card-title text-muted">Anmerkninger:</p>
                            <h3 class="card-text"><?php echo $annotationsCount; ?></h3>
                            <div class="action-icons">
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
        // Sjekk skjermens bredde når siden lastes
        window.onload = function() {
            checkScreenWidth();

            // Lytt etter endringer i vindusstørrelsen
            window.addEventListener('resize', checkScreenWidth);
        };

        function checkScreenWidth() {
            // Hent skjermens bredde
            var screenWidth = window.innerWidth;

            // Sjekk om skjermens bredde er under 575px
            if (screenWidth < 575) {
                // Bytt logo til "eleva_logo.svg"
                document.querySelector('.navbar img').setAttribute('src', '../images/eleva_logo.svg');
                // Hvis visningsmodusen er tabell, endre den til gruppevisning
            } else {
            // Hvis skjermens bredde er 575px eller mer
            // Bytt logo tilbake til "eleva_text_logo.svg"
            document.querySelector('.navbar img').setAttribute('src', '../images/eleva_text_logo.svg');
            }
        }
    </script>

    <footer class="text-light text-center py-3" style="margin-top:200px;background-color:#01364C;">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> | Nulla facilisi. Mauris nulla diam, auctor at neque ut, convallis finibus erat. In porttitor id nulla id ullamcorper.</p>
        </div>
    </footer>

</body>

</html>
