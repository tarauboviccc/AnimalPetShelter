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
        <h1>Explore</h1>
        <a href="home-page.php">
            <button class="return-home">Return to Home Page</button>
        </a>
        <h2 id="having">Aggregation with HAVING: Find fundraiser event types with specified donation goal or above</h2>
        <form method="GET" action="explore.php"> <!-- refresh page when submitted -->
            <input type="hidden" id="havingQueryRequest" name="havingQueryRequest">
            Donation Goal: <input type="text" name="havingAvgDonationGoalThreshold"> <br /><br />
            <input type="submit" name="havingSubmit">
        </form>

        <h2 id="nested">Nested Aggregation with GROUP BY: Find all customers who have purchased equal to or greater than the average number of items</h2>
        <form method="GET" action="explore.php"> <!-- refresh page when submitted -->
            <input type="hidden" id="nestedQueryRequest" name="nestedQueryRequest">
            <input type="submit" name="nestedSubmit">
        </form>

        <h2 id="division">DIVISION: Caretakers facilitating adoption of every animal type</h2>
        <form method="GET" action="explore.php"> <!-- refresh page when submitted -->
            <input type="hidden" id="divisionQueryRequest" name="divisionQueryRequest">
            <input type="submit" name="divisionSubmit">
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

