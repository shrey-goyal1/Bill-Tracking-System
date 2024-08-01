<?php

$servername = "localhost";
$dbname = "main";
$dbusername = "root"; 
$dbpassword = ""; 


$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT u_id, username, full_name, contact_number, role FROM new_users";
$result = $conn->query($sql);

$newUsers = array();
if ($result) {
    while($row = $result->fetch_assoc()) {
        $newUsers[] = $row;
    }
}

$conn->close();

echo json_encode($newUsers);
?>
