<?php

$servername = "localhost";
$dbname = "main";
$dbusername = "root"; 
$dbpassword = ""; 


$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['username'])) {
    $username = $_POST['username'];


    $sql = "SELECT * FROM new_users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result(); 

    if ($stmt->num_rows > 0) {
        
        echo 'exists';
    } else {
        $stmt->close();

        $sql = "SELECT * FROM heads WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result(); 

        if ($stmt->num_rows > 0) {
            // Username already exists in heads
            echo 'exists';
        } else {
            // Username does not exist in either table
            echo 'available';
        }
    }

    $stmt->close();
}

$conn->close();
?>
