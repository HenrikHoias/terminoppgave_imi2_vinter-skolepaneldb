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

    <title>Forside</title>
</head>

<body>
<nav class="navbar justify-content-between fs-4 custom-navbar">
    <img src="images/eleva_text_logo.svg" alt="Eleva" class="px-2">Forside
    <i class="fa fa-info-circle px-4"></i>
</nav>
<nav class="fs-6 mb-5 text-dark custom-navlinks">
    <a href="index.php" class="nav-link" style="display: inline-block; margin-right: 20px;">Forside</a>
    <a href="absence/absence.php" class="nav-link" style="display: inline-block; margin-right: 20px;">Fravær</a>
    <a href="annotation/annotation.php" class="nav-link" style="display: inline-block; margin-right: 20px;">Anmerkninger</a>
    <a href="" class="nav-link" style="display: inline-block;">Timeplan</a>
</nav>
<div class="container">
    <?php
    if (isset($_GET["msg"])) {
        $msg = $_GET["msg"];
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            ' . $msg . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }
    ?>

    <h2 class="my-4">Om oss</h2>
    <p class="mb-5">Velkommen til Eleva, ditt dedikerte system for skoleadministrasjon. Vårt intuitive online-verktøy er utformet for å imøtekomme behovene til lærere og skoleansatte ved å samle viktige oppgaver på ett sted. Med Eleva kan fravær, notater, timeplaner og annen viktig elevinformasjon administreres med enkelhet og effektivitet. Vårt oppdrag er å lette administrativt arbeid, slik at lærere kan konsentrere seg om det som virkelig teller: å fremme elevens læring og vekst. <u><a href="#oversikt">Er du klar for en enklere skolehverdag?<a></u></p>
    <h2 id="oversikt" class="my-4 text-center">Oversikt</h2>
    <div class="row">
        <div class="col-md-4">
            <a href="absence/absence.php">
                <div class="card mb-4 hover">
                <img src="images/absence_img.png" class="card-img-top" alt="Fravær">
                    <div class="card-body">
                        <h5 class="card-title">Fravær</h5>
                        <p class="card-text">Les, sett og endre elevfravær samt elever på en enkel og intuitiv måte.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="annotation/annotation.php">
                <div class="card mb-4 hover">
                    <img src="images/annotation_img.png" class="card-img-top" alt="Anmerkninger">
                    <div class="card-body">
                        <h5 class="card-title">Anmerkninger</h5>
                        <p class="card-text">Få oversikten over elevenes orden og atferd og ivareta skolereglene på en strukturert måte.</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="">
                <div class="card mb-4 hover">
                    <img src="images/schedule_img.png" class="card-img-top" alt="Timeplan">
                    <div class="card-body">
                        <h5 class="card-title">Timeplan</h5>
                        <p class="card-text">Se timeplaner for både elever og lærere for å sikre effektiv planlegging.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <h2 class="my-4">Kontakt oss</h2>
    <p>Vi er tilgjengelige på epost, telefon og har kontor i Professor Kohts vei 5.</p>
    <p class="mb-5">E-post: eleva@skolessy.no | Tlf: +47 223 33 233</p>
    <!-- Kontaktskjema -->
    <div class="container mt-5">
    <form action="contact_form.php" method="post">
        <div class="mb-3">
            <label for="melding" class="form-label">Hvordan kan vi forbedre oss? Jo mer du forteller oss, desto bedre kan vi hjelpe deg.</label>
            <textarea class="form-control" id="melding" name="melding" rows="5" maxlength="200" required></textarea>
            <div id="chartCount" class="mt-2">0 / 200</div>
        </div>
      <button type="submit" class="btn btn-primary">Send tilbakemelding</button>
    </form>
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
                document.querySelector('.navbar img').setAttribute('src', 'images/eleva_logo.svg');
                // Hvis visningsmodusen er tabell, endre den til gruppevisning
            } else {
            // Hvis skjermens bredde er 575px eller mer
            // Bytt logo tilbake til "eleva_text_logo.svg"
            document.querySelector('.navbar img').setAttribute('src', 'images/eleva_text_logo.svg');
            }
        }

        var commentTextarea = document.getElementById("melding");
        var chartCount = document.getElementById("chartCount");

        commentTextarea.addEventListener("input", function () {
        var currentLength = commentTextarea.value.length;
        chartCount.textContent = currentLength + " / 200";
        });
    </script>

    <footer class="text-light text-center py-3" style="margin-top:200px;background-color:#01364C;">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> | Nulla facilisi. Mauris nulla diam, auctor at neque ut, convallis finibus erat. In porttitor id nulla id ullamcorper.</p>
        </div>
    </footer>

</body>

</html>
