<?php
// Include PHPMailer classes

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();


$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$database = $_ENV['DB_DATABASE'];
$port = $_ENV['DB_PORT'];
// Create connection
$conn = new mysqli($servername, $username, $password, $database,$port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the POST request has the 'emailer' field
if (isset($_POST['emailer']) && !empty($_POST['emailer'])) {
    $email = $_POST['emailer'];

    // Sanitize and validate the email input
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        
        // Check if the email already exists in the database
        $stmt = $conn->prepare("SELECT COUNT(*) FROM user_subscribe_newsletter WHERE emailer = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            echo "This email is already subscribed.";
        } else {
            // Prepare and bind the SQL statement
            $stmt = $conn->prepare("INSERT INTO user_subscribe_newsletter (emailer) VALUES (?)");
            if ($stmt) {
                $stmt->bind_param("s", $email);

                // Execute the statement and check for success
                if ($stmt->execute()) {
                    echo "Subscription successful!";

                    // Send "Thank You" email using PHPMailer
                    $mailer = new PHPMailer(true);
                    try {
                        
                        // PHPMailer settings
                        $mailer->isSMTP();
                        $mailer->Host = $_ENV['SMPT_HOST'];
                        $mailer->SMTPAuth = true;
                        $mailer->Username = $_ENV['SMPT_USERNAME'];
                        $mailer->Password = $_ENV['SMPT_PASSWORD'];
                        $mailer->SMTPSecure = $_ENV['SMPT_SECURE'];
                        $mailer->Port = $_ENV['SMPT_PORT'];

                        // Recipients
                        $mailer->setFrom('info@kaitotech.com', 'KaitoTech'); // Replace with your "From" email address
                        $mailer->addAddress($email);  // Add the recipient's email address

                        // Content
                        $mailer->isHTML(true);
                        $mailer->Subject = 'Thank You for Your Subscription';
                        $mailer->Body = "
                            <p>Dear Subscriber,</p>
                            <p>Thank you for subscribing to our newsletter. We will keep you updated with the latest news.</p>
                            <p>Best regards,<br>KaitoTech</p>
                        ";

                        // Send the email
                        $mailer->send();
                        echo ' A thank-you email has been sent to ' . $email;
                    } catch (Exception $e) {
                        echo "Message could not be sent. Mailer Error: {$mailer->ErrorInfo}";
                    }
                } else {
                    echo "Error executing query: " . $stmt->error;
                }

                // Close the statement
                $stmt->close();
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
        }
    } else {
        echo "Invalid email format.";
    }
} else {
    echo "Error: Email field is missing or empty.";
}

// Close the connection
$conn->close();
?>
