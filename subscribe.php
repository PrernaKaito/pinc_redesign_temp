<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "kaitotech07";
$database = "Newsletter";

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

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
        $stmt = $conn->prepare("SELECT COUNT(*) FROM user WHERE emailer = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            echo "This email is already subscribed.";
        } else {
            // Prepare and bind the SQL statement
            $stmt = $conn->prepare("INSERT INTO user (emailer) VALUES (?)");
            if ($stmt) {
                $stmt->bind_param("s", $email);

                // Execute the statement and check for success
                if ($stmt->execute()) {
                    echo "Subscription successful!";

                    // Send "Thank You" email using PHPMailer
                    $mailer = new PHPMailer(true);
                    try {
                        // SMTP server settings
                        $smtpHost = 'smtp.office365.com';
                        $smtpPort = 587;
                        $smtpUsername = 'info@kaitotech.com';
                        $smtpPassword = 'Lut63954';
                        $smtpSecure = 'tls';
                        
                        // PHPMailer settings
                        $mailer->isSMTP();
                        $mailer->Host = $smtpHost;
                        $mailer->SMTPAuth = true;
                        $mailer->Username = $smtpUsername;
                        $mailer->Password = $smtpPassword;
                        $mailer->SMTPSecure = $smtpSecure;
                        $mailer->Port = $smtpPort;

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
