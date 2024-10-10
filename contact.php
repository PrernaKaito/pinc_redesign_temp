<?php

require_once __DIR__ . '/vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$receivingEmailAddress = 'marketing@pinc.co.in';
// $receivingEmailAddress = 'nikhil@kaitotech.com';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formType = filter_input(INPUT_POST, 'form_type', FILTER_SANITIZE_STRING);
    error_log('Received Form Type: ' . $formType);

    if ($formType === 'contactForm') {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $mobile = filter_input(INPUT_POST, 'mobile', FILTER_SANITIZE_STRING);
        $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);

        if (empty($name) || empty($email) || empty($mobile) || empty($city)) {
            http_response_code(400);
            echo 'Please fill in all required fields.';
            exit;
        }

        $mailer = new PHPMailer(true);
        try {
            $mailer->isSMTP();
            $mailer->Host = $_ENV['SMPT_HOST'];
            $mailer->SMTPAuth = true;
            $mailer->Username = $_ENV['SMPT_USERNAME'];
            $mailer->Password = $_ENV['SMPT_PASSWORD'];
            $mailer->SMTPSecure = $_ENV['SMPT_SECURE'];
            $mailer->Port = $_ENV['SMPT_PORT'];

            $mailer->setFrom($_ENV['SMPT_USERNAME'], $name);
            $mailer->addAddress($receivingEmailAddress);
            $mailer->isHTML(true);
            $mailer->Subject = 'Business Enquiry';
            $mailer->Body = "
                <p><strong>Name:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Mobile:</strong> $mobile</p>
                <p><strong>City:</strong> $city</p>
                <p><strong>Enquiry Date:</strong><br>" . date('d-m-Y') . "</p>
            ";
            $mailer->send();
            http_response_code(200);
            echo 'Message sent successfully!';
            $mailer->clearAddresses();  // Clear previous recipient
            $mailer->addAddress($email);  // User's email
            $mailer->Subject = 'Thank you!';
            $mailer->Body = "
                <p>Thank you, $name!</p>
                <p>Shortly, we will contact you.</p>
            ";
            $mailer->send();
            echo 'Message sent successfully!';
        } catch (Exception $e) {
            http_response_code(500);
            echo 'Unable to send message. Error: ' . $mailer->ErrorInfo;
        }
    } elseif ($formType === 'contactFormer') {
        $fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'mail', FILTER_SANITIZE_EMAIL);
        $contactNumber = filter_input(INPUT_POST, 'contactNumber', FILTER_SANITIZE_STRING);
        $company = filter_input(INPUT_POST, 'company', FILTER_SANITIZE_STRING);
        $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

        if (empty($fullname) || empty($email) || empty($contactNumber) || empty($company) || empty($message)) {
            http_response_code(400);
            echo 'Please fill in all required fields.';
            exit;
        }

        $mailer = new PHPMailer(true);
        try {

            $mailer->isSMTP();
            $mailer->Host = $_ENV['SMPT_HOST'];
            $mailer->SMTPAuth = true;
            $mailer->Username = $_ENV['SMPT_USERNAME'];
            $mailer->Password = $_ENV['SMPT_PASSWORD'];
            $mailer->SMTPSecure = $_ENV['SMPT_SECURE'];
            $mailer->Port = $_ENV['SMPT_PORT'];

            $mailer->setFrom($_ENV['SMPT_USERNAME'], $fullname);
            $mailer->addAddress($receivingEmailAddress);
            $mailer->isHTML(true);
            $mailer->Subject = 'Business Enquiry';
            $mailer->Body = "
                <p><strong>Full Name:</strong> $fullname</p>
                <p><strong>Contact Email:</strong> $email</p>
                <p><strong>Contact Number:</strong> $contactNumber</p>
                <p><strong>Company:</strong> $company</p>
                <p><strong>Message:</strong> $message</p>
            ";
            $mailer->send();
            http_response_code(200);
            echo 'Message sent successfully!';
            $mailer->clearAddresses();  // Clear previous recipient
            $mailer->addAddress($email);  // User's email
            $mailer->Subject = 'Thank you!';
            $mailer->Body = "
                <p>Thank you, $name!</p>
                <p>Shortly, we will contact you.</p>
            ";
            $mailer->send();
            echo 'Message sent successfully!';
        } catch (Exception $e) {
            http_response_code(500);
            echo 'Unable to send message. Error: ' . $mailer->ErrorInfo;
        }
    } else {
        error_log('Invalid form type received: ' . $formType);
        http_response_code(400);
        echo 'Invalid form submission.';
    }
} else {
    http_response_code(405);
    echo 'Method not allowed.';
}
?>
