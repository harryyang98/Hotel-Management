<?php
include ("connection.php");
include ("general_functions.php");

function handleInsertGuestRequest() {
    echo "handing guest insertion <br>";
    global $db_conn;
    $customer_ID = rand(1000, 9999);
    while (($row = oci_fetch_row(executePlainSQL("SELECT * FROM Customer WHERE customer_ID = " . $customer_ID))) != false) {
        $customer_ID = rand(1000, 9999);
    }
    //Getting the values from user and insert data into the table
    $customer_tuple = array (
        ":customer_ID" => $customer_ID,
        ":customer_name" => $_POST['name'],
        ":customer_email" => $_POST['email'],
        ":phone_number" => $_POST['phone'],
        ":department_ID" => 1,
        ":password" => $_POST['password']
    );

    $customer_tuples = array (
        $customer_tuple
    );
    echo "Input tuple: ";
    print_r($customer_tuple);
    executeBoundSQL("insert into Customer values (:customer_ID,
                                                  :customer_name,
                                                  :customer_email,
                                                  :phone_number,
                                                  :department_ID,
                                                  :password)", $customer_tuples);
    OCICommit($db_conn);
}

function handlePOSTRequest() {
    echo "handling POST <br>";
    if (connectToDB()) {
        if (array_key_exists('register', $_POST)) {
            handleInsertGuestRequest();
        }

        disconnectFromDB();
    }
}

echo "execution reached<br>";
if (isset($_POST['register'])) {
    handlePOSTRequest();
}

