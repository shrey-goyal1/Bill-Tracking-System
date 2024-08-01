<?php
$servername = "localhost";
$dbname = "main";
$dbusername = "root";
$dbpassword = ""; 


$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Checking connection is happening or not
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT bill_id, vendor_name, date FROM bills";
$result = $conn->query($sql);

$bills = array();
if ($result) {
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $bills[] = $row;
        }
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}

$conn->close();

echo json_encode($bills);
?>
