<?php
include "../db_conn.php";

if (isset($_GET['id'])) {
    // Hent annotation_id fra URL-parameteren
    $annotationId = $_GET['id'];

    // Forberedt SQL-spørring for å slette anmerkningen basert på annotation_id
    $deleteSql = "DELETE FROM annotations WHERE annotation_id = ?";

    // Forbered og utfør spørringen med forberedte uttalelser for å forhindre SQL-injeksjoner
    $stmt = mysqli_prepare($conn, $deleteSql);
    mysqli_stmt_bind_param($stmt, "i", $annotationId);

    if (mysqli_stmt_execute($stmt)) {
        // Anmerkningen ble slettet vellykket, omdiriger brukeren tilbake til forrige side
        header("Location: {$_SERVER['HTTP_REFERER']}&msg=Anmerkningen hos medeleven er slettet");
        exit();
    } else {
        echo "Feil ved sletting av anmerkning: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    echo "Ugyldig id";
}
?>
