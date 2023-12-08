<?php
include "db_conn.php";

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
    <link rel="stylesheet" href="style.css"/>

    <title>Anmerkninger</title>
</head>

<body>
<nav class="navbar justify-content-between fs-3 custom-navbar">
        <i class="fa fa-graduation-cap px-4"></i> Anmerkninger
        <i class="fa fa-info-circle px-4"></i>
    </nav>
<nav class="fs-6 mb-5 text-dark custom-navlinks">
    <a href="index.php" class="nav-link" style="display: inline-block; margin-right: 20px;">Fravær</a>
    <a href="" class="nav-link" style="display: inline-block; margin-right: 20px;">Anmerkninger</a>
    <a href="" class="nav-link" style="display: inline-block;">Timeplan</a>
</nav>

    <div class="container">

        <!-- Gruppevisning -->
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
                        <div class="action-icons">
                            <a href="edit-annonation.php?id=<?php echo $row["id"] ?>" class="link-dark"><i
                                    class="fa fa-pencil-square fs-5"></i></a>
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
        crossorigin="anonymous"></script>

    <footer class="bg-dark text-light text-center py-3" style="margin-top:200px;">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Alle rettigheter reservert.</p>
        </div>
    </footer>

</body>

</html>
