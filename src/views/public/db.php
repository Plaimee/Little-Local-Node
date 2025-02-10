<?php
    $servername = "localhost";
    $username = "cp094466_artistr";
    $password = ",7}IU9;H7W%";
    $dbname = "cp094466_func_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>