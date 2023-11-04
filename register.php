<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $passcode = bin2hex(random_bytes(5));
 
    echo $full_name;
 
    if (empty($full_name) || empty($email) || empty($phone)) {
       die("Please fill in all fields.");
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
       die("Invalid email format.");
    }
    
    $host = "your_database_host";
    $username = "your_username";
    $password = "your_password";
    $database = "deewill";
    
    $mysqli = new mysqli($host, $username, $password, $database);
    
    if ($mysqli->connect_error) {
       die("Connection failed: " . $mysqli->connect_error);
    }
    
    $sql = "INSERT INTO wedding-registration (full_name, email, phone, passcode) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssss", $full_name, $email, $phone, $passcode);
    
    if ($stmt->execute()) {
       echo "Data successfully stored in the database.";
    } else {
       echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
    $mysqli->close();
 
 }
 

?>