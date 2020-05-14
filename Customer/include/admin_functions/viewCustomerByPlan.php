<html>
<body>
  <div>
    <h2>View Customers By Their Plan</h2>
    <form action = "viewCustomerByPlan.php" method="POST">
                <fieldset class="box">
                    <legend class="headerText">Plan:</legend>
                    <input name="plan" type="radio" value="A"/> Type A <br />
                    <input name="plan" type="radio" value="B"/> Type B <br />
                    <input name="plan" type="radio" value="C"/> Type C <br />
                </fieldset>
                <form action = "viewCustomerByPlan.php" method="GET">
                    <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
                    <button class="button" name="insertSubmit" id="insertSubmit" style="vertical-align: middle"><span>Submit</span></button>
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

        function disconnectFromDB() {
            global $db_conn;

            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }

        function displayCustomer() {
            global $Customer;
            global $HotelPlan;

            $type="";

            if ($_POST["plan"] == "A") {
                $type = "A";
            } else if ($_POST["plan"] == "B") {
                $type = "B";
            } else if ($_POST["plan"] == "C") {
                $type = "C";
            }

            $result = executePlainSQL(

                  "SELECT Customer.customer_name, Customer.customer_ID FROM Customer JOIN HotelPlan ON Customer.customer_ID = HotelPlan.customer_ID WHERE HotelPlan.type='$type' ORDER BY Customer.customer_ID");
                    printResult($result);

            $numOfCustomerPerPlan = executePlainSQL(
                "SELECT COUNT(*), type FROM HotelPlan Group by type");
                printNum($numOfCustomerPerPlan);

            $numOfCustomer = executePlainSQL(
              "SELECT COUNT(*) FROM Customer");
                printNumC($numOfCustomer);

        }

        function printNumC($result) {
          echo "<center><table></center>";
          echo "<h3>Number of customers</h3>";
          $list = array();
          while ($r = OCI_Fetch_Array($result, OCI_BOTH)) {
              array_push($list, $r);
          }
          if (sizeof($list) != 0) {
              echo "<tr><th>Customer number</th></tr>";
              foreach ($list AS $row) {
                  echo "<tr><td>" . $row["0"] .  "</td></tr>";
              }
          } else {
              echo "There is no customer within this type";
          }
          echo "</table>";
      }

        function printResult($result) {
          echo "<center><table></center>";
          echo "<h3>All the customers within the type of plan</h3>";
          $list = array();
          while ($r = OCI_Fetch_Array($result, OCI_BOTH)) {
              array_push($list, $r);
          }
          if (sizeof($list) != 0) {
              echo "<tr><th>Customer name</th><th>Customer ID</th></tr>";
              foreach ($list AS $row) {
                  echo "<tr><td>" . $row["0"] . "</td><td>" . $row["1"] . "</td></tr>";
              }
          } else {
              echo "There is no customer within this type";
          }
          echo "</table>";
      }

      function printNum($result) {
        echo "<center><table></center>";
        echo "<h3>All the number of customers within each type of plan</h3>";
        $list = array();
        while ($r = OCI_Fetch_Array($result, OCI_BOTH)) {
            array_push($list, $r);
        }
        if (sizeof($list) != 0) {
            echo "<tr><th>number</th><th>type</th></tr>";
            foreach ($list AS $row) {
                echo "<tr><td>" . $row["0"] . "</td><td>" . $row["1"] . "</td></tr>";
            }
        } else {
            echo "There is no customer within this type";
        }
        echo "</table>";
    }

        // HANDLE ALL GET ROUTES
        // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('insertQueryRequest', $_POST)) {
                    displayCustomer();
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
