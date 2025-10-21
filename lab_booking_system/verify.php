<?php
session_start();

$roleMap = [
    'student' => 'student/student_dashboard.php',
    'lecture' => 'lecturer/lecture_dashboard.php',
    'labto'   => 'admin/labto_dashboard.php',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredOtp = trim($_POST['otp']);
    $storedOtp = $_SESSION['otp'] ?? null;
    $expiry = $_SESSION['otp_expiry'] ?? 0;

    if (!$storedOtp || time() > $expiry) {
        $error = "OTP expired. Please login again.";
        session_destroy();
    } elseif ($enteredOtp === strval($storedOtp)) {
        $role = $_SESSION['role'] ?? '';
        if (isset($roleMap[$role]) && file_exists($roleMap[$role])) {
            unset($_SESSION['otp'], $_SESSION['otp_expiry']);
            header("Location: " . $roleMap[$role]);
            exit();
        } else {
            $error = "Dashboard not found for role: '$role'";
        }
    } else {
        $error = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify MFA Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .verify-box {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .verify-box h2 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="verify-box text-center">
        <h2>Authentication</h2>
        <p class="text-muted">Enter the 6-digit code sent to your email</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <input type="text" name="otp" maxlength="6" class="form-control text-center" placeholder="Enter OTP" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Verify</button>
        </form>
    </div>
</body>
</html>
