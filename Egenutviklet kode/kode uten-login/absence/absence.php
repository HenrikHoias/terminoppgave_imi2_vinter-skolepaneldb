<?php
include "../db_conn.php";

function getFravaerClass($fravaer) {
    if ($fravaer == "Til stede") {
        return "text-success";
    } elseif ($fravaer == "Dokumentert") {
        return "text-warning";  
    } elseif ($fravaer == "Udokumentert") {
        return "text-danger";
    } else {
        return ""; // Ingen spesifikk klasse for andre verdier
    }
}

// Legg til en variabel for å lagre visningsmodus (tabell eller gruppevisning)
$viewMode = isset($_GET['view']) ? $_GET['view'] : 'table';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["merk-til-stede"])) {
        $updateQuery = "UPDATE `students` SET `absence` = 'Til stede'";
        mysqli_query($conn, $updateQuery);
        // Slett alle kommentarer
        $deleteCommentsQuery = "UPDATE `students` SET `comment` = NULL";
        mysqli_query($conn, $deleteCommentsQuery);
        header("Location: absence.php?msg=Alle elever er merket til stede&view=$viewMode");
        exit();
    } elseif (isset($_POST["nullstill-fravaer"])) {
        $updateQuery = "UPDATE `students` SET `absence` = 'Ingen'";
        mysqli_query($conn, $updateQuery);
        // Slett alle kommentarer
        $deleteCommentsQuery = "UPDATE `students` SET `comment` = NULL";
        mysqli_query($conn, $deleteCommentsQuery);
        header("Location: absence.php?msg=Fraværet til alle elever er nullstilt&view=$viewMode");
        exit();
    }
}
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
        @media (max-width: 575px) {
            .btn-group {
                display: none;
            }
        }
    </style>

    <title>Fravær</title>
</head>

<body>
<nav class="navbar justify-content-between fs-4 custom-navbar">
    <img src="../images/eleva_text_logo.svg" alt="Eleva" class="px-2">Fravær
    <i class="fa fa-bars px-4"></i>
</nav>
<nav class="fs-6 mb-5 text-dark custom-navlinks">
    <a href="../index.php" class="nav-link" style="display: inline-block; margin-right: 20px;">Forside</a>
    <a href="absence.php" class="nav-link" style="display: inline-block; margin-right: 20px;">Fravær</a>
    <a href="../annotation/annotation.php" class="nav-link" style="display: inline-block; margin-right: 20px;">Anmerkninger</a>
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
                        <?php
                        $sql = "SELECT * FROM `students`";
                        $result = mysqli_query($conn, $sql);
                        while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                            <td><?php echo $row["id"] ?></td>
                            <td><?php echo $row["first_name"] ?></td>
                            <td><?php echo $row["last_name"] ?></td>
                            <td><?php echo $row["class"] ?></td>
                            <td class="<?php echo getFravaerClass($row["absence"]); ?>"><?php echo $row["absence"] ?></td>
                            <td class="action-icons">
                                <?php
                                // Sjekk om det finnes en kommentar, og vis kommentarikonet bare hvis det er en kommentar
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
                        <?php
                        }
                        ?>
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
                <?php
                $sql = "SELECT * FROM `students`";
                $result = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row["first_name"] . ' ' . $row["last_name"]; ?></h5>
                            <p class="card-text">Klasse: <?php echo $row["class"]; ?></p>
                            <p class="<?php echo getFravaerClass($row["absence"]); ?>"><k class="text-dark">Fravær: </k><?php echo $row["absence"]; ?></p>
                            <div class="action-icons">
                                <?php
                                // Sjekk om det finnes en kommentar, og vis kommentarikonet bare hvis det er en kommentar
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
                <?php
                }
                ?>
            </div>
            <a href="create-absence.php" class="btn btn-dark mb-3">Legg til elev</a>
                <button type="submit" name="merk-til-stede" class="btn btn-dark mb-3">Merk alle til stede</button>
                <button type="submit" name="nullstill-fravaer" class="btn btn-dark mb-3">Nullstill fravær</button>
            </form>
            </form>
        <?php endif; ?>

        <!-- Bootstrap Comment Modal -->
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

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>

    <footer class="text-light text-center py-3" style="margin-top:200px;background-color:#01364C;">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> | Nulla facilisi. Mauris nulla diam, auctor at neque ut, convallis finibus erat. In porttitor id nulla id ullamcorper.</p>
        </div>
    </footer>

    <script>
        // Show comment in Bootstrap modal
        function showComment(commentText) {
            document.getElementById("comment-text").textContent = commentText;
        }
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
                var viewMode = '<?php echo $viewMode; ?>';
                if (viewMode === 'table') {
                    window.location.href = 'absence.php?view=group';
                }
            } else {
            // Hvis skjermens bredde er 575px eller mer
            // Bytt logo tilbake til "eleva_text_logo.svg"
            document.querySelector('.navbar img').setAttribute('src', '../images/eleva_text_logo.svg');
            }
        }
    </script>
</body>

</html>
