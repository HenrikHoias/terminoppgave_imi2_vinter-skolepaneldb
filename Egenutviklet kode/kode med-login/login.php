<?php
session_start(); // Start sesjonen

include "db_conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        $usernameOrEmail = $conn->real_escape_string($_POST['usernameormail']);
        $password = hash('sha256', $_POST['password']);
        
        $sql = "SELECT * FROM users WHERE (username='$usernameOrEmail' OR mail='$usernameOrEmail') AND password='$password'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            // Hvis det er en match, lagre brukernavnet som en sesjon
            $_SESSION['username'] = $usernameOrEmail;
            
            // Omdiriger til index.php med en suksessmelding
            header("Location: index.php?msg=Du er logget inn!");
            exit(); // Avslutt skriptet etter omdirigering for å forhindre uønsket kodekjøring
        } else {
            // Hvis ikke, vis feilmelding og omdiriger tilbake til login-siden
            header("Location: login.php?msg=Feil brukernavn eller passord. Prøv igjen.");
            exit();
        }
    }
}

// Lukker tilkoblingen til databasen
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <!-- Legg til Bootstrap CSS-lenken -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet" type="text/css">
    <style>
        .login-container {
            margin-top: 100px;
        }
    </style>
</head>

<body>
    <!-- Skjema for innlogging -->
    <div class="container">
        <?php
        if (isset($_GET["msg"])) {
            $msg = $_GET["msg"];
            echo '<div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                ' . $msg . '
            </div>';
            }
        ?>
        <div class="row justify-content-center login-container">
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <img id="logo" src="images/eleva_text_logo.svg" alt="Eleva" class="img-fluid px-5 mb-4">
                        <form action="login.php" method="post">
                            <div class="form-group">
                                <!-- Brukernavn- eller e-post-inntastingsfelt -->
                                <label for="username">Brukernavn eller e-post:</label>
                                <input type="text" class="form-control" id="usernameormail" name="usernameormail" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Passord:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block" name="login">Logg inn</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Legg til Bootstrap JS-lenken -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>

</html>
