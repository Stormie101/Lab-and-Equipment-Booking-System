<?php
// login.php
include 'header.php';
?>

<style>
    * {
        box-sizing: border-box;
    }

    .top-header{
        background-color: #f0f2f5;
        color:black;
    }

    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f0f2f5;
    }

    img{
        max-width: 100%;
        height: 140px;
    }

    h1{
        font-size: 23px;
    }

    .container {
        width: 100%;
        max-width: 400px;
        margin: 30px auto;
        padding: 15px;
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
        color: #444;
    }

    input, select {
        width: 100%;
        padding: 12px;
        margin-bottom: 18px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 16px;
    }

    input:focus, select:focus {
        border-color: #007bff;
        outline: none;
    }

    button {
        width: 100%;
        padding: 14px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #0056b3;
    }

    p {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
    }

    a {
        color: #007bff;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    @media (max-width: 500px) {
        .container {
            max-width: 300px;
            margin: 20px auto;
        }

        input, select, button {
            font-size: 15px;
            padding: 12px;
        }

        h2 {
            
        }
    }

</style>

<div class="container">
    <h2>Login</h2>
    <form action="auth.php" method="POST">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <!-- <label for="role">Role</label>
        <select id="role" name="role" required>
            <option value="">Select Role</option>
            <option value="student">Student</option>
            <option value="lecture">Lecture</option>
            <option value="labto">Admin</option>
            <option value="instructor">Instructor</option>
            
        </select> -->

        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>

</body>
</html>
