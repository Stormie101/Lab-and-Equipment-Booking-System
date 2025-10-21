<?php
// auth.php
include 'config.php';
include 'mfa_config.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, username, password, role, email FROM users WHERE username = ? AND status = 'active'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if ($password === $user['password']) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];

            // Generate OTP
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['otp_expiry'] = time() + 300; // 5 minutes

            // Send OTP via email
            if (sendMFA($user['email'], $otp)) {
                header("Location: verify.php");
                exit();
            } else {
                echo "<h3>❌ Failed to send MFA code. Try again later.</h3>";
                exit();
            }
        } else {
            header("Location: login.php?error=Invalid password");
            exit();
        }
    } else {
        header("Location: login.php?error=User not found or inactive");
        exit();
    }
}

?>
