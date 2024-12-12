<?php
session_start();
require_once "database.php"; // Database connection
require_once "mailer.php";   // Mailer for sending OTP

$recaptcha_secret_key = "6LetEz8pAAAAANIhGJx_iu5AKpKoxB83bKK2YLFA"; 

// Redirect to index if user is already logged in
if (isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

// Handle login form submission
if (isset($_POST["login"])) {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = htmlspecialchars($_POST["password"]);
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Verify reCAPTCHA with Google's API
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_verify = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret_key . '&response=' . $recaptcha_response);
    $recaptcha_verify = json_decode($recaptcha_verify);

    if (!$recaptcha_verify->success) {
        $error_message = "Please complete the reCAPTCHA verification.";
    } else {
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Invalid email format";
        } elseif (!isValidPassword($password)) {
            $error_message = "Incorrect Password.";
        } else {
            // Prepare SQL query to prevent SQL injection
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user) {
                if (password_verify($password, $user["password"])) {
                    // Generate 6 digit OTP and update it in the database
                    $otp = rand(100000, 999999);
                    $stmt = $conn->prepare("UPDATE users SET otp = ? WHERE email = ?");
                    $stmt->bind_param("is", $otp, $email);
                    $stmt->execute();
                    
                    // Send OTP to the user's email
                    if (sendOtpEmail($email, $otp)) {
                        $_SESSION["email"] = $email; // Store email in session
                        header("Location: otp.php"); // Redirect to OTP page
                        exit();
                    } else {
                        $error_message = "Failed to send OTP email";
                    }
                } else {
                    $error_message = "Password does not match";
                }
            } else {
                $error_message = "Email does not exist";
            }
        }
    }
}

// Function to validate password complexity
function isValidPassword($password) {
    return preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
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

        .input-group {
            position: relative;
        }
        .input-group input {
            padding-right: 70px; 
        }
        .input-group span {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            z-index: 1;
            background-color: #fff; 
            padding: 0 10px;
        }
        

        #loading-message {
            display: none; 
            text-align: center;
            margin-top: 20px;
            font-family: 'Mooli', cursive;

            color: #007bff; 
        }  
    </style>
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <script>
        // Function to toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const passwordToggle = document.getElementById("password-toggle");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordToggle.textContent = "Hide";
            } else {
                passwordInput.type = "password";
                passwordToggle.textContent = "Show";
            }
        }

        // Function to show loading message
        function showLoadingMessage() {
            document.getElementById("loading-message").style.display = "block";
        }
    </script>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Login</h1>
        
        <!-- Show logout success message -->
        <?php if (isset($_SESSION["success_message"])): ?>
            <div class="alert alert-success text-center" id="success-alert">
                <?php 
                    echo htmlspecialchars($_SESSION["success_message"]); 
                    unset($_SESSION["success_message"]); // Clear message after displaying it
                ?>
            </div>
            <script>
                // Hide the success message after 2 seconds
                setTimeout(() => {
                    document.getElementById("success-alert").style.display = "none";
                }, 2000);
            </script>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger text-center">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        <form action="login.php" method="post" onsubmit="showLoadingMessage();">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Enter Email:" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <div class="input-group">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter Password:" required>
                    <span id="password-toggle" onclick="togglePassword()">Show</span>
                </div>
            </div>
            <p><a href="forgot-password.php">Forgot Password?</a></p>

            <div class="g-recaptcha" style="width:400px;" data-sitekey="6LetEz8pAAAAACa27IYQgoF1KPysyQ1UTdlIxzCQ"></div>

            <div id="loading-message">Sending OTP to this Email, Please wait...</div> <!-- Loading message -->
            <div class="form-btn mt-3">
                <input type="submit" value="Login" name="login" class="btn btn-primary">
            </div>
        </form>
        
        <div class="account-link mt-3">
            <p>Don't have an account? <a href="registration.php">Register Here</a></p>
        </div>
    </div>
</body>
</html>
