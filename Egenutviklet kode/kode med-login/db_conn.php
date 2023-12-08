<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "skolepanel";

// Lager kobling
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Sjekker kobling
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
