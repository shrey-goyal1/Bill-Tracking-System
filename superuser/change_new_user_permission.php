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
$u_id = $data['u_id'];
$role = $data['role'];

// Map the role to ENUM values
$role_map = [
    'superuser' => 'superuser',
    'department_head' => 'department_head_users',
    'unit_head' => 'unit_head_users',
    'accounts_head' => 'accounts_head_users',
    'client' => 'client',  
    'delete' => 'delete'
];

if (!array_key_exists($role, $role_map)) {
    echo json_encode(['success' => false, 'message' => 'Invalid role']);
    exit();
}

$mapped_role = $role_map[$role];

// Fetch the user details
$sql = "SELECT * FROM new_users WHERE u_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $u_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();


if ($user) {
    if ($mapped_role === 'client') {
        $sql = "UPDATE new_users SET role = ? WHERE u_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $mapped_role, $u_id);
    } elseif($mapped_role === 'delete'){
        $sql = "DELETE FROM new_users WHERE u_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt ->bind_param("i", $u_id);
    } else {
        $sql = "INSERT INTO heads (username, full_name, contact_number, role, password) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $user['username'], $user['full_name'], $user['contact_number'], $mapped_role, $user['password']);
        $stmt->execute();

        $sql = "DELETE FROM new_users WHERE u_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $u_id);
    }

    $response = array();

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['success'] = false;
        $response['message'] = 'Query execution failed';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'User Not found';
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>
