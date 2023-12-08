<?php
session_start(); // Start sesjonen

// Slett alle sesjonsvariabler
session_unset();

// Ødelegg sesjonen
session_destroy();

// Omdiriger tilbake til innloggingssiden etter utlogging
header("Location: login.php");
exit(); // Avslutt skriptet etter omdirigering for å forhindre uønsket kodekjøring
?>
