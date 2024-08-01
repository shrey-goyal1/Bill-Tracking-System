<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Add these lines at the top of your PHP script
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';

function sendEmail($bill_id, $vendor_name, $amount, $description, $date, $status, $accounts_remark, $dept_remark, $unit_remark, $dept_decision, $unit_decision, $accounts_decision, $client_email) {
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
        $mail->addAddress($client_email, 'Client'); 

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Bill Decision Update';
        //Conditions
        if($status === 'Rejected'){
            $mail->Body = "Your Bill Is been Rejected and the Reason is been Mentioned Below . Make Changes depending upon the Remarks Provided by our Heads
                            <br>
                            1) Department Head Decision :- {$dept_decision}
                            <br>
                            Reason :- {$dept_remark}
                            <br>
                            2) Unit Head :- {$unit_decision}
                            <br> 
                            Reason :- {$unit_remark}
                            <br>
                            3) Accounts Head :- {$accounts_decision}
                            <br> 
                            Reason :- {$accounts_remark}
                            <br>
                            We are providing you the link for resubmission of the bill.
                            <br>
                            <a href='http://localhost/ipca/Main/templates/resubmission/resubmit_bill.html'><b>Click here for resubmission</b></a>
                            <br>
                            <b>Thank You For Using Our Services</b>";
        }else{
        $mail->Body    = "Your bill with ID <b>{$bill_id}</b> has received decisions from the heads. Following is the details of your Bill. 
                          <br><b>Vendor Name:</b> {$vendor_name}
                          <br><b>Amount:</b> {$amount}
                          <br><b>Description:</b> {$description}
                          <br><b>Date:</b> {$date}
                          <br><bold>Please check your status on the following link when the server is running .<bold>
                          <br><a href='http://localhost/ipca/Main/templates/status/status.html'>Click here for Checking your status </a> ";
        }
        $mail->send();
        $response['success'] = true;
        $response['message'] = 'Email has been sent to the client.';
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    return $response;
}
?>
