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
$bill_date = $_GET['bill_date'];

$stmt = $conn->prepare("SELECT vendor_name, amount, description, date FROM rejected_bills WHERE bill_id = ? AND date = ?");
$stmt->bind_param("is", $bill_id, $bill_date);
$stmt->execute();
$stmt->bind_result($vendor_name, $amount, $description, $date);

$response = array();

if ($stmt->fetch()) {
    $response['success'] = true;
    $response['bill'] = array(
        'vendor_name' => $vendor_name,
        'amount' => $amount,
        'description' => $description,
        'date' => $date
    );
} else {
    $response['success'] = false;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
