<?php
$servername = "localhost";
$dbname = "main";
$dbusername = "root"; 
$dbpassword = ""; 

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get input data
$data = json_decode(file_get_contents('php://input'), true);
$bill_id = $data['bill_id'];
$bill_date = $data['bill_date'];

// Validate input
if (empty($bill_id) || empty($bill_date)) {
    echo json_encode(['error' => 'Invalid input.']);
    $conn->close();
    exit();
}


$sql = "SELECT department_decision, unit_decision, accounts_decision FROM finished_bills WHERE bill_id = ? AND bill_date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $bill_id, $bill_date);
$stmt->execute();
$result = $stmt->get_result();
$bill = $result->fetch_assoc();

if ($bill) {
    
    $department_decision = $bill['department_decision'];
    $unit_decision = $bill['unit_decision'];
    $accounts_decision = $bill['accounts_decision'];

    if ($department_decision === 'Rejected' || $unit_decision === 'Rejected' || $accounts_decision === 'Rejected') {
        $status = 'The bill is rejected.';
    } elseif ($department_decision === 'Pending' || $unit_decision === 'Pending' || $accounts_decision === 'Pending') {
        $status = 'The bill is pending.';
    } else {
        $status = 'The bill is approved.';
    }
    
    echo json_encode(['status' => $status]);
} else {
    echo json_encode(['error' => 'Bill not found.']);
}

$stmt->close();
$conn->close();
?>