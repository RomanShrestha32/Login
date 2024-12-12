<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

require_once "database.php";

$recaptcha_secret_key = "6LetEz8pAAAAANIhGJx_iu5AKpKoxB83bKK2YLFA"; 
$recaptcha_site_key = "6LetEz8pAAAAACa27IYQgoF1KPysyQ1UTdlIxzCQ"; 

if (isset($_POST["submit"])) {

    // Get reCAPTCHA response
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Verify reCAPTCHA with Google's API
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_verify = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret_key . '&response=' . $recaptcha_response);
    $recaptcha_verify = json_decode($recaptcha_verify);
    
    // Check if the reCAPTCHA was successful
    if ($recaptcha_verify->success) {
        $fullName = filter_var($_POST["fullname"], FILTER_SANITIZE_STRING); // Corrected typo
        $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
        $password = htmlspecialchars($_POST["password"]);
        $passwordConfirm = htmlspecialchars($_POST["confirm_password"]);

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $errors = array();

        if (empty($fullName) || empty($email) || empty($password) || empty($passwordConfirm)) {
            $errors[] = "All fields are required";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email is not valid";
        }
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long";
        }
        if ($password !== $passwordConfirm) {
            $errors[] = "Password does not match";
        }

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors[] = "Email already exists!";
        }

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger text-center'>$error</div>";
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sss", $fullName, $email, $passwordHash);
                $stmt->execute();
                echo "<div class='alert alert-success text-center'>You are registered successfully.</div>";
            } else {
                die("Something went wrong");
            }
        }
    } else {
        // Add an error for failed CAPTCHA verification
        $errors[] = "Captcha verification failed. Please try again.";
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger text-center'>$error</div>";
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
    <title>Registration Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Mooli&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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

        .form-group {
            margin-bottom: 1rem;
        }
        .form-btn {
            margin-top: 1rem;
        }
        .alert {
            margin-top: 1rem;
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
        .valid-border {
            border-color: green !important;
        }
        .invalid-border {
            border-color: red !important;
        }
        #confirmMessage {
            margin-top: 0.5rem;
            font-family: 'Mooli', cursive;
            font-weight: bold;
        }
        #passwordStrength {
            margin-top: 0.5rem;
            font-family: 'Mooli', cursive;
            font-weight: bold;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById("password");
            const confirmPasswordInput = document.getElementById("confirm_password");
            const matchMessage = document.getElementById("confirmMessage");
            const passwordStrength = document.getElementById("passwordStrength");

            function checkPasswordStrength() {
                const password = passwordInput.value;
                const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
                if (regex.test(password)) {
                    passwordStrength.textContent = "Strong password";
                    passwordStrength.style.color = "green";
                } else {
                    passwordStrength.textContent = "Password must be at least 8 characters, include an uppercase letter, a number, and a special character.";
                    passwordStrength.style.color = "red";
                }
            }

            function checkPasswordMatch() {
                if (passwordInput.value !== confirmPasswordInput.value) {
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

            passwordInput.addEventListener('input', checkPasswordStrength);
            confirmPasswordInput.addEventListener('input', function () {
                checkPasswordMatch();
            });
        });

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
        <h1 class="text-center">Register</h1>
        <form action="registration.php" method="post">
            <div class="form-group">
                <label for="fullname">Full Name:</label>
                <input type="text" class="form-control" name="fullname" placeholder="Enter Full Name:" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" name="email" placeholder="Enter Email:" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <div class="input-group">
                    <input type="password" id="password" class="form-control" name="password" placeholder="Enter Password:" required>
                    <span id="password-toggle" onclick="togglePassword('password', 'password-toggle')">Show</span>
                </div>
                <div id="passwordStrength"></div>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <div class="input-group">
                    <input type="password" id="confirm_password" class="form-control" name="confirm_password" placeholder="Enter Confirm Password:" required>
                    <span id="confirm-password-toggle" onclick="togglePassword('confirm_password', 'confirm-password-toggle')">Show</span>
                </div>
                <div id="confirmMessage"></div>
            </div>
            <div class="g-recaptcha" style="width:400px;" data-sitekey="<?php echo $recaptcha_site_key; ?>"></div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
        </form>
        <div class="mt-3 text-center">
            <p>Already Have an Account? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>
