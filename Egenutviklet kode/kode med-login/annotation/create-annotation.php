<?php
session_start();

include "../db_conn.php";

// Sjekker om brukeren er logget inn
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Henter tilgangsnivået til brukeren fra databasen basert på brukernavn eller e-postadresse
$usernameOrMail = $_SESSION['username'];
$sqlUserAccess = "SELECT access FROM users WHERE username = '$usernameOrMail' OR mail = '$usernameOrMail'";
$resultUserAccess = mysqli_query($conn, $sqlUserAccess);

// Håndterer feil hvis det oppstår problemer med henting av tilgangsnivå
if (!$resultUserAccess) {
    die("Feil ved henting av brukerens tilgangsnivå: " . mysqli_error($conn));
}

// Henter brukerdata og tilgangsnivået til brukeren fra resultatet av SQL-spørringen
$userData = mysqli_fetch_assoc($resultUserAccess);

// Håndterer feil hvis ingen brukerdata ble funnet
if (!$userData) {
    die("Ingen brukerdata funnet for brukernavnet eller e-postadressen: " . $usernameOrMail);
}

// Deler opp tilgangsnivået for å få en liste over klassenavn brukeren har tilgang til
$accessLevels = explode("-", $userData['access']);
$classNames = array();

// Henter klassenavnene basert på tilgangsnivået
foreach ($accessLevels as $level) {
    if ($level === '*') {
        $sqlClassName = "SELECT class_name FROM classes";
    } else {
        $sqlClassName = "SELECT class_name FROM classes WHERE id = $level";
    }
    $resultClassName = mysqli_query($conn, $sqlClassName);

    // Håndterer feil hvis det oppstår problemer med henting av klasse
    if (!$resultClassName) {
        die("Feil ved henting av klasse: " . mysqli_error($conn));
    }

    // Legger til klassenavn i listen
    while ($dataClassName = mysqli_fetch_assoc($resultClassName)) {
        array_push($classNames, $dataClassName['class_name']);
    }
}

// Sjekker om 'id'-parameteren er satt og er numerisk
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Ugyldig ID-parameter.";
    exit();
}

// Henter 'id' fra URL
$id = $_GET['id'];

// Behandler POST-request for å opprette en ny anmerkning
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject_type = $_POST['subject_type'];
    $annotation_text = $_POST['annotation_text'];
    $annotation_date = $_POST['annotation_date'];
    $annotation_text = substr($annotation_text, 0, 200);

    // Forbereder SQL-spørring med parametere og utfører den
    $stmt = $conn->prepare("INSERT INTO annotations (student_id, subject_type, annotation_text, annotation_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $id, $subject_type, $annotation_text, $annotation_date);

    // Sjekker om spørringen ble utført vellykket
    if ($stmt->execute()) {
        header("Location: annotation.php?msg=Ny anmerkning er opprettet hos medelev");
        exit();
    } else {
        echo "Feil: " . $stmt->error;
    }

    $stmt->close();
}

// Henter informasjon om studenten basert på 'id'
$sql = "SELECT class, first_name, last_name FROM students WHERE id = $id LIMIT 1";
$result = mysqli_query($conn, $sql);

// Sjekker om resultatet er tomt eller feilet
if (!$result || mysqli_num_rows($result) === 0) {
    echo "Ingen student funnet med angitt ID.";
    exit();
}

// Henter informasjon om studenten og sjekker om brukeren har tilgang til studentens klasse
$row = mysqli_fetch_assoc($result);

if (in_array($row["class"], $classNames)) {
    $firstName = $row['first_name'];
    $lastName = $row['last_name'];
} else {
    header("Location: annotation.php?msg=Du kan ikke opprette anmerkning for denne eleven!");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Anmerkninger</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

  <!-- Custom CSS -->
  <link rel="stylesheet" href="../style.css"/>
</head>

<body>
    <nav class="navbar navbar-light justify-content-center fs-3 mb-5" style="background-color: #1099B9; color: #FFF;">
        Anmerkninger
    </nav>

    <div class="container">
        <div class="text-center mb-4">
          <h3>Sett anmerkning</h3>
          <p class="text-muted">Fyll ut skjema nedenfor for å sette anmerkning på <?php echo $row["first_name"] . ' ' . $row["last_name"]?> i klasse <?php echo $row["class"]?></p>
        </div>

        <div class="container d-flex justify-content-center">
            <form action="" method="post" style="width:50vw; min-width:300px;">
                <div class="row mb-3">
                    <div class="form-group mb-3">
                        <label for="subject_typeSelect">Fag:</label>
                        <select class="form-select" id="subject_typeSelect" name="subject_type">
                            <option value="Engelsk">Engelsk</option>
                            <option value="Mattematikk">Mattematikk</option>
                            <option value="Norsk">Norsk</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Anmerkning beskrivelse:</label>
                        <textarea type="text" class="form-control" name="annotation_text" id="annotation_text" maxlength="200" required value="<?php echo isset($annotation_text) ? $annotation_text : ''; ?>"></textarea>
                        <div id="annotationTextCount">(maks 200 tegn)</div>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Dato:</label>
                      <input type="date" class="form-control" name="annotation_date" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-success" name="submit">Opprett</button>
                        <a href="annotation.php" class="btn btn-danger">Avbryt</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

    <footer class="bg-dark text-light text-center py-3" style="margin-top:200px;">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> | Nulla facilisi. Mauris nulla diam, auctor at neque ut, convallis finibus erat. In porttitor id nulla id ullamcorper.</p>
        </div>
    </footer>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script>
// Legg til en event listener for å oppdatere anmerkningsteksttelleren
var annotationText = document.getElementById("annotation_text");
var annotationTextCount = document.getElementById("annotationTextCount");

annotationText.addEventListener("input", function () {
  var currentLength = annotationText.value.length;
  annotationTextCount.textContent = currentLength + " / 200";
});
</script>
</html>
