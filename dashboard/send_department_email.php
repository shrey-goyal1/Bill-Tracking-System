<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendEmail($bill_id, $vendor_name, $amount, $description, $date) {
    $mail = new PHPMailer(true);
    $response = array();
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sender@gmail.com';
        $mail->Password   = 'xxxx xxxx xxxx xxxx';      // Substitue your password which can be generated using accounts password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('sender@gmail.com', 'Admin');
        $mail->addAddress('unithead@gmail.com', 'Unit Head');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Bill Decision Update';
        $mail->Body    = "A bill with ID <b>{$bill_id}</b> has received a decision from the department head. 
                          <br><b>Vendor Name:</b> {$vendor_name}
                          <br><b>Amount:</b> {$amount}
                          <br><b>Description:</b> {$description}
                          <br><b>Date:</b> {$date}
                          <br><bold>Please review and provide your decision.<bold>";

        $mail->send();
        $response['success'] = true;
        $response['message'] = 'Email has been sent to the unit head.';
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    return $response;
}
?>
