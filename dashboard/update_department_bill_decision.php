<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$dbname = "main";
$dbusername = "root";
$dbpassword = "";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    $response = array('success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error);
    sendResponse($response);
}

// Get input data
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['bill_id']) || !isset($data['decision']) || !isset($data['dept_remark'])) {
    $response = array('success' => false, 'message' => 'Invalid input');
    sendResponse($response);
}

$bill_id = $data['bill_id'];
$decision = $data['decision'];
$dept_remark = $data['dept_remark'];
date_default_timezone_set('Asia/Kolkata');
$dept_date = date('Y-m-d'); 
$dept_time = date('H:i:s');

include 'get_turnaround_time.php';
$stmt = $conn->prepare("SELECT date, bill_time FROM bills WHERE bill_id = ?");
$stmt->bind_param("i",$bill_id);
$stmt->execute();
$stmt->bind_result($bill_date, $bill_time);
$stmt->fetch();
$stmt->close();

$turnaround_dept_time = calculateTimeDifference($bill_date , $bill_time , $dept_date , $dept_time);

$sql = "UPDATE bills SET department_decision = ?, dept_date = ?, dept_time = ? , dept_remark = ? , dept_ta_time = ? WHERE bill_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssi", $decision, $dept_date, $dept_time, $dept_remark , $turnaround_dept_time, $bill_id);

$response = array();
if ($stmt->execute()) {
    $response['success'] = true;

    $stmt = $conn->prepare("SELECT vendor_name, amount, description, date FROM bills WHERE bill_id = ?");
    $stmt->bind_param("i", $bill_id);
    $stmt->execute();
    $stmt->bind_result($vendor_name, $amount, $description, $date);
    $stmt->fetch();
    $stmt->close();

    include 'send_department_email.php';
    $emailResponse = sendEmail($bill_id, $vendor_name, $amount, $description, $date , $dept_remark);

    if ($emailResponse['success']) {
        $response['message'] = $emailResponse['message'];
    } else {
        $response['message'] = $emailResponse['message'];
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Error updating bill decision: ' . $stmt->error;
}

$conn->close();
sendResponse($response);

function sendResponse($response) {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
