<?php

$servername = "localhost";
$dbname = "main";
$dbusername = "root"; 
$dbpassword = ""; 


$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT bill_id, vendor_name, date, department_decision, unit_decision, accounts_decision FROM finished_bills";
$result = $conn->query($sql);

$bills = array();
if ($result) {
    while($row = $result->fetch_assoc()) {
        $bills[] = $row;
    }
}

$conn->close();

echo json_encode($bills);
?>
