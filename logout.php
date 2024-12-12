<?php
session_start();
session_unset();
session_destroy();
session_start();
$_SESSION["success_message"] = "Logout successful!";
header("Location: login.php");
exit();
?>
