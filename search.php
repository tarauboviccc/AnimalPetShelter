<!--CITATION: This code was adapted from the starter code file oracle-test.php -->

<html>
    <head>
        <title>CPSC 304 PHP/Oracle Demonstration</title>
        <style>
            <?php include 'style.css'; ?>
        </style>
        <link href="https://fonts.googleapis.com/css?family=Archivo+Black|Judson:400,700" rel="stylesheet">
    </head>

    <body>
        <h1>Search the Database</h1>
        <a href="home-page.php">
            <button class="return-home">Return to Home Page</button>
        </a>
        <h2 id="projection">PROJECTION: Choose which attributes you would like to see from Animals table</h2>
        <form method="POST" action="search.php"> <!-- refresh page when submitted -->
            <input type="checkbox" name="projectionAttributes[]" value="petID"> petID<br><br>
            <input type="checkbox" name="projectionAttributes[]" value="animalName"> animalName<br><br>
            <input type="checkbox" name="projectionAttributes[]" value="type"> type<br><br>
            <input type="checkbox" name="projectionAttributes[]" value="age"> age<br><br>
            <input type="checkbox" name="projectionAttributes[]" value="favouriteCaretaker"> Animal's Favorite Caretaker<br><br>
            <input type="checkbox" name="projectionAttributes[]" value="previousOwner"> Animal's Previous Owner<br><br>
            <input type="checkbox" name="projectionAttributes[]" value="arrivalDate"> Animal's Arrival Date to the Shelter<br><br>
            <input type="checkbox" name="projectionAttributes[]" value="adopterID"> Adopter ID<br><br>
            
            <input type="submit" name="projectionSubmit" value="submit">
        </form>
        <h2 id="groupBy">Aggregation with GROUP BY: Count how many animals of specific type we have in pet shelter</h2>
        <p>The values are case sensitive and if you enter in the wrong case, the group by query will not be correctly executed.</p>
        <p>Reminder: Animal type first letter should be capitalized.</p>
        <form method="GET" action="search.php"> <!-- refresh page when submitted -->
            <input type="hidden" id="groupByQueryRequest" name="groupByQueryRequest">
            Type of animal: <input type="text" name="animalType"> <br /><br />
            <input type="submit" name="groupBySubmit">
        </form>
	</body>
    <!-- call GET or POST function -->
    <?php
        include 'database_and_queries.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handlePOSTRequest();
        }
        else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            handleGETRequest();
        }
    ?>
</html>
