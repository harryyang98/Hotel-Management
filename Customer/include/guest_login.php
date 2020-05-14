<?php

        include ("connection.php");

        include ("general_functions.php");

        function handleLoginRequest() {
            global $db_conn;
            echo "handling login <br>";
            $result = executePlainSQL("SELECT customer_ID, customer_name FROM Customer WHERE customer_email = '" . $_POST[email] . "' AND password = '" . $_POST[password] . "'");
            if (($c_info = oci_fetch_row($result)) != false) {
                echo "logged in as '" . $c_info[1] . "'<br>";
                setcookie("customer_ID", $c_info[0]);
                setcookie("customer_name", $c_info[1]);
                header('Location: personal_page.php');
            } else {
                echo "<script type='text/javascript'>alert('email or password is incorrect');</script>";
            }

        }

        function handlePOSTRequest() {
            echo "handling POST <br>";
            if (connectToDB()) {
                if (array_key_exists('login', $_POST)) {
                    handleLoginRequest();
                }

                disconnectFromDB();
            }
        }


        echo "execution reached<br>";
        if (isset($_POST['login'])) {
            handlePOSTRequest();
        }
?>