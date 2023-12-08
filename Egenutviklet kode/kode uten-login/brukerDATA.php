<?php
// Database-tilkobling (Erstatt med dine egne databaseinnstillinger)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "testdb";

// Opprett en tilkobling til databasen
$conn = new mysqli($servername, $username, $password, $dbname);

// Sjekk tilkoblingen
if ($conn->connect_error) {
    die("Kunne ikke koble til databasen: " . $conn->connect_error);
}

// Antall elever som skal legges til (mellom 1 og 20)
$numStudents = rand(10, 20);

// Liste over mulige fornavn, etternavn, klasser og fraværstyper
$firstNames = ["Henrik", "Emma", "Oscar", "Maja", "Oliver", "Nora", "William", "Sofie", "Lucas", "Ella", "Filip", "Amalie", "Emil", "Ida", "Noah", "Leah", "Liam", "Ingrid", "Jakob", "Thea", "Aksel", "Julie", "Jonas", "Hanna", "Matias", "Sara", "Alexander", "Maria", "Elias", "Victoria", "Adrian", "Emilie", "Sebastian", "Anna", "Kristian", "Mathilde", "Marcus", "Isabella", "Benjamin", "Sofia", "Daniel", "Lilly", "Olav", "Eline", "Andreas", "Amelia", "Martin", "Elise", "Sander", "Selma", "Thomas", "Alma", "Magnus", "Frida", "Håkon", "Vilde", "Erik", "Aurora", "Johannes", "Hedda", "Nikolai", "Lea", "Victor", "Emilia", "Fredrik", "Tiril", "Gustav", "Mia", "Herman", "Linnea", "Ludvig", "Ingeborg", "Jon", "Live", "Bjørn", "Marie", "Petter", "Hilde", "Ole", "Karen", "Per", "Anne", "Knut", "Inger", "Geir", "Lise", "Øystein", "Tone", "Arne", "Marte", "Jan", "Silje", "Eirik", "Kristine", "Terje", "Camilla", "Odd", "Hege", "Roger", "Pernille"];
$lastNames = ["Hansen", "Johansen", "Olsen", "Larsen", "Andersen", "Pedersen", "Nilsen", "Kristiansen", "Jensen", "Karlsen", "Johnsen", "Pettersen", "Eriksen", "Berg", "Haugen", "Hagen", "Johannessen", "Andreassen", "Jacobsen", "Dahl", "Halvorsen", "Sørensen", "Lund", "Solberg", "Knutsen", "Moen", "Gundersen", "Iversen", "Strand", "Svendsen", "Evensen", "Bakken", "Nielsen", "Haugland", "Myhre", "Ødegård", "Solheim", "Fredriksen", "Bakke", "Berntsen", "Sæther", "Haug", "Moe", "Aas", "Hovland", "Bjerke", "Berge", "Hermansen", "Bøe", "Sætre", "Lunde", "Birkeland", "Borge", "Hovde", "Hovden", "Brekke", "Helle", "Bakken", "Hovland", "Bjerke", "Berge", "Hermansen", "Bøe", "Sætre", "Lunde", "Birkeland", "Borge", "Hovde", "Hovden", "Brekke", "Helle", "Bakken", "Hovland", "Bjerke", "Berge", "Hermansen", "Bøe", "Sætre", "Lunde", "Birkeland", "Borge", "Hovde", "Hovden", "Brekke", "Helle", "Bakken", "Hovland", "Bjerke", "Berge", "Hermansen", "Bøe", "Sætre", "Lunde", "Birkeland", "Borge", "Hovde", "Hovden", "Brekke", "Helle"];
$classes = ["10A", "10B", "10C"];
$absences = ["Ingen", "Til stede", "Dokumentert", "Udokumentert"];

// Legg til elever i databasen
for ($i = 0; $i < $numStudents; $i++) {
    $firstName = $firstNames[array_rand($firstNames)];
    $lastName = $lastNames[array_rand($lastNames)];
    $class = $classes[array_rand($classes)];
    $randomAbsenceChance = rand(1, 10); // Generer et tilfeldig tall mellom 1-100 for sjansen for fravær

    if ($randomAbsenceChance <= 5) { // % sjanse for "Til stede"
        $absence = "Til stede";
        $comment = "";
    } else {
        $absence = $absences[array_rand($absences)]; // Velg tilfeldig fraværstype
        $comments = [
            "Elev $firstName $lastName var fraværende i dag, men det var dokumentert.",
            "Det ser ut som $firstName $lastName var borte, men fraværet var dokumentert.",
            "$firstName $lastName hadde et dokumentert fravær i dag.",
            "Fraværet til $firstName $lastName er dokumentert.",
            "$firstName $lastName var fraværende i dag, og det var dessverre ikke dokumentert.",
            "Uheldigvis var $firstName $lastName borte i dag uten dokumentasjon.",
            "Fraværet til $firstName $lastName er udokumentert.",
            "Det ser ut til at $firstName $lastName hadde et udokumentert fravær i dag.",
            "$firstName $lastName var borte i dag uten gyldig dokumentasjon.",
            "Merk: $firstName $lastName mangler en gyldig dokumentasjon for fraværet."
        ];

        if ($absence == "Dokumentert" || $absence == "Udokumentert") {
            $comment = $comments[array_rand($comments)];
        } else {
            $comment = "";
        }
    }

    // SQL-spørring for å legge til en elev i databasen
    $sql = "INSERT INTO students (first_name, last_name, class, absence, comment) VALUES ('$firstName', '$lastName', '$class', '$absence', '$comment')";

    if ($conn->query($sql) === TRUE) {
        echo "Elev lagt til: $firstName $lastName<br>";
    } else {
        echo "Feil ved innsetting av elev: " . $conn->error . "<br>";
    }
}

// Lukk databasetilkoblingen
$conn->close();
?>
