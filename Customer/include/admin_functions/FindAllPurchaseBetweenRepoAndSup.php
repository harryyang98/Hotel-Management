<html>
<body>
  <div>
    <h2>View purchase between specific Repository and Supplier</h2>
    <form action = "FindAllPurchaseBetweenRepoAndSup.php" method="POST">
                <fieldset class="box">
                    <legend class="headerText">Storage:</legend>
                    <input name="Repository" type="radio" value="1"/> Storage 1 <br />
                    <input name="Repository" type="radio" value="2"/> Storage 2 <br />
                    <input name="Repository" type="radio" value="3"/> Storage 3 <br />
                </fieldset>

                <fieldset class="box">
                  <legend class="headerText">Supplier:</legend>
                  <input name="Supplier" type="radio" value="1"/> Supplier 1 <br />
                  <input name="Supplier" type="radio" value="2"/> Supplier 2 <br />
                  <input name="Supplier" type="radio" value="3"/> Supplier 3 <br />
                </fieldset>

                <form action = "FindAllPurchaseBetweenRepoAndSup.php" method="GET">
                    <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
                    <button class="button" name="insertSubmit" id="insertSubmit" style="vertical-align: middle"><span>Find</span></button>
                </form>
            </form>
      <form action="../../admin.php" method="GET">
          <button class="button" style="vertical-align: middle"><span>Back</span></button>
      </form>
  </div>

    <?php
        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

        function debugAlertMessage($message) {
            global $show_debug_alert_messages;

            if ($show_debug_alert_messages) {
                echo "<script type='text/javascript'>alert('" . $message . "');</script>";
            }
        }

        function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
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

//            echo "<br> here's the sql: " . $cmdstr ."<br>";
            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
              //  echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                echo htmlentities($e['message']);
                $success = False;
            }

//            echo "<br> here's the result: " . var_dump(oci_fetch_row($statement)) ."<br>";
            return $statement;
        }

        function executeBoundSQL($cmdstr, $list) {
            /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
		See the sample code below for how this function is used */

            global $db_conn, $success;
            $statement = OCIParse($db_conn, $cmdstr);

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn);
                echo htmlentities($e['message']);
                $success = False;
            }

            foreach ($list as $tuple) {
                foreach ($tuple as $bind => $val) {
                    //echo $val;
                    //echo "<br>".$bind."<br>";
                    OCIBindByName($statement, $bind, $val);
                    unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
                }

                $r = OCIExecute($statement, OCI_DEFAULT);
                if (!$r) {
                    echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                    $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                    echo htmlentities($e['message']);
                    echo "<br>";
                    $success = False;
                }
            }
        }

        function connectToDB() {
            global $db_conn;

            // Your username is ora_(CWL_ID) and the password is a(student number). For example,
            // ora_platypus is the username and a12345678 is the password.
            $db_conn = OCILogon("ora_ketaha", "a43459262", "dbhost.students.cs.ubc.ca:1522/stu");

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

        function printResult($result) { //prints results from a select statement
            echo "<center><table></center>";
            echo "<h3>All the products between the purchases of the Storage and Supplier</h3>";
            $list = array();
            while ($r = OCI_Fetch_Array($result, OCI_BOTH)) {
                array_push($list, $r);
            }
            if (sizeof($list) != 0) {
                echo "<tr><th>Storage ID</th><th>Supplier ID</th><th>Product</th></tr>";
                foreach ($list AS $row) {
                    echo "<tr><td>" . $row["0"] . "</td><td>" . $row["1"] . "</td><td>" . $row["2"] . "</td></tr>";
                }
            } else {
                echo "There is no purchase in between";
            }
            echo "</table>";
        }

        function displayResult() {
          global $Storage;
          global $Supplier;
          global $Purchase_from;
            $rep="";
            $sup="";
            if ($_POST["Repository"] == "1") {
                $rep = 1;
            } else if ($_POST["Repository"] == "2") {
                $rep = 2;
            } else if ($_POST["Repository"] == "3") {
                $rep = 3;
            }

            if ($_POST["Supplier"] == "1") {
                $sup = 1;
            } else if ($_POST["Supplier"] == "2") {
                $sup = 2;
            } else if ($_POST["Supplier"] == "3") {
                $sup = 3;
            }

            $result = executePlainSQL(
                    "SELECT Storage.storage_ID, Supplier.supplier_ID, Purchase_from.product FROM Storage JOIN Purchase_from ON Purchase_from.storage_ID = Storage.storage_ID JOIN Supplier ON Supplier.supplier_ID = Purchase_from.supplier_ID WHERE Supplier.supplier_ID='$sup' AND Storage.storage_ID ='$rep' ");
                    printResult($result);
        }

        function disconnectFromDB() {
            global $db_conn;

            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }

        // HANDLE ALL GET ROUTES
        // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('insertQueryRequest', $_POST)) {
                    displayResult();
                }

                disconnectFromDB();
            }
        }

        if (isset($_POST['insertSubmit'])) {
            handleGETRequest();
        }

    ?>
</body>

</html>
