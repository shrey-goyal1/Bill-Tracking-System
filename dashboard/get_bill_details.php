<?php

$servername = "localhost";
$dbname = "main";
$dbusername = "root"; 
$dbpassword = ""; 


$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$bill_id = $_GET['bill_id'];

$sql = "SELECT * FROM bills WHERE bill_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bill_id);
$stmt->execute();
$result = $stmt->get_result();
$bill = $result->fetch_assoc();

if ($bill) {
    echo json_encode($bill);
} else {
    echo json_encode(['error' => 'Bill not found']);
}

$stmt->close();
$conn->close();
?>
