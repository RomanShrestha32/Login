<?php
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

require_once "database.php";

if (isset($_POST["verify"])) {
    $email = $_SESSION["email"];
    $otp = $_POST["otp1"] . $_POST["otp2"] . $_POST["otp3"] . $_POST["otp4"] . $_POST["otp5"] . $_POST["otp6"];

    $stmt = $conn->prepare("SELECT otp FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && $user["otp"] == $otp) {
        $_SESSION["user"] = "yes";
        $stmt = $conn->prepare("UPDATE users SET otp = NULL WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $_SESSION["success_message"] = "Login successful!";
        header("Location: index.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Invalid OTP</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Verification</title>
    <link href="https://fonts.googleapis.com/css2?family=Mooli&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Your existing stylesheet -->
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
        h1 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .otp-input {
            width: 2rem;
            height: 2.5rem;
            font-size: 0.8rem; 
            font-family: 'Mooli', cursive;
            text-align: center;
            margin: 0.3rem;
            border-radius: 0.5rem;
            border: 2px solid black; 
            box-sizing: border-box;
        }

        .form-btn input[type="submit"] {
            background-color: #007bff;
            border: none;
            color: #fff;
            border-radius: 0.5rem;
            padding: 0.75rem;
            font-size: 1rem;
            width: 100%;
        }
        .form-btn input[type="submit"]:hover {
            background-color: #0056b3;
            cursor: pointer;
        }
        .alert {
            border-radius: 0.5rem;
            padding: 1rem;
            margin-top: 1rem;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const inputs = document.querySelectorAll(".otp-input");
            
            inputs.forEach((input, index) => {
                input.addEventListener("input", function() {
                    if (this.value.length === 1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                });

                input.addEventListener("keydown", function(e) {
                    if (e.key === "Backspace" && this.value.length === 0 && index > 0) {
                        inputs[index - 1].focus();
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>2FA Verification</h1>
        <form action="otp.php" method="post">
            <div class="form-group d-flex justify-content-center">
                <input type="text" maxlength="1" name="otp1" class="otp-input form-control" required>
                <input type="text" maxlength="1" name="otp2" class="otp-input form-control" required>
                <input type="text" maxlength="1" name="otp3" class="otp-input form-control" required>
                <input type="text" maxlength="1" name="otp4" class="otp-input form-control" required>
                <input type="text" maxlength="1" name="otp5" class="otp-input form-control" required>
                <input type="text" maxlength="1" name="otp6" class="otp-input form-control" required>
            </div>
            <div class="form-btn mt-3">
                <input type="submit" value="Verify" name="verify" class="btn btn-primary">
            </div>
        </form>
    </div>
</body>
</html>
