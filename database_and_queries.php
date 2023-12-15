<!--CITATION: This code was adapted from the starter code file oracle-test.php -->
<?php
    /* DATABASE MANAGEMENT FUNCTIONS */
    
    $success = True; //keep track of errors so it redirects the page only if there are no errors
    $db_conn = NULL; // edit the login credentials in connectToDB()
    $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

    function debugAlertMessage($message) {
        global $show_debug_alert_messages;

        if ($show_debug_alert_messages) {
            echo "<script type='text/javascript'>alert('" . $message . "');</script>";
        }
    }

    function executePlainSQL($cmdstr, $bindings = null) { //takes a plain (no bound variables) SQL command and executes it
        //echo "<br>running ".$cmdstr."<br>";
        global $db_conn, $success;

        $statement = OCIParse($db_conn, $cmdstr);
        //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

        if (!$statement) {
            echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
            echo htmlentities($e['message']);
            $success = False;
        }

        if ($bindings) {
            foreach ($bindings as $bind => $val) {
                OCIBindByName($statement, $bind, $val);
            }
        }
        
        $r = OCIExecute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
            echo htmlentities($e['message']);
            $success = False;
        } else {
            // Operation was successful
            if($success){
                echo "<h3>Operation successful<h3>";
            }
        }

        return $statement;
    }

    function executeBoundSQL($cmdstr, $list) {
        global $db_conn, $success;
        $statement = OCIParse($db_conn, $cmdstr);
    
        if (!$statement) {
            echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($db_conn);
            echo htmlentities($e['message']);
            $success = False;
        }

        $result = array();
    
        foreach ($list as $tuple) {
            foreach ($tuple as $bind => $val) {
                OCIBindByName($statement, $bind, $val);
                unset($val);
            }
    
            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($statement);
                echo htmlentities($e['message']);
                echo "<br>";
                $success = False;
                // If one execution fails, stop processing further and exit the loop
                break;
            }
        }
    
        // Display success message only if all commands were successful
        if ($success) {
            echo "<h3>Operation successful<h3>";
        }

    }

    function printResult($result) { //prints results from a select statement
        echo "<br>Retrieved data from table demoTable:<br>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }

    function connectToDB() {
        global $db_conn;

        debugAlertMessage("test");

        // Your username is ora_(CWL_ID) and the password is a(student number). For example,
        // ora_platypus is the username and a12345678 is the password.
        $db_conn = OCILogon("ora_robinmth", "a80425994", "dbhost.students.cs.ubc.ca:1522/stu");

        if ($db_conn) {
            debugAlertMessage("Database is Connected");
            return true;
        } else {
            debugAlertMessage("Cannot connect to Database");
            $e = OCI_Error(); // For OCILogon errors pass no handle
            echo htmlentities($e['message']);
            return false;
        }
    }

    function disconnectFromDB() {
        global $db_conn;

        debugAlertMessage("Disconnect from Database");
        OCILogoff($db_conn);
    }

    /* QUERIES */
 
    function handleAnimalInsertRequest() {
        global $db_conn;
    
        /* Sanitize data */
        $name = filter_var($_POST['insAnimalName'], FILTER_SANITIZE_STRING);
        $type = filter_var($_POST['insAnimalType'], FILTER_SANITIZE_STRING);
        $age = ($_POST['insAge'] !== '') ? filter_var($_POST['insAge'], FILTER_VALIDATE_INT) : null;
        $care = ($_POST['insFavCare'] !== '') ? filter_var($_POST['insFavCare'], FILTER_VALIDATE_INT) : null;
        $prev_owner = ($_POST['insPrevOwner'] !== '') ? filter_var($_POST['insPrevOwner'], FILTER_VALIDATE_INT) : null;
        $arrivalYear = filter_var($_POST['arrivalYear'], FILTER_VALIDATE_INT);
        $arrivalMonth = filter_var($_POST['arrivalMonth'], FILTER_VALIDATE_INT);
        $arrivalDay = filter_var($_POST['arrivalDay'], FILTER_VALIDATE_INT);
        $adopter = ($_POST['insAdopterID'] !== '') ? filter_var($_POST['insAdopterID'], FILTER_VALIDATE_INT) : null;
    
        if ($name === false) {
            echo "Error: Invalid name";
            return;
        } else if ($type === false) {
            echo "Error: Invalid animal type";
            return;
        } elseif ($age === false) {
            echo "Error: Invalid age";
            return;
        } elseif ($care === false) {
            echo "Error: Invalid caretaker ID";
            return;
        } elseif ($prev_owner === false) {
            echo "Error: Invalid previous owner";
            return;
        } elseif ($arrivalYear === false || $arrivalMonth === false || $arrivalDay === false) {
            echo "Error: Invalid arrival date";
            return;
        } elseif ($adopter === false) {
            echo "Error: Invalid adopter ID";
            return;
        }
    
        /* Check caretaker ID */
        if ($care !== null) {
            if (!checkForeignKey($care, "caretakerID", "AnimalCaretaker")) {
                echo "Error: Invalid caretaker ID";
                return;
            }
        }
    
        /* Check customer ID */
        if ($prev_owner !== null) {
            if (!checkForeignKey($prev_owner, "customerID", "Customer")) {
                echo "Error: Invalid customer ID";
                return;
            }
        }
    
        /* Check adopter ID */
        if ($adopter !== null) {
            if (!checkForeignKey($adopter, "adopterID", "Adopter")) {
                echo "Error: Invalid adopter ID";
                return;
            }
        }
    
        /* Construct the Arrival Date in the format 'YYYY-MM-DD' */
        $arrivalDate = sprintf("%04d-%02d-%02d", $arrivalYear, $arrivalMonth, $arrivalDay);

        $tuple = array(
            ":bind1" => $name,
            ":bind2" => $type,
            ":bind3" => $age,
            ":bind4" => $care,
            ":bind5" => $prev_owner,
            ":bind6" => $arrivalDate,
            ":bind7" => $adopter
        );
    
        $alltuples = array(
            $tuple
        );
    
        executeBoundSQL("INSERT INTO Animal (animalName, type, age, favouriteCaretaker, previousOwner, arrivalDate, adopterID) VALUES (:bind1, :bind2, :bind3, :bind4, :bind5, TO_DATE(:bind6, 'YYYY-MM-DD'), :bind7)", $alltuples);
        OCICommit($db_conn);
    }
    
    function handleAnimalDeleteRequest() {
        global $db_conn;

        $petID = filter_var($_POST['delPetID'], FILTER_VALIDATE_INT);

        if($petID === false){
            echo "Error: Invalid input for Pet ID.";
            return;
        }

        if (!isPetIDValid($petID)) {
            echo "Error: Pet with ID $petID not found.";
            return;
        }

        $tuple = array (
            ":bind1" => $petID
        );
        
        $alltuples = array (
            $tuple
        );

        executeBoundSQL("DELETE FROM Animal WHERE petID = :bind1", $alltuples);

        OCICommit($db_conn);

    }

    function handleAnimalUpdateRequest() {
        global $db_conn;

        $petID = filter_var($_POST['upPetID'], FILTER_VALIDATE_INT);
        $age = ($_POST['upAge'] !== '') ? filter_var($_POST['upAge'], FILTER_VALIDATE_INT) : null;
        $care = ($_POST['upFavCare'] !== '') ? filter_var($_POST['upFavCare'], FILTER_SANITIZE_STRING) : null;
        $adopter = ($_POST['upAdopterID'] !== '') ? filter_var($_POST['upAdopterID'], FILTER_VALIDATE_INT) : null;

        if ($petID === false){
            echo "Error: Invalid input for Pet ID.";
            return;
        } else if ($age === false) {
            echo "Error: Invalid input for age.";
            return;
        } else if ($care === false) {
            echo "Error: Invalid input for age.";
            return;
        } else if ($adopter === false) {
            echo "Error: Invalid input for age.";
            return;
        }

        /* Check that the petID provided by the user is valid */
        if (!isPetIDValid($petID)) {
            echo "Error: Pet with ID $petID not found.";
            return;
        }
        
        $query = "UPDATE Animal SET ";

        $tuple = array();

        if ($care !== null) {
            if (!checkForeignKey($care, "caretakerID", "AnimalCaretaker")) {
                echo "Error: Invalid caretaker ID";
                return;
            }
            $query .= "favouriteCaretaker = :care, ";
            $tuple[':care'] = $care;
        }

        if ($adopter !== null) {
            if (!checkForeignKey($adopter, "adopterID", "Adopter")) {
                echo "Error: Invalid adopter ID";
                return;
            }
            $query .= "adopterID = :adopter, ";
            $tuple[':adopter'] = $adopter;
        }

        if ($age !== null) {
            $query .= "age = :age, ";
            $tuple[':age'] = $age;
        }

        $query = rtrim($query, ', ');

        $query .= " WHERE petID = :petID";
        $tuple[':petID'] = $petID;

        $alltuples = array (
            $tuple
        );

        executeBoundSQL($query, $alltuples);

        OCICommit($db_conn);
    }
    
    /* Check that the petID provided by the user is valid */
    function isPetIDValid($petID) {
        global $db_conn, $success;
    
        $query = "SELECT COUNT(*) AS count FROM Animal WHERE petID = :bind1";
        $binds = array(":bind1" => $petID);
    
        $statement = OCIParse($db_conn, $query);
    
        if (!$statement) {
            echo "<br>Cannot parse the following command: " . $query . "<br>";
            $e = OCI_Error($db_conn);
            echo htmlentities($e['message']);
            $success = False;
        }
    
        foreach ($binds as $bind => $val) {
            OCIBindByName($statement, $bind, $val);
            unset($val);
        }
    
        $r = OCIExecute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<br>Cannot execute the following command: " . $query . "<br>";
            $e = OCI_Error($statement);
            echo htmlentities($e['message']);
            echo "<br>";
            $success = False;
        }
    
        $result = OCI_Fetch_Array($statement, OCI_ASSOC);

        return $result['COUNT'] > 0;
    }

    /* Check that a foreign key value exists in the other table */
    function checkForeignKey($foreign_key, $attribute_name, $table) {
        global $db_conn, $success;
    
        $query = "SELECT COUNT(*) AS count FROM $table WHERE $attribute_name = :bind1";
        $binds = array(":bind1" => $foreign_key);
    
        $statement = OCIParse($db_conn, $query);
    
        if (!$statement) {
            echo "<br>Cannot parse the following command: " . $query . "<br>";
            $e = OCI_Error($db_conn);
            echo htmlentities($e['message']);
            $success = False;
        }
    
        foreach ($binds as $bind => $val) {
            OCIBindByName($statement, $bind, $val);
            unset($val);
        }
    
        $r = OCIExecute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<br>Cannot execute the following command: " . $query . "<br>";
            $e = OCI_Error($statement);
            echo htmlentities($e['message']);
            echo "<br>";
            $success = False;
        }
 
        $result = OCI_Fetch_Array($statement, OCI_ASSOC);
    
        return $result['COUNT'] > 0;
    }
    
    function handleSelectionRequest() {
        global $db_conn;
    
        $careID = ($_GET['selectionCareID'] !== '') ? filter_var($_GET['selectionCareID'], FILTER_VALIDATE_INT) : null;
        $careName = ($_GET['selectionName'] !== '') ? "'" . filter_var($_GET['selectionName'], FILTER_SANITIZE_STRING) . "'" : null;
        $fundraiserID = ($_GET['selectionFundEvent'] !== '') ? filter_var($_GET['selectionFundEvent'], FILTER_VALIDATE_INT) : null;
        $address = ($_GET['selectionAddress'] !== '') ? "'" . filter_var($_GET['selectionAddress'], FILTER_SANITIZE_STRING) . "'" : null;
        $postalCode = ($_GET['selectionPostal'] !== '') ? "'" . filter_var($_GET['selectionPostal'], FILTER_SANITIZE_STRING) . "'" : null;
    
        if ($careID === false) {
            echo "Error: Invalid caretaker ID";
            return;
        } elseif ($careName === false || containsSqlKeywords($careName)) {
            echo "Error: Invalid caretaker name";
            return;
        } elseif ($fundraiserID === false) {
            echo "Error: Invalid fundraiser ID";
            return;
        } elseif ($address === false || containsSqlKeywords($address)) {
            echo "Error: Invalid address";
            return;
        } elseif ($postalCode === false || containsSqlKeywords($postalCode)) {
            echo "Error: Invalid postal code";
            return;
        }
        
        $query = "SELECT * FROM AnimalCaretaker WHERE ";
    
        $conditions = array();
        if ($careID !== null) {
            $conditions[] = "caretakerID = $careID";
        }
        if ($careName !== null ) {
            $conditions[] = "caretakerName = $careName";
        }
        if ($fundraiserID !== null) {
            $conditions[] = "fundEventID = $fundraiserID";
        }
        if ($address !== null) {
            $conditions[] = "caretakerAddress = $address";
        }
        if ($postalCode !== null) {
            $conditions[] = "caretakerPostalCode = $postalCode";
        }
    
        $query .= implode(" AND ", $conditions);
    
        $result = executePlainSQL($query);

        /* Display the result of the query as a formatted table */
        echo "<h1>Search Results</h1>";
        echo "<h2>Caretakers matching the user specifications</h2>";
        echo "<table>";
        echo "<tr><th>Caretaker ID</th><th>Caretaker Name</th><th>Fundraiser ID</th><th>Address</th><th>Postal Code</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['CARETAKERID'] . "</td>";
            echo "<td>" . $row['CARETAKERNAME'] . "</td>";
            echo "<td>" . $row['FUNDEVENTID'] . "</td>";
            echo "<td>" . $row['CARETAKERADDRESS'] . "</td>";
            echo "<td>" . $row['CARETAKERPOSTALCODE'] . "</td>";
            echo "</tr>";
        }

        echo "</table>";

        OCICommit($db_conn);
    }
    
    function containsSqlKeywords($input) {
        $sqlKeywords = array('SELECT', 'INSERT', 'UPDATE', 'DELETE', 'DROP', 'ALTER', 'CREATE', 'TRUNCATE');
    
        foreach ($sqlKeywords as $keyword) {
            if (stripos($input, $keyword) !== false) {
                return true;
            }
        }
    
        return false;
    }

    function handleJoinRequest(){
        global $db_conn;
    
        $donation = ($_GET['donationAmount'] !== '') ? filter_var($_GET['donationAmount'], FILTER_VALIDATE_INT) : null;
        
        if($donation === false){
            echo "Error: Donation must be an integer value.";
            return;
        }
    
        $query = "SELECT Customer.customerName, Donation.amount FROM Customer
        JOIN Donation ON Customer.customerID = Donation.customerID
        WHERE Donation.amount > :bind1";

        $bindings = array(':bind1' => $donation);
    
        $result = executePlainSQL($query, $bindings);

        /* Display the result of the query as a formatted table */
        echo "<h1>Search Results</h1>";
        echo "<h2>Customers with donation above $$donation</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Customer Name</th><th>Donation Amount</th></tr>";
    
        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['CUSTOMERNAME'] . "</td>";
            echo "<td>" . $row['AMOUNT'] . "</td>";
            echo "</tr>";
        }
    
        echo "</table>";
    
        OCICommit($db_conn);
    }
    
    function handleProjectionRequest() {
        global $db_conn;
    
        // getting info which attribute checkboxes were selected when the query request is submitted
        $selectedAttributes = isset($_POST['projectionAttributes']) ? $_POST['projectionAttributes'] : array();

        $query = "SELECT ";

        foreach ($selectedAttributes as $attribute) {
            if ($attribute == "petID") {
                $query .= "petID, "; 
            }
            if ($attribute == "animalName") {
                $query .= "animalName, "; 
            }
            if ($attribute == "type") {
                $query .= "type, "; 
            }
            if ($attribute == "age") {
                $query .= "age, "; 
            }
            if ($attribute == "favouriteCaretaker") {
                $query .= "favouriteCaretaker, "; 
            }
            if ($attribute == "previousOwner") {
                $query .= "previousOwner, "; 
            }
            if ($attribute == "arrivalDate") {
                $query .= "arrivalDate, "; 
            }
            if ($attribute == "adopterID") {
                $query .= "adopterID, "; 
            }
        }

        $query = rtrim($query, ", ") . " FROM Animal";
        $result = executePlainSQL($query);

        echo "<h2>Search Results</h2>";
        echo "<table>";
        
        $columnHeaders = !empty($selectedAttributes) ? $selectedAttributes : array("petID", "animalName", "type", "age", "favouriteCaretaker", "previousOwner", "arrivalDate", "adopterID");
        
        echo "<tr>";
        foreach ($columnHeaders as $header) {
            echo "<th>$header</th>";
        }
        echo "</tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr>";
            foreach ($row as $element) {
                echo "<td>" . $element . "</td>";
            }
            echo "</tr>";
        }

        echo "</table>";

        OCICommit($db_conn);
    }


    function handleGroupByRequest(){
        global $db_conn;

        $animal_type = ($_GET['animalType'] !== '') ? "'" . filter_var($_GET['animalType'], FILTER_SANITIZE_STRING) . "'" : null;

        if ($animal_type === false || containsSqlKeywords($animal_type)) {
            echo "Error: Animal type must be a string value";
            return;
        }
    
        $query = "SELECT type, COUNT(*) as typeCount 
                FROM Animal 
                WHERE type = $animal_type
                GROUP BY type"; 

        $result = executePlainSQL($query);

        echo "<h2> Search Results</h2>";
        echo "<table>";
        echo "<tr><th>Animal Type</th><th>TypeCount</th></tr>";

        while($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            foreach($row as $element) {
                echo "<td>" . $element . "</td>";
            }
        }

        echo "</table>";

        OCICommit($db_conn);
    }
    
    function handleAggregationHavingRequest() {
        global $db_conn;
    
        $donation = ($_GET['havingAvgDonationGoalThreshold'] !== '') ? filter_var($_GET['havingAvgDonationGoalThreshold'], FILTER_VALIDATE_INT) : null;

        if($donation === false){
            echo "Error: Average Donation Amount must be an integer value.";
            return;
        }
    
        $query = "SELECT FundraiserEvent.eventType, AVG(FundraiserEvent.donationGoal) AS avgDonationGoal 
        FROM FundraiserEvent
        GROUP BY eventType
        HAVING AVG(FundraiserEvent.donationGoal) >= :bind1";

        $bindings = array(':bind1' => $donation);

        $result = executePlainSQL($query, $bindings);
    
        echo "<h2>Search Results</h2>";
        echo "<table>";
        echo "<tr><th>Event Type</th><th>Donation Goal</th></tr>";
    
        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr>";
            foreach($row as $element) {
                echo "<td>" . $element . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";

        OCICommit($db_conn);
    }

    function handleNestedGroupByRequest() {
        global $db_conn;

        $query = "SELECT C.customerID, C.customerName, COUNT(*) AS purchaseCount
        FROM Customer C
        JOIN ItemPurchase I ON C.customerID = I.customerID
        GROUP BY C.customerID, C.customerName
        HAVING COUNT(*) >= (SELECT AVG(purchaseCount2)
                           FROM (SELECT customerID, COUNT(*) AS purchaseCount2
                                 FROM ItemPurchase
                                 GROUP BY customerID))";


        $result = executePlainSQL($query);
    
        echo "<h2>Search Results</h2>";
        echo "<table>";
        echo "<tr><th>Customer ID</th><th>Customer Name</th><th>Number of items purchased</tr>";
    
        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr>";
            foreach($row as $element) {
                echo "<td>" . $element . "</td>";
            }
            echo "</tr>";
        }
    
        echo "</table>";
    
        OCICommit($db_conn);
    }

    
    function handleDivisionRequest() {
        global $db_conn;
    
        $query = "SELECT AC.caretakerID, AC.caretakerName
        FROM AnimalCaretaker AC
        WHERE NOT EXISTS (SELECT DISTINCT A.type FROM Animal A MINUS SELECT DISTINCT A.type FROM Animal A, AdoptionDetails AD WHERE AD.caretakerID = AC.caretakerID AND AD.petID = A.petID)";


        $result = executePlainSQL($query);

        echo "<h2>Search Results</h2>";
        echo "<table>";
        echo "<tr><th>Caretaker ID</th><th>Caretaker Name</th></tr>";
    
        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr>";
            foreach($row as $element) {
                echo "<td>" . $element . "</td>";
            }
            echo "</tr>";
        }
    
        echo "</table>";
    
        OCICommit($db_conn);
    }

    function handleResetRequest() {
        global $db_conn;
	
	if (!file_exists('script.sql')) {
		echo "Error: File not found - script.sql";
		return;
	}
	
        $sqlScript = file_get_contents('script.sql');
        $sqlStatements = explode(';', $sqlScript);
	foreach ($sqlStatements as $sqlStatement) {
		$sqlStatement = trim($sqlStatement);
		if (!empty($sqlStatement)) {
			executePlainSQL($sqlStatement);
		}
	}
	
        OCICommit($db_conn);       
    }
    
    function handleVerificationRequest(){
        global $db_conn;

        $result = executePlainSQL("SELECT * FROM Animal");

        if($result === false){
            echo "Can't execute query";
        }

        /* Display the result of the query as a formatted table */
        echo "<h2>Animal Table</h2>";
        echo "<table>";
        echo "<tr><th>Pet ID</th><th>Animal Name</th><th>Animal Type</th><th>Animal Age</th><th>Favourite Caretaker ID</th><th>Previous Owner</th><th>Arrival Date</th><th>Adopter ID</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['PETID'] . "</td>";
            echo "<td>" . $row['ANIMALNAME'] . "</td>";
            echo "<td>" . $row['TYPE'] . "</td>";
            echo "<td>" . $row['AGE'] . "</td>";
            echo "<td>" . $row['FAVOURITECARETAKER'] . "</td>";
            echo "<td>" . $row['PREVIOUSOWNER'] . "</td>";
            echo "<td>" . $row['ARRIVALDATE'] . "</td>";
            echo "<td>" . $row['ADOPTERID'] . "</td>";
            
            echo "</tr>";
        }

        echo "</table>";

        OCICommit($db_conn);
    }

    // HANDLE ALL POST ROUTES
    // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
    function handlePOSTRequest() {
        if (connectToDB()) {
            if (array_key_exists('insertAnimalSubmit', $_POST)) {
                handleAnimalInsertRequest();
            } else if (array_key_exists('deleteAnimalSubmit', $_POST)) {
                handleAnimalDeleteRequest();
            } else if (array_key_exists('updateAnimalSubmit', $_POST)) {
                handleAnimalUpdateRequest();
            } else if (array_key_exists('resetTablesRequest', $_POST)) {
	        handleResetRequest();
	        } else if (array_key_exists('projectionSubmit', $_POST)) {
                handleProjectionRequest();
            } else if (array_key_exists('verifyAnimalTable', $_POST)) {
                handleVerificationRequest();
            }
            disconnectFromDB();
        }
    }

    // HANDLE ALL GET ROUTES
    // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
    function handleGETRequest() {
        if (connectToDB()) {
            if (array_key_exists('selectionSubmit', $_GET)) {
                handleSelectionRequest();
            } else if (array_key_exists('donationSubmit', $_GET)) {
                handleJoinRequest();
            } else if (array_key_exists('groupBySubmit', $_GET)) {
                handleGroupByRequest();
            } else if (array_key_exists('havingSubmit', $_GET)) {
                handleAggregationHavingRequest();
            } else if (array_key_exists('nestedSubmit', $_GET)) {
                handleNestedGroupByRequest();
            } else if (array_key_exists('divisionSubmit', $_GET)) {
                handleDivisionRequest();
            } 

            disconnectFromDB();
        }
    }
?>
