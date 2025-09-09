<?php
// register.php
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
    <h2>Register</h2>
    <form action="register_process.php" method="POST" id="registerForm">
        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <label>Role:</label>
        <select name="role" id="role" required onchange="toggleRoleFields()">
            <option value="">Select Role</option>
            <option value="student">Student</option>
            <option value="lecture">Lecture</option>
            <!-- <option value="instructor">Instructor</option> -->
            <option value="labto">Admin</option>
        </select><br><br>

        <div id="studentFields" style="display:none;">
            <label>Student ID:</label><br>
            <input type="text" name="student_id"><br><br>

            <label>Name:</label><br>
            <input type="text" name="student_name"><br><br>

            <label>Semester:</label><br>
            <input type="number" name="semester" min="1" max="12"><br><br>
        </div>

        <div id="instructorFields" style="display:none;">
            <label>Name:</label><br>
            <input type="text" name="instructor_name"><br><br>
        </div>

        <div id="labtoFields" style="display:none;">
            <label>Name:</label><br>
            <input type="text" name="labto_name"><br><br>
        </div>

        <div id="lectureFields" style="display:none;">
            <label>Name:</label><br>
            <input type="text" name="lecture_name"><br><br>

            <label>Department:</label><br>
            <input type="text" name="department"><br><br>
        </div>

        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

<script>
function toggleRoleFields() {
    var role = document.getElementById('role').value;
    document.getElementById('studentFields').style.display = (role === 'student') ? 'block' : 'none';
    document.getElementById('instructorFields').style.display = (role === 'instructor') ? 'block' : 'none';
    document.getElementById('labtoFields').style.display = (role === 'labto') ? 'block' : 'none';
    document.getElementById('lectureFields').style.display = (role === 'lecture') ? 'block' : 'none';
}
</script>

</body>
</html>

