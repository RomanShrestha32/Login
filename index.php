<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
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

        .mooli-font {
            font-family: 'Mooli', sans-serif;
        }
        .custom-container {
            max-width: 800px; 
            margin: auto; /
        }
    </style>

    <script>
        // Function to hide the alert after 3 seconds
        function hideAlert() {
            const alert = document.getElementById("success-alert");
            if (alert) {
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 3000); // 3000 milliseconds = 3 seconds
            }
        }
    </script>
</head>
<body onload="hideAlert()">

    <div class="container mt-5 custom-container">
        <?php if (isset($_SESSION["success_message"])): ?>
            <div class="alert alert-success text-center" id="success-alert">
                <?php 
                    echo htmlspecialchars($_SESSION["success_message"]); 
                    unset($_SESSION["success_message"]); // Clear message after displaying it
                ?>
            </div>
        <?php endif; ?>
        <h1 class="text-center my-4">Welcome to the Dashboard!</h1>

        <div class="text-center mt-6"> <!-- Increased margin-top here -->
            <a href="logout.php" class="btn btn-warning btn-lg mooli-font">Logout</a>
        </div>
    </div>
</body>
</html>
