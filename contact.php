<?php

require_once __DIR__ . '/vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$receivingEmailAddress = 'mahesh.satpute@pincinsure.com';
// $receivingEmailAddress = 'marketing@pinc.co.in';
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
                <!DOCTYPE html>
    <html>

    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>Query notification internal</title>
        <link href='' rel='stylesheet'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css'>
    </head>

    <body style='font-family: Open Sans;'>
        <table align='center' border='0' cellpadding='0' cellspacing='0' style='width:100%;margin:0 auto;background-color:#ffffff;'>
            <tbody>
                <tr>
                    <td style='font-size:0'>&nbsp;</td>
                    <td align='center' style='width:680px;' valign='top'>
                        <table align='center' style='border:1px solid #2EBBA8; background-color: #ffffff;' cellpadding='0' cellspacing='0'>
                            <tbody>
                                <tr>
                                    <td align='center'>
                                        <a href='https://pincwealth.com/' target='_blank'>
                                            <img src='img/email-img.png' width='100%' /></a>
                                    </td>
                                </tr>

                                <tr>
                                    <td align='left'>
                                        <div style='padding:45px 35px;'>
                                            <div>Dear <b>$name</b>,</div>
                                            <p>Thank you for reaching out to PINC Insurance. We appreciate your interest in our Insurance Services.</p>
                                            <p>One of our expert advisors will be in touch with you shortly to discuss your specific needs and provide you with more information.</p>
                                            <p>If you have any immediate questions, please feel free to contact us at <a style='color:#e2066e' href='mailto:info@pincinsure.com'>info@pincinsure.com</a></p>
                                            <p>We look forward to the opportunity to assist.</p>
                                            <div>Regards,</div>
                                            <div>PINC Insurance</div>
                                        </div>
                                    </td>
                                </tr>

                                <tr style='background-color: #fcfcfc;'>
                                    <td style='padding:50px 23px 40px;text-align: center;'>
                                        <div style='margin-bottom: 10px;'>Follow us on</div>
                                        <a style='margin-right:8px ; color: #e1056d;' href='https://www.facebook.com/PINCInsurance/' target='_blank'>
                                            <i class='fab fa-facebook' style='font-size:24px;'></i>
                                        </a>
                                        <a style='margin-right:8px ; color: #e1056d;' href='https://x.com/PINCInsurance' target='_blank'>
                                            <i class='fab fa-twitter' style='font-size:24px;'></i>
                                        </a>
                                        <a style='margin-right:8px ; color: #e1056d;' href='https://www.instagram.com/pinc_insurance/?hl=en' target='_blank'>
                                            <i class='fab fa-instagram' style='font-size:24px;'></i>
                                        </a>
                                        <a style='margin-right:8px ; color: #e1056d;' href='https://www.linkedin.com/company/pincinsurance/' target='_blank'>
                                            <i class='fab fa-linkedin' style='font-size:24px;'></i>
                                        </a>
                                    </td>
                                </tr>
                                
                                <tr style='background-color: #e8e8e8;'>
                                    <td align='center' style='width:100%;font-size: 12px;padding:12px;' valign='top'>
                                        <p>Pioneer Insurance & Reinsurance Brokers Pvt. Ltd.: 1219, 12th floor, Maker Chamber 5, Jamnalal Bajaj Rd, Nariman Point, Mumbai, Maharashtra 400021</p>
                                        <p>CIN No.: U67200MH2002PTC137986 | IRDAI License No.: 118 | Validity: 24.02.2024 – 23.02.2027 | Category: Composite Brokers</p>
                                        <p>All Rights Reserved. Copyright 2024 Pioneer Insurance & Reinsurance Brokers Pvt. Ltd.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td style='font-size:0'>&nbsp;</td>
                </tr>
            </tbody>
        </table>
    </body>

    </html>
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
               <!DOCTYPE html>
    <html>

    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <title>Query notification internal</title>
        <link href='' rel='stylesheet'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css'>
    </head>

    <body style='font-family: Open Sans;'>
        <table align='center' border='0' cellpadding='0' cellspacing='0' style='width:100%;margin:0 auto;background-color:#ffffff;'>
            <tbody>
                <tr>
                    <td style='font-size:0'>&nbsp;</td>
                    <td align='center' style='width:680px;' valign='top'>
                        <table align='center' style='border:1px solid #2EBBA8; background-color: #ffffff;' cellpadding='0' cellspacing='0'>
                            <tbody>
                                <tr>
                                    <td align='center'>
                                        <a href='https://pincwealth.com/' target='_blank'>
                                            <img src='img/email-img.png' width='100%' /></a>
                                    </td>
                                </tr>

                                <tr>
                                    <td align='left'>
                                        <div style='padding:45px 35px;'>
                                            <div>Dear <b>$name</b>,</div>
                                            <p>Thank you for reaching out to PINC Insurance. We appreciate your interest in our Insurance Services.</p>
                                            <p>One of our expert advisors will be in touch with you shortly to discuss your specific needs and provide you with more information.</p>
                                            <p>If you have any immediate questions, please feel free to contact us at <a style='color:#e2066e' href='mailto:info@pincinsure.com'>info@pincinsure.com</a></p>
                                            <p>We look forward to the opportunity to assist.</p>
                                            <div>Regards,</div>
                                            <div>PINC Insurance</div>
                                        </div>
                                    </td>
                                </tr>

                                <tr style='background-color: #fcfcfc;'>
                                    <td style='padding:50px 23px 40px;text-align: center;'>
                                        <div style='margin-bottom: 10px;'>Follow us on</div>
                                        <a style='margin-right:8px ; color: #e1056d;' href='https://www.facebook.com/PINCInsurance/' target='_blank'>
                                            <i class='fab fa-facebook' style='font-size:24px;'></i>
                                        </a>
                                        <a style='margin-right:8px ; color: #e1056d;' href='https://x.com/PINCInsurance' target='_blank'>
                                            <i class='fab fa-twitter' style='font-size:24px;'></i>
                                        </a>
                                        <a style='margin-right:8px ; color: #e1056d;' href='https://www.instagram.com/pinc_insurance/?hl=en' target='_blank'>
                                            <i class='fab fa-instagram' style='font-size:24px;'></i>
                                        </a>
                                        <a style='margin-right:8px ; color: #e1056d;' href='https://www.linkedin.com/company/pincinsurance/' target='_blank'>
                                            <i class='fab fa-linkedin' style='font-size:24px;'></i>
                                        </a>
                                    </td>
                                </tr>
                                
                                <tr style='background-color: #e8e8e8;'>
                                    <td align='center' style='width:100%;font-size: 12px;padding:12px;' valign='top'>
                                        <p>Pioneer Insurance & Reinsurance Brokers Pvt. Ltd.: 1219, 12th floor, Maker Chamber 5, Jamnalal Bajaj Rd, Nariman Point, Mumbai, Maharashtra 400021</p>
                                        <p>CIN No.: U67200MH2002PTC137986 | IRDAI License No.: 118 | Validity: 24.02.2024 – 23.02.2027 | Category: Composite Brokers</p>
                                        <p>All Rights Reserved. Copyright 2024 Pioneer Insurance & Reinsurance Brokers Pvt. Ltd.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td style='font-size:0'>&nbsp;</td>
                </tr>
            </tbody>
        </table>
    </body>

    </html>
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
