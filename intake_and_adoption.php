<!--CITATION: This code was adapted from the starter code file oracle-test.php -->

<html>
    <head>
        <title>Intake and adoption page</title>
        <style>
            <?php include 'style.css'; ?>
        </style>
        <link href="https://fonts.googleapis.com/css?family=Archivo+Black|Judson:400,700" rel="stylesheet">
    </head>

    <body>
        <h1>Intake and Adoption</h1>
        <a href="home-page.php">
            <button class="return-home">Return to Home Page</button>
        </a>
        <form method="POST" action="intake_and_adoption.php">
            <button class="view-animal-table" type="submit" name="verifyAnimalTable">
                Check the Animal Table
            </button>
        </form>
        <h2 id="add">Add an animal to the system</h2>
        <form method="POST" action="intake_and_adoption.php"> <!--refresh page when submitted-->
            <input type="hidden" id="insertAnimalQueryRequest" name="insertAnimalQueryRequest">
            Animal Name: <input type="text" name="insAnimalName"> <br /><br />

            <div class="form-group">
            <label for="animalType">Animal Type:</label>
            <div class="animal-dropdown">
            <select name="insAnimalType">
                <option value="dog">Dog</option>
                <option value="cat">Cat</option>
                <option value="hamster">Hamster</option>
                <option value="bunny">Bunny</option>
            </select>
            </div>
            </div>
            Age: <input type="text" name="insAge"> <br /><br />
            Favourite Caretaker ID: <input type="text" name="insFavCare"> <br /><br />
            Previous Owner ID: <input type="text" name="insPrevOwner"> <br /><br />
            
            <div class="form-group">
            <label for="arrivalDate">Arrival Date:</label>
            <div class="date-dropdowns">
                <select name="arrivalYear">
                    <?php
                    // Generate year options
                    for ($year = 2000; $year <= 2023; $year++) {
                        echo "<option value=\"$year\">$year</option>";
                    }
                    ?>
                </select>
                <select name="arrivalMonth">
                    <?php
                    // Generate month options
                    for ($month = 1; $month <= 12; $month++) {
                        $monthName = date("F", mktime(0, 0, 0, $month, 1, 2000));
                        echo "<option value=\"$month\">$monthName</option>";
                    }
                    ?>
                </select>
                <select name="arrivalDay">
                    <?php
                    // Generate day options
                    for ($day = 1; $day <= 31; $day++) {
                        echo "<option value=\"$day\">$day</option>";
                    }
                    ?>
                </select>
            </div>
            </div>
            Adopter ID: <input type="text" name="insAdopterID"> <br /><br />

            <input type="submit" value="Insert" name="insertAnimalSubmit"></p>
        </form>

        <h2 id="remove">Remove an animal from the system</h2>
        <form method="POST" action="intake_and_adoption.php"> <!--refresh page when submitted-->
            <input type="hidden" id="deleteAnimalQueryRequest" name="deleteAnimalQueryRequest">
            Pet ID: <input type="text" name="delPetID"> <br /><br />
            <input type="submit" value="Delete" name="deleteAnimalSubmit"></p>
        </form>

        <h2 id="update">Update animal information</h2>
        <p>The values are case sensitive and if you enter in the wrong case, the update statement will not do anything.</p>
        <form method="POST" action="intake_and_adoption.php"> <!--refresh page when submitted-->
            <input type="hidden" id="updateAnimalQueryRequest" name="updateAnimalQueryRequest">
            Pet ID: <input type="text" name="upPetID"> <br /><br />
            Age: <input type="text" name="upAge"> <br /><br />
            Favourite Caretaker ID: <input type="text" name="upFavCare"> <br /><br />
            Adopter ID: <input type="text" name="upAdopterID"> <br /><br />

            <input type="submit" value="Update" name="updateAnimalSubmit"></p>
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

