<?php
include "db_conn.php";
$id = $_GET["id"];

if (isset($_POST["submit"])) {
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $class = $_POST['class'];
  $absence = $_POST['absence'];
  $absence_type = $_POST['absence_type'];
  $comment = $_POST['comment'];

  // Begrens kommentaren til maksimalt 200 tegn
  $comment = substr($comment, 0, 200);

  if ($absence == 'Fraværende') {
    if ($absence_type == 'Dokumentert') {
      $absence = 'Dokumentert';
    } else {
      $absence = 'Udokumentert';
    }
  }

  $sql = "UPDATE students SET first_name='$first_name', last_name='$last_name', class='$class', absence='$absence', comment='$comment' WHERE id = $id";

  $result = mysqli_query($conn, $sql);

  if ($result) {
    header("Location: index.php?msg=Elevopplysningene er oppdatert");
  } else {
    echo "Failed: " . mysqli_error($conn);
  }
}

// Hent studentinformasjon på nytt etter oppdatering
$sql = "SELECT * FROM `students` WHERE id = $id LIMIT 1";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

  <title>Fraværsoversikt</title>
</head>

<body>
    <nav class="navbar navbar-light justify-content-center fs-3 mb-5" style="background-color: #0077A0; color: #FFF;">
        Fraværsoversikt
    </nav>

  <div class="container">
    <div class="text-center mb-4">
      <h3>Endre fravær</h3>
      <p class="text-muted">Klikk på oppdater etter at du har endret informasjon</p>
    </div>

    <div class="container d-flex justify-content-center">
      <form action="" method="post" style="width:50vw; min-width:300px;">
        <div class="row mb-3">
          <div class="col">
            <label class="form-label">First Name:</label>
            <input type="text" class="form-control" name="first_name" value="<?php echo $row['first_name'] ?>">
          </div>

          <div class="col">
            <label class="form-label">Last Name:</label>
            <input type="text" class="form-control" name="last_name" value="<?php echo $row['last_name'] ?>">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Klasse:</label>
          <input type="text" class="form-control" name="class" value="<?php echo $row['class'] ?>">
        </div>

        <div class="form-group mb-3">
          <label for="absenceSelect">Fravær:</label>
          <select class="form-select" id="absenceSelect" name="absence" onchange="toggleExtraOptions()">
            <option value="Ingen" <?php echo ($row["absence"] == 'Ingen') ? "selected" : ""; ?>>-</option>
            <option value="Til stede" <?php echo ($row["absence"] == 'Til stede') ? "selected" : ""; ?>>Til stede</option>
            <option value="Fraværende" <?php echo ($row["absence"] == 'Fraværende') ? "selected" : ""; ?>>Fraværende</option>
          </select>
        </div>

        <div id="extraOptions" style="display: none;">
          <div class="form-check mb-3">
            <input class="form-check-input" type="radio" name="absence_type" id="documented" value="Dokumentert">
            <label class="form-check-label" for="documented">
              Dokumentert fravær
            </label>
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="radio" name="absence_type" id="undocumented" value="Udokumentert" <?php echo ($row["absence"] == 'Udokumentert') ? "checked" : ""; ?>>
            <label class="form-check-label" for="undocumented">
                Udokumentert fravær
            </label>
          </div>
        </div>

        <!-- Legg til kommentar textarea -->
        <div id="commentSection" style="display: none;">
          <div class="form-group mb-3">
            <label for="comment">Legg til kommentar:</label>
            <textarea class="form-control" name="comment" id="comment" rows="3" maxlength="200"><?php echo $row['comment']; ?></textarea>
            <div id="commentCount">(maks 200 tegn)</div>
          </div>
        </div>

        <div>
          <button type="submit" class="btn btn-success" name="submit">Oppdater</button>
          <a href="index.php" class="btn btn-danger">Avbryt</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

  <script>
    var absence = "<?php echo $row['absence']; ?>"; // Hent verdien av absence fra PHP

    function toggleExtraOptions() {
      var select = document.getElementById("absenceSelect");
      var extraOptions = document.getElementById("extraOptions");
      var commentSection = document.getElementById("commentSection");
      var documentedRadio = document.getElementById("documented");
      var undocumentedRadio = document.getElementById("undocumented");

      if (select.value === "Fraværende") {
        extraOptions.style.display = "block";
        commentSection.style.display = "block";
      } else if (select.value === "Til stede" || select.value === "Ingen") {
        if (absence === "Dokumentert") {
          documentedRadio.checked = true;
        } else {
          undocumentedRadio.checked = true;
        }
        extraOptions.style.display = "none";
        commentSection.style.display = "none";
        document.getElementById("comment").value = "";
      } else {
        extraOptions.style.display = "none";
        commentSection.style.display = "block";
      }
    }

    toggleExtraOptions();

    // Legg til en event listener for å oppdatere ordtelleren
    var commentTextarea = document.getElementById("comment");
    var commentCount = document.getElementById("commentCount");

    commentTextarea.addEventListener("input", function () {
      var currentLength = commentTextarea.value.length;
      commentCount.textContent = currentLength + " / 200";
    });
  </script>

  <footer class="bg-dark text-light text-center py-3" style="margin-top:200px;">
    <div class="container">
      <p>&copy; <?php echo date('Y'); ?> Alle rettigheter reservert.</p>
    </div>
  </footer>

</body>

</html>
