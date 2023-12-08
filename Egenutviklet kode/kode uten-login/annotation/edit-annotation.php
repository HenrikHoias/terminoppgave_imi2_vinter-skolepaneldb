<?php
include "../db_conn.php";
$annotation_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

// Sjekk om skjemaet er sendt inn
if ($annotation_id !== null && isset($_POST["submit"])) {
    // Hent data fra skjemaet
    $subject_type = $_POST['subject_type'];
    $annotation_text = $_POST['annotation_text'];
    $annotation_date = DateTime::createFromFormat('Y-m-d', $_POST['annotation_date'])->format('d.m.Y');

    // Begrens anmerkningsteksten til maksimalt 200 tegn
    $annotation_text = substr($annotation_text, 0, 200);

    // Oppdater anmerkningsdata i databasen ved hjelp av forberedte uttalelser (prepared statements)
    $sql = "UPDATE annotations SET subject_type = ?, annotation_text = ?, annotation_date = ? WHERE annotation_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $subject_type, $annotation_text, $annotation_date, $annotation_id);
    $result = mysqli_stmt_execute($stmt);

    // Sjekk om oppdateringen var vellykket
    if ($result) {
        header("Location: annotation.php?msg=Anmerkning er oppdatert");
        exit(); // Avslutt skriptet etter omdirigering
    } else {
        echo "Feil: " . mysqli_error($conn);
    }
}

// Hent eksisterende anmerkningsdata og tilhørende elevinfo fra databasen
$sql = "SELECT a.*, s.first_name, s.last_name, s.class FROM annotations a 
        JOIN students s ON a.student_id = s.id 
        WHERE a.annotation_id = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $annotation_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="no">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Endre anmerkning</title>
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
            <h3>Endre anmerkning</h3>
            <p class="text-muted">Endre anmerkningen på <?php echo $row["first_name"] . ' ' . $row["last_name"] . ' i klasse ' . $row["class"]; ?></p>
        </div>

        <div class="container d-flex justify-content-center">
            <form action="" method="post" style="width:50vw; min-width:300px;">
                <div class="row mb-3">
                    <!-- Skjema for å oppdatere anmerkning -->
                    <div class="form-group mb-3">
                        <label for="subject_typeSelect">Fag:</label>
                        <select class="form-select" id="subject_typeSelect" name="subject_type">
                            <option value="Engelsk" <?php if ($row["subject_type"] == "Engelsk") echo "selected"; ?>>Engelsk</option>
                            <option value="Mattematikk" <?php if ($row["subject_type"] == "Mattematikk") echo "selected"; ?>>Mattematikk</option>
                            <option value="Norsk" <?php if ($row["subject_type"] == "Norsk") echo "selected"; ?>>Norsk</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Anmerkning beskrivelse:</label>
                        <textarea type="text" class="form-control" name="annotation_text" id="annotation_text" maxlength="200" required><?php echo $row["annotation_text"]; ?></textarea>
                        <div id="annotationTextCount">(maks 200 tegn)</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dato:</label>
                        <input type="date" class="form-control" name="annotation_date" required value="<?php echo date('Y-m-d', strtotime($row["annotation_date"])); ?>">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-success" name="submit">Oppdater</button>
                        <a href="annotation.php" class="btn btn-danger">Avbryt</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-4i0XEilCbKpkZtNz/kIBzy38YhMI6vOmwRCmwZGcRiBJoklDWeTvfFGmxHkvDbTE" crossorigin="anonymous"></script>

<!-- Bootstrap Datepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
  $(document).ready(function() {
    // initial setup for datepicker
    $('input[name="annotation_date"]').datepicker({
      format: 'yyyy-mm-dd'
    });

    // character count for annotation text
    $('#annotation_text').on('keyup', function() {
      var text_length = $(this).val().length;
      var text_remaining = 200 - text_length;

      $('#annotationTextCount').html('( ' + text_remaining + ' remaining)');
    });
  });
</script>
</body>
</html>
