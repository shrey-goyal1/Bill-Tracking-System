<?php

$servername = "localhost";
$dbname = "main";
$dbusername = "root";
$dbpassword = ""; 


$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT head_id, username,full_name, contact_number, role FROM heads";
$result = $conn->query($sql);

$users = array();
if ($result) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$conn->close();

echo json_encode($users);
?>
