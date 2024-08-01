<?php
session_start();


$servername = "localhost";
$dbname = "main";
$dbusername = "root"; 
$dbpassword = ""; 

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $contact_number = $_POST['contact_number'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

  
    if ($password !== $confirm_password) {
        die("Passwords do not match");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO new_users (username, full_name, contact_number, password) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ssss", $username, $full_name, $contact_number, $hashed_password);

    // Execute and check if successful
    if ($stmt->execute()) {
        echo "Registration successful! Please wait for your account to be approved.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
