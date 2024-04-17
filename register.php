<?php
// Include the database configuration file
include_once 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$json_data = file_get_contents('php://input');

// Decode the JSON data into an associative array
$data = json_decode($json_data, true);

// Check if the JSON decoding was successful
if ($data === null) {
    // Handle JSON decoding error
    echo "Error decoding JSON data.";
} else {
    // Extract data from the decoded JSON
    $email = isset($data["email"]) ? trim($data["email"]) : "";
    $firstName = isset($data["firstName"]) ? trim($data["firstName"]) : "";
    $lastName = isset($data["lastName"]) ? trim($data["lastName"]) : "";
    $password = isset($data["password"]) ? password_hash(trim($data["password"]), PASSWORD_DEFAULT) : "";
    $accountType = isset($data["accountType"]) ? $data["accountType"] : "";

    // Your remaining code for processing and database insertion goes here
    // Make sure your database connection is properly established

    // Example of validation and database insertion
    if (empty($email) || empty($firstName) || empty($lastName) || empty($password) || empty($accountType)) {
        echo "All fields are required.";
    } else {
        // Insert user data into 'users' table
        $sql = "INSERT INTO users (email, first, last, password) VALUES ('$email', '$firstName', '$lastName', '$password')";
        if ($conn->query($sql) === TRUE) {
            echo "User registered successfully.";

            // Get the user ID of the newly inserted user
            $user_id = $conn->insert_id;

            // If account type is 'admin', insert into 'admin' table as well
            if ($accountType === "Admin") {
                $sql_admin = "INSERT INTO admin (admin_id, uid) VALUES (NULL, '$user_id')";
                if ($conn->query($sql_admin) === TRUE) {
                    echo "Admin registered successfully.";
                } else {
                    echo "Error: " . $sql_admin . "<br>" . $conn->error;
                }
            }
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Close connection
    $conn->close();
}
?>