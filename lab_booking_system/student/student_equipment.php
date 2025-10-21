<?php
include '../config.php';
session_start();
// Ensure only students can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}
?>

<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        margin: 0;
        padding: 0;
        background: #f4f6f8;
        color: #333;
    }

    img{
        max-width: 100%;
        height: 250px;
    }

    .container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 20px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    h2, h3 {
        color: #2c3e50;
        margin-bottom: 20px;
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

    @media (max-width: 600px) {
        .container {
            padding: 10px;
        }

        table, th, td {
            font-size: 13px;
        }
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
}
    .booking-card {
        background-color: #f9fbfc;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        max-width: 600px;
        margin-top: 30px;
        margin-bottom: 40px;
    }

    .booking-card h3 {
        margin-bottom: 20px;
        color: #2c3e50;
        font-size: 22px;
    }

    .booking-card label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #34495e;
    }

    .booking-card select,
    .booking-card input {
        width: 100%;
        padding: 12px;
        margin-bottom: 18px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 15px;
        background-color: #fff;
    }

    .booking-card input:focus,
    .booking-card select:focus {
        border-color: #3498db;
        outline: none;
    }

    .booking-card button {
        width: 100%;
        padding: 14px;
        background-color: #3498db;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .booking-card button:hover {
        background-color: #2980b9;
    }

    .filter-form label {
    font-weight: 600;
    margin-right: 10px;
}

.filter-form input[type="date"] {
    padding: 6px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.filter-form button {
    padding: 6px 12px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
}

.filter-form button:hover {
    background-color: #2980b9;
}

</style>


<?php
include 'header.php';
echo '<div class="wrapper">';
echo '<div class="sidebar">
        <h2>Student Panel</h2>
        <a href="student_dashboard.php">Dashboard</a>
        <a href="student_lab.php">Reserve Lab</a>
        <a href="student_equipment.php">Book Equipment </a>
        <a href="student_profile.php">Profile</a>
        <a href="../logout.php">Logout</a>
      </div>';
echo '<div class="main-content container">';
echo '<div class="top-right"><a href="logout.php">Logout</a></div>';

echo "<h2>Student Dashboard</h2>";
echo "<h3>Available Equipment</h3>";

echo '<form method="GET" class="filter-form" style="margin-bottom: 20px;">
    <label for="filter_date">Filter by Date:</label>
    <input type="date" name="filter_date" id="filter_date" value="' . htmlspecialchars($_GET['filter_date'] ?? '') . '">
    <button type="submit">Apply</button>
</form>';


$filterDate = $_GET['filter_date'] ?? '';
if ($filterDate) {
    $equip = $conn->query("
        SELECT Equipment_ID, Name, Type, Quantity, Available_Date
        FROM available_equipment
        WHERE Available_Date = '$filterDate'
        ORDER BY Available_Date ASC
    ");
} else {
    $equip = $conn->query("
        SELECT Equipment_ID, Name, Type, Quantity, Available_Date
        FROM available_equipment
        ORDER BY Available_Date ASC
    ");
}


if ($equip && $equip->num_rows > 0) {
    echo "<table><tr>
        <th>Name</th><th>Type</th><th>Quantity</th><th>Date</th>
    </tr>";
    while ($row = $equip->fetch_assoc()) {
        echo "<tr>
            <td>" . htmlspecialchars($row['Name']) . "</td>
            <td>" . htmlspecialchars($row['Type']) . "</td>
            <td>{$row['Quantity']}</td>
            <td>{$row['Available_Date']}</td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No equipment available for booking.</p>";
}

echo '<div class="booking-card">';
echo '<h3>Book Equipment</h3>';
echo '<form action="student_bookE.php" method="POST">
    <label for="equipment_id">Select Equipment</label>
    <select name="equipment_id" id="equipment_id" required>
        <option value="" disabled selected>Select equipment</option>';
        
    $equipList = $conn->query("
        SELECT Equipment_ID, Name, Type, Quantity, Available_Date
        FROM available_equipment
        ORDER BY Available_Date ASC
    ");
    if ($equipList && $equipList->num_rows > 0) {
        while ($eq = $equipList->fetch_assoc()) {
            $label = htmlspecialchars($eq['Name']) . " (" . $eq['Type'] . ", Qty: " . $eq['Quantity'] . ", " . $eq['Available_Date'] . ")";
            echo "<option value='{$eq['Equipment_ID']}'>$label</option>";
        }
    }

echo '</select>
    <label for="quantity">Quantity</label>
    <input type="number" name="quantity" id="quantity" min="1" required>

    <label for="booking_date">Booking Date</label>
    <input type="date" name="booking_date" id="booking_date" required>

    <input type="hidden" name="user_id" value="' . htmlspecialchars($_SESSION['user_id']) . '">

    <button type="submit">Submit Booking</button>
</form>';
echo '</div>';

echo "</div>";
echo '<script>';
echo '
document.querySelector(".booking-card form").addEventListener("submit", function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    fetch("student_bookE.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(response => {
        if (response.trim() === "success") {
            alert("✅ Booking successful!");
            setTimeout(() => {
                location.reload(); // refresh after 1.5 seconds
            }, 1500);
        } else {
            alert("❌ " + response);
        }
    })
    .catch(() => {
        alert("❌ Something went wrong. Please try again.");
    });
});
';
echo '</script>';
echo "</body></html>";
?>
