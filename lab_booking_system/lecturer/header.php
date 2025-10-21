<?php
// header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>University Poly-Tech Malaysia</title>
    <link rel="stylesheet" href="css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #515cf5;
            color: white;
            padding: 10px 20px;
            div {
  box-shadow: 10px 10px 5px lightblue;
}
        }

        .top-header img {
            height: 60px;
        }

        .top-header h1 {
            font-size: 24px;
            margin: 0;
            font-weight: 500;
        }

        @media (max-width: 500px) {
            .top-header {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .top-header img {
                display: none;
            }

            .top-header h1 {
                font-size: 20px;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="top-header">
        <img src="../uptm logo.png" alt="UPTM Logo">
        <h1>Faculty Of Computing & Multimedia</h1>
    </div>
