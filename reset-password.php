<?php
session_start();
require_once "database.php";

if (!isset($_SESSION["email"])) {
    header("Location: forgot-password.php");
    exit();
}

if (isset($_POST["reset_password"])) {
    $otp = $_POST["otp"];
    $new_password = htmlspecialchars($_POST["new_password"]);
    $confirm_password = htmlspecialchars($_POST["confirm_password"]);

    if (!isValidPassword($new_password)) {
        $error_message = "Password must be at least 8 characters long, include an uppercase letter, a number, and a special character.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Verify OTP and reset password
        $stmt = $conn->prepare("SELECT otp FROM users WHERE email = ?");
        $stmt->bind_param("s", $_SESSION["email"]);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && $user["otp"] == $otp) {
            // Hash the new password and update it in the database
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ?, otp = NULL WHERE email = ?");
            $stmt->bind_param("ss", $hashed_password, $_SESSION["email"]);
            $stmt->execute();

            $_SESSION["success_message"] = "Password has been reset successfully.";
            header("Location: login.php");
            exit();
        } else {
            $error_message = "Invalid OTP.";
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
    <title>Reset Password</title>
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
        #password-strength, #matchMessage {
            margin-top: 0.5rem;
            font-weight: bold;
            font-family: 'Mooli', cursive; 

        }
        .valid-border {
            border-color: green !important;
        }
        .invalid-border {
            border-color: red !important;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const newPasswordInput = document.getElementById("new_password");
            const confirmPasswordInput = document.getElementById("confirm_password");
            const passwordStrength = document.getElementById("password-strength");
            const matchMessage = document.getElementById("matchMessage");

            // Function to check password strength
            function checkPasswordStrength() {
                const password = newPasswordInput.value;
                const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
                if (regex.test(password)) {
                    passwordStrength.textContent = "Strong password";
                    passwordStrength.style.color = "green";
                } else {
                    passwordStrength.textContent = "Password must be at least 8 characters, include an uppercase letter, a number, and a special character.";
                    passwordStrength.style.color = "red";
                }
            }

            // Function to check if passwords match (only for confirm password input)
            function checkPasswordMatch() {
                if (newPasswordInput.value !== confirmPasswordInput.value) {
                    confirmPasswordInput.classList.add("invalid-border");
                    confirmPasswordInput.classList.remove("valid-border");
                    matchMessage.textContent = "Passwords do not match!";
                    matchMessage.style.color = "red";
                } else {
                    confirmPasswordInput.classList.add("valid-border");
                    confirmPasswordInput.classList.remove("invalid-border");
                    matchMessage.textContent = "Passwords match!";
                    matchMessage.style.color = "green";
                }
            }

            // Listen for input changes only in Confirm Password
            confirmPasswordInput.addEventListener('input', checkPasswordMatch);

            // Listen for input changes in New Password to update password strength
            newPasswordInput.addEventListener('input', function () {
                checkPasswordStrength();
            });
        });

        // Function to toggle password visibility
        function togglePassword(id, toggleId) {
            const passwordInput = document.getElementById(id);
            const passwordToggle = document.getElementById(toggleId);
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordToggle.textContent = "Hide";
            } else {
                passwordInput.type = "password";
                passwordToggle.textContent = "Show";
            }
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Reset Password</h1>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger text-center">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        <form action="reset-password.php" method="post">
            <div class="form-group">
                <label for="otp">OTP:</label>
                <input type="text" id="otp" name="otp" class="form-control" placeholder="Enter OTP" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <div class="input-group">
                    <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Enter New Password" required>
                    <span id="new-password-toggle" onclick="togglePassword('new_password', 'new-password-toggle')">Show</span>
                </div>
                <div id="password-strength"></div> <!-- Password strength message -->
            </div>
            <div class="form-group">
                <label for="confirm_password">New Password:</label>
                <div class="input-group">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Enter Confirm Password" required>
                    <span id="confirm-password-toggle" onclick="togglePassword('confirm_password', 'confirm-password-toggle')">Show</span>
                </div>
                <div id="matchMessage"></div> <!-- Password match message -->
            </div>
            <div class="form-btn mt-3">
                <input type="submit" value="Reset Password" name="reset_password" class="btn btn-primary">
            </div>
        </form>
    </div>
</body>
</html>
