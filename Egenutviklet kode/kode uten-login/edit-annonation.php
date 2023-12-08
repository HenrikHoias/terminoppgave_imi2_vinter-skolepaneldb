<?php
include "db_conn.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

  <title>A</title>
</head>

<body>
    <nav class="navbar navbar-light justify-content-center fs-3 mb-5" style="background-color: #0077A0; color: #FFF;">
        Anmerkninger
    </nav>

  <div class="container">
    <div class="text-center mb-4">
      <h3>Sett anmerkning</h3>
      <p class="text-muted">Fyll ut skjema nedenfor for å sette anmerkning på <?php echo $row["first_name"] . ' ' . $row["last_name"]; ?> i klasse <?php echo $row["class"]?></p>
    </div>

    <div class="container d-flex justify-content-center">
      <form action="" method="post" style="width:50vw; min-width:300px;">
        <p>ID: </p>

        <div class="row mb-3">

          <div class="col">
            <label class="form-label">fag:</label>
            <input type="text" class="form-control" name="subject_type" value="<?php echo $row['subject_type'] ?>">
          </div>
        </div>

        <div class="form-group mb-3">
          <label for="subject_typeSelect">Fag:</label>
          <select class="form-select" id="subject_typeSelect" name="subject">
            <option value="Norsk" <?php echo ($row["subject_type"] == 'Norsk') ? "selected" : ""; ?>>Norsk</option>
            <option value="Engelsk" <?php echo ($row["subject_type"] == 'Engelsk') ? "selected" : ""; ?>>Engelsk</option>
            <option value="Mattematikk" <?php echo ($row["subject_type"] == 'Mattematikk') ? "selected" : ""; ?>>Mattematikk</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Anmerkning beskrivelse:</label>
          <input type="text" class="form-control" name="annotation_text" value="<?php echo $row['annotation_text'] ?>">
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
          <button type="submit" class="btn btn-success" name="submit">Opprett</button>
          <a href="index.php" class="btn btn-danger">Avbryt</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

  <script>
    var absence = "<?php echo $row['absence']; ?>"; // Hent verdien av absence fra PHP

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
