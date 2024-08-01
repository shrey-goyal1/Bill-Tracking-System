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

$data = json_decode(file_get_contents('php://input'), true);
$bill_id = $data['bill_id'];
$new_decision = $data['new_decision'];

$sql = "UPDATE finished_bills SET department_decision = ? WHERE bill_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_decision, $bill_id);

$response = array();
if ($stmt->execute()) {
    $response['success'] = true;
} else {
    $response['success'] = false;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
