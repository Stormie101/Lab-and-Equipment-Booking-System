<?php
include '../config.php';
session_start();

// Ensure only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'labto') {
    header("Location: ../login.php");
    exit();
}

include 'header.php';

// Handle update request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = trim($_POST['username']);
    $newEmail = trim($_POST['email']);
    $newPhoneNumber = trim($_POST['phone_number']);
    $newAdminName = trim($_POST['admin_name']);


    // Check for duplicate username (excluding current user)
    $check = $conn->prepare("SELECT * FROM users WHERE username = ? AND user_id != ?");
    $check->bind_param("ss", $newUsername, $_SESSION['user_id']);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $error = "Username already taken.";
    } else {
        $update = $conn->prepare("UPDATE users SET username = ?, email = ?, phone_number = ?, admin_name = ? WHERE user_id = ?");
        $update->bind_param("sssss", $newUsername, $newEmail, $newPhoneNumber, $newAdminName, $_SESSION['user_id']);
        if ($update->execute()) {
            $_SESSION['username'] = $newUsername;
            $_SESSION['admin_name'] = $newAdminName;
            $_SESSION['email'] = $newEmail;
            $_SESSION['phone_number'] = $newPhoneNumber;
            $success = "Profile updated successfully.";
        } else {
            $error = "Failed to update profile.";
        }
    }
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Profile</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f6f8;
            color: #333;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 220px;
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            flex-shrink: 0;
        }

        .sidebar h2 {
            font-size: 20px;
            margin-bottom: 30px;
            color: #ecf0f1;
        }

        .sidebar a {
            display: block;
            color: #ecf0f1;
            text-decoration: none;
            margin-bottom: 15px;
            font-weight: 500;
            padding: 8px 12px;
            border-radius: 6px;
        }

        .sidebar a:hover {
            background-color: #34495e;
        }

        .main-content {
            flex-grow: 1;
            padding: 40px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .top-right {
            text-align: right;
            margin-bottom: 10px;
        }

        .top-right a {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #ecf0f1;
            color: #2c3e50;
        }

        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            display: inline-block;
        }

        .status.approved {
            background-color: #2ecc71;
            color: white;
        }

        @media (max-width: 768px) {
            .wrapper {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 10px;
            }

            .main-content {
                padding: 20px;
            }

            table, th, td {
                font-size: 14px;
            }
        }

        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-top: 6px;
        }

        button {
            padding: 10px 18px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 12px;
        }

        .message {
            margin-top: 15px;
            font-weight: bold;
            color: #27ae60;
        }

        .error {
            color: #e74c3c;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="labto_dashboard.php">Dashboard</a>
        <a href="labto_labRecord.php">Lab Record</a>
        <a href="labto_equipmentRecord.php">Equipment Record</a>
        <a href="labto_profile.php">Profile</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="top-right"><a href="../logout.php">Logout</a></div>
            <h2>My Profile</h2>

            <?php if (isset($success)): ?>
                <div class="message"><?= $success ?></div>
            <?php elseif (isset($error)): ?>
                <div class="message error"><?= $error ?></div>
            <?php endif; 
            
            $displayRole = match($user['role']) {
            'labto' => 'Admin',
            'student' => 'Student',
            'lecture' => 'Lecturer',
            default => ucfirst($user['role'])
            };
            ?>
            
            <form method="POST">
                <table>
                    <tr>
                        <th>User ID</th>
                        <td><?= htmlspecialchars($user['user_id']) ?></td>
                    </tr>
                    <tr>
                        <th>Full Name</th>
                        <td><input type="text" name="admin_name" value="<?= htmlspecialchars($user['admin_name']) ?>" required></td>
                    </tr>
                    <tr>
                        <th>Username</th>
                        <td><input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" readonly></td>
                    </tr>
                    <tr>
                        <th>Phone Number</th>
                        <td><input type="text" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>" required></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td><?= htmlspecialchars($displayRole) ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><span class="status approved"><?= htmlspecialchars($user['status']) ?></span></td>
                    </tr>
                </table>
                <button type="submit">Update Profile</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
