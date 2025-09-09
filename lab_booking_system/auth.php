<?php
// auth.php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, username, password, role FROM users WHERE username = ? AND status = 'active'");
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

            // Map roles to correct folder and file
            $roleMap = [
                'student'    => 'student/student_dashboard.php',
                'lecture'    => 'lecturer/lecture_dashboard.php',
                'instructor' => 'instructor/instructor_dashboard.php',
                'lab'        => 'labto/labto_dashboard.php',
                'admin'      => 'admin/admin_dashboard.php'
            ];

            $role = trim($user['role']);

            if (isset($roleMap[$role]) && file_exists($roleMap[$role])) {
                header("Location: " . $roleMap[$role]);
                exit();
            } else {
                echo "<h3>❌ Dashboard not found for role: '$role'</h3>";
                echo "<p>Expected path: " . ($roleMap[$role] ?? 'undefined') . "</p>";
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
