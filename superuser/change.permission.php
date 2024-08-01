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
$head_id = $data['head_id'];
$roles = $data['roles'];

$sql = "UPDATE heads SET roles = ? WHERE head_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $role, $head_id);

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
