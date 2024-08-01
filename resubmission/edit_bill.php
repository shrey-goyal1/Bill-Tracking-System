<?php
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
$vendor_name = $data['vendor_name'];
$amount = $data['amount'];
$description = $data['description'];
$date = $data['date'];

$sql = "UPDATE rejected_bills SET vendor_name = ?, amount = ?, description = ?, date = ? WHERE bill_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $vendor_name, $amount, $description, $date, $bill_id);

$response = array();

// Execute the update query
if ($stmt->execute()) {
    // If update is successful, move the bill to the bills table
    include 'move_bill_to_bills.php';
    if (moveBillToBills($conn, $bill_id)) {
        $response['success'] = true;
    } else {
        $response['success'] = false;
        $response['message'] = 'Error moving to bills table';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Error updating rejected bill';
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
