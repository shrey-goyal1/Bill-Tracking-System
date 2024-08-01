<?php
session_start();

// Database configuration
$servername = "localhost";
$dbname = "main";
$dbusername = "root"; 
$dbpassword = ""; 


$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT password, role FROM heads WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            switch ($role) {
                case 'department_head_users':
                    header("Location: ../dashboard/department_head_dashboard.html");
                    break;
                case 'unit_head_users':
                    header("Location: ../dashboard/unit_head_dashboard.html");
                    break;
                case 'accounts_head_users':
                    header("Location: ../dashboard/accounts_head_dashboard.html");
                    break;
                case 'superuser':
                    header("Location: ../superuser/superuser_dashboard.html");
                    break;
                case 'client':
                    header("Location: ../dashboard/client_dashboard.html");
                    break;
                default:
                    echo 'Login successful! Welcome, ' . htmlspecialchars($username) . '.';
                    break;
            }
        } else {
            echo 'Invalid username or password';
        }
    } else {
        echo 'Your Role hasnt been assigned. Please contact the authorities regarding that purpose or else wait. <a href="../login/index.html">Login Page</a>';
    }

    $stmt->close();
}

$conn->close();
?>
