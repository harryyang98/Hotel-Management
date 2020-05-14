<?php
	include ("connection.php");

    include ("general_functions.php");

    function printBookedRoom($result) { //prints results from a select statement
        echo "<table>";
        echo "<tr><th>Room Number</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row[0] . "</td></tr>"; //or just use "echo $row[0]" 
        }

        echo "</table>";
    }

    function handleAddBookingRequest() {
        global $db_conn;
        global $customer_ID;

        $room_number = $_POST["roomNumber"];
        // echo "handing Add Booking for " . $customer_ID . " with " . $room_number . "<br>";
        // you need the wrap the old name and new name values with single quotations
        executePlainSQL("UPDATE Room SET customer_ID='" . $customer_ID . "' WHERE room_number='" . $room_number . "'");
        OCICommit($db_conn);
    }

    function handleCancelBookingRequest() {
        global $db_conn;
        global $customer_ID;

        $room_number = $_POST["roomNumber"];
        // echo "handing deleting Booking for " . $customer_ID . " with " . $room_number . "<br>";
        // you need the wrap the old name and new name values with single quotations
        executePlainSQL("UPDATE Room SET customer_ID='" . null . "' WHERE room_number='" . $room_number . "'");
        OCICommit($db_conn);
    }

    function printOrderedFood($result) { //prints results from a select statement
        echo "<table>";
        echo "<tr><th>Food Name</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row[0] . "</td></tr>"; //or just use "echo $row[0]" 
        }

        echo "</table>";
    }

    function printMenu($result) { //prints results from a select statement
        $showPrice = isset($_POST["showPrice"]) ? true : false;
        if ($showPrice) {
            # code...
            echo "<table>";
            echo "<tr><th>Food Name</th><th>Price</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>"; //or just use "echo $row[0]" 
            }

            echo "</table>";
        } else {
            echo "<table>";
            echo "<tr><th>Food Name</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td></tr>"; //or just use "echo $row[0]" 
            }

            echo "</table>";
        }
    }

    function handleAddDishRequest() {
        global $db_conn;
        global $customer_ID;

        $dish_name = $_POST["foodName"];
        // echo "handing Add Booking for " . $customer_ID . " with " . $room_number . "<br>";
        // you need the wrap the old name and new name values with single quotations
        $raw_dish_ID = executePlainSQL("SELECT dish_ID FROM Dish WHERE dish_name = '" . $dish_name . "'");
        $row = oci_fetch_row($raw_dish_ID);
        $dish_ID = $row[0];
        executePlainSQL("INSERT INTO Order_Dish VALUES ('". $customer_ID . "', '" . $dish_ID . "')");
        OCICommit($db_conn);
    }

    function handleDeleteDishRequest() {
        global $db_conn;
        global $customer_ID;

        $dish_name = $_POST["foodName"];
        // echo "handing Add Booking for " . $customer_ID . " with " . $room_number . "<br>";
        // you need the wrap the old name and new name values with single quotations
        $raw_dish_ID = executePlainSQL("SELECT dish_ID FROM Dish WHERE dish_name = '" . $dish_name . "'");
        $row = oci_fetch_row($raw_dish_ID);
        $dish_ID = $row[0];
        executePlainSQL("DELETE FROM Order_Dish WHERE dish_ID = '" . $dish_ID . "' AND customer_ID = '" . $customer_ID . "'");
        OCICommit($db_conn);
    }


    function handleChangePlanRequest() {
        global $db_conn;
        global $customer_ID;

        $type = $_POST["planType"];
        // echo "handing deleting Booking for " . $customer_ID . " with " . $room_number . "<br>";
        // you need the wrap the old name and new name values with single quotations
        $result = executePlainSQL("SELECT * FROM HotelPlan WHERE customer_ID = '" . $customer_ID . "'");
        if (($row = oci_fetch_row($result)) != false) {
            executePlainSQL("UPDATE HotelPlan SET type ='" . $type . "', start_date = TO_DATE('" . date('Y-m-d') . "', 'yyyy-mm-dd') WHERE customer_ID = '" . $customer_ID . "'");
        } else {
            executePlainSQL("INSERT INTO HotelPlan VALUES ('" . $type . "', TO_DATE('" . date('Y-m-d') . "', 'yyyy-mm-dd'), '" . $customer_ID . "')");
        }
        OCICommit($db_conn);
    }

    function handleDeleteAccountRequest() {
        global $db_conn;
        global $customer_ID;

        executePlainSQL("DELETE FROM Customer WHERE customer_ID = " . $customer_ID);
        OCICommit($db_conn);
        header('Location: customer_login.php');
    }

    function handlePOSTRequest() {
        // echo "handling POST <br>";
        if (connectToDB()) {
            if (array_key_exists('addBooking', $_POST)) {
                handleAddBookingRequest();
            } else if (array_key_exists('cancelBooking', $_POST)) {
                handleCancelBookingRequest();
            } else if (array_key_exists('addDish', $_POST)) {
                handleAddDishRequest();
            } else if (array_key_exists('deleteDish', $_POST)) {
                handleDeleteDishRequest();
            } else if (array_key_exists('changePlan', $_POST)) {
                handleChangePlanRequest();
            } else if (array_key_exists('deleteAccount', $_POST)) {
                handleDeleteAccountRequest();
            }

            disconnectFromDB();
        }
    }


    // echo "execution reached<br>";
    if (isset($_POST['addBooking']) || 
    	isset($_POST['cancelBooking']) || 
    	isset($_POST['addDish']) || 
    	isset($_POST['deleteDish']) || 
    	isset($_POST['changePlan']) ||
        isset($_POST['deleteAccount'])) {
        handlePOSTRequest();
    }
