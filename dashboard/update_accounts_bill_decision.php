<?php
session_start();

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
if (!isset($data['bill_id']) || !isset($data['decision']) || !isset($data['accounts_remark'])) {
    $response = array('success' => false, 'message' => 'Invalid input');
    sendResponse($response);
}

$bill_id = $data['bill_id'];
$decision = $data['decision'];
$accounts_remark = $data['accounts_remark'];
$unit_remark = $data['unit_remark'];
$dept_remark = $data['dept_remark'];
$dept_decision = $data['dept_decision'];
$unit_decision = $data['unit_decision'];
$accounts_decision = $data['accounts_decision'];
date_default_timezone_set('Asia/Kolkata');
$accounts_date = date('Y-m-d');
$accounts_time = date('H:i:s');

include 'get_turnaround_time.php';
$stmt = $conn->prepare("SELECT date, bill_time , department_decision , unit_decision FROM bills WHERE bill_id = ?");
$stmt->bind_param("i", $bill_id);
$stmt->execute();
$stmt->bind_result($bill_date, $bill_time , $department_decision , $unit_decision); 
$stmt->fetch();
$stmt->close();

$turnaround_time = calculateTimeDifference($bill_date, $bill_time, $accounts_date, $accounts_time);

if ($department_decision === 'Rejected' || $unit_decision ==='Rejected' || $decision === 'Rejected'){
    $status = 'Rejected';
}elseif ($department_decision === 'Approved'&& $unit_decision === 'Approved' && $decision === 'Approved'){
    $status = 'Approved';
}else{
    $status = 'Pending';
}

// Update the accounts decision in the database
$sql = "UPDATE bills SET accounts_decision = ?, accounts_date = ?, accounts_time = ?, accounts_remark = ?, turnaround_time = ? WHERE bill_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssi", $decision, $accounts_date, $accounts_time, $accounts_remark, $turnaround_time, $bill_id);

$response = array();
if ($stmt->execute()) {
    $response['success'] = true;

    $stmt = $conn->prepare("SELECT vendor_name, amount, description, date, client_email FROM bills WHERE bill_id = ?");
    $stmt->bind_param("i", $bill_id);
    $stmt->execute();
    $stmt->bind_result($vendor_name, $amount, $description, $date, $client_email);
    $stmt->fetch();
    $stmt->close();

    // Included the email sending function
    include 'send_accounts_email.php';
    $emailResponse = sendEmail($bill_id, $vendor_name, $amount, $description, $date, $status, $accounts_remark, $unit_remark, $dept_remark, $dept_decision, $unit_decision, $decision, $client_email);

    if ($emailResponse['success']) {
        $response['message'] = $emailResponse['message'];
    } else {
        $response['message'] = $emailResponse['message'];
    }

    if ($status === 'Rejected') {
        include 'move_bill_to_rejected_table.php';
        if (moveBillToRejected($conn, $bill_id)) {
            $response['success'] = true;
        } else {
            $response['success'] = false;
            $response['message'] = 'Error moving to rejected bill table.';
            sendResponse($response);
        }
    } else {
        // if not rejected then ofc approved
        include 'move_bill_to_finished.php';
        if (moveBillToFinished($conn, $bill_id)) {
            $response['success'] = true;
        } else {
            $response['success'] = false;
            $response['message'] = 'Error moving bill to finished bills table.';
            sendResponse($response);
        }
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
