<?php
include "db_conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Hent tilbakemeldingen fra skjemaet
    $feedback = $_POST['melding'];

    // Forbered SQL-setningen med et prepared statement
    $sql = "INSERT INTO feedback (feedback) VALUES (?)";

    // Opprett et prepared statement
    $stmt = $conn->prepare($sql);

    // Binder parameteren til prepared statement
    $stmt->bind_param("s", $feedback);

    // Utfør prepared statement for å sette inn data i databasen
    if ($stmt->execute()) {
        header("Location: index.php?msg=Tilbakemelding er sendt. Tusen takk for tilbakemelding!");
    } else {
        echo "Failed: " . mysqli_error($conn);
    }

    // Lukk prepared statement
    $stmt->close();

    // Lukk databaseforbindelsen
    $conn->close();
}
?>