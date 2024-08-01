<?php
session_start();

// Database configuration
$servername = "localhost";
$dbname = "main";
$dbusername = "root"; // Default XAMPP MySQL username
$dbpassword = ""; // Default XAMPP MySQL password

// Create connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vendor_name = $_POST['vendor_name'];
    $client_email = $_POST['client_email'];
    $bill_date = $_POST['bill_date'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $date = date('Y-m-d'); // Get the current date
    $bill_time = $_POST['bill_time']; // Get the current time

    // Calculate the number of days late
    $bill_date_time = new DateTime($bill_date);
    $current_date_time = new DateTime($date);
    $interval = $bill_date_time->diff($current_date_time);
    $days_late = max(0, $interval->days - 30); // Calculate days late, ensuring it's not negative

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO bills (vendor_name, client_email, bill_date, amount, description, date, bill_time, days_late, department_decision, unit_decision, accounts_decision) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', 'Pending', 'Pending')");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("sssisssi", $vendor_name, $client_email, $bill_date, $amount, $description, $date, $bill_time, $days_late);

    // Execute and check if successful
    if ($stmt->execute()) {
        $bill_id = $conn->insert_id;
        echo "<script>alert(`Bill submitted successfully! It is sent for approval. 
        Click on the Below link to be redirected to the home page.
        Your Bill ID is ${bill_id}.
        Please Note that for future reference`);</script>";

        // Send email notification to department head
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();                                      // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';               // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                             // Enable SMTP authentication
            $mail->Username   = 'sender@gmail.com';         // SMTP username
            $mail->Password   = 'xxxx xxxx xxxx xxxx';                  // SMTP password Substitue your password which can be generated using accounts password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Enable TLS encryption; PHPMailer::ENCRYPTION_SMTPS also accepted
            $mail->Port       = 587;                              // TCP port to connect to

            // Recipients
            $mail->setFrom('sender@gmail.com', 'Admin');
            $mail->addAddress('departmenthead@gmail.com', 'Department Head');     // Add a recipient

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'New Bill Submission';
            $mail->Body    = "A new bill with ID <b>{$bill_id}</b> has been submitted on <b>{$date} </b> at <b>{$bill_time} </b>. Please review and provide your decision.";

            $mail->send();
            echo 'Email has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    if ($days_late > 30) {
        // Send Email notification to the client about late submission of the bill
        $mail = new PHPMailer(true);
        try {
            // Server Settings
            $mail->isSMTP(); // Sending using SMTP
            $mail->Host = 'smtp.gmail.com'; // Setting the SMTP server to send
            $mail->SMTPAuth = true; // Enable SMTP Authentication
            $mail->Username = 'sender@gmail.com'; // username
            $mail->Password = 'xxxx xxxx xxxx xxxx'; // Mail Pass Substitue your password which can be generated using accounts password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption; PHPMailer::ENCRYPTION_SMTPS also accepted
            $mail->Port = 587; // TCP port

            // Recipients
            $mail->setFrom('sender@gmail.com', 'Admin');
            $mail->addAddress($client_email, 'User');

            // Content
            $mail->isHTML(true); // Setting html format
            $mail->Subject = 'Late Bill Submission';
            $mail->Body = "We have received your bill with bill ID <b>{$bill_id}</b>, but we regret to tell you that your bill is late submitted and it is <b>{$days_late} days</b> late. We will apply standard rates and penalties on it.<br> <b>Thank You.</b>";

            $mail->send();
            echo '<br>Email has been sent to user too';
        } catch (Exception $e) {
            echo "Message could not be sent Mailer Error: {$mail->ErrorInfo}";
        }
    }

    $stmt->close();

    // Redirect to login page after successful submission
    echo("<a href='../login/index.html'>Click here</a>");
    exit();
}

$conn->close();
?>
