<?php
session_start();
require_once "database.php";
require_once "mailer.php";

if (isset($_POST['submit'])) {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format";
    } else {
        // Check if the email exists in the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            // Generate OTP and update it in the database
            $otp = rand(100000, 999999);
            $stmt = $conn->prepare("UPDATE users SET otp = ? WHERE email = ?");
            $stmt->bind_param("is", $otp, $email);
            $stmt->execute();

            // Send OTP email
            if (sendOtpEmail($email, $otp)) {
                $_SESSION["email"] = $email; // Store email in session
                header("Location: reset-password.php"); // Redirect to reset password page
                exit();
            } else {
                $error_message = "Failed to send OTP email.";
            }
        } else {
            $error_message = "Email does not exist.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Mooli&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .container {
            background-image: url('Background.jpg'); 
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.1); 
            color: #333; 
        }
        h3 {
            font-family: 'Mooli', sans-serif; 
            font-size: 16px; 
            color: #FF8C00; 
            text-align: center; 
            margin-top: 20px; 
            margin-bottom: 10px; 
        }
        #loading-message {
            display: none; 
            text-align: center;
            font-family: 'Mooli', sans-serif; 
            margin-top: 20px;
            color: #007bff; 
        }
    </style>
    <script>
        function showLoadingMessage() {
            document.getElementById("loading-message").style.display = "block";
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Forgot Password</h1>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger text-center">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        <form action="forgot-password.php" method="post" onsubmit="showLoadingMessage();">
            <div class="form-group">
                <label for="email">Email address:</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Enter Your Email Address" required>
            </div>
            <div>
                <h3>We will send OTP to this email after submission</h3>
            </div>
            <div id="loading-message">Sending OTP, Please wait...</div>
            <div class="form-btn mt-3">
                <input type="submit" value="Submit" name="submit" class="btn btn-primary">
            </div>
        </form>
    </div>
</body>
</html>
