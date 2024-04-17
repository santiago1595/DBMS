<?php

require_once "config.php";

// Ensure JSON content type
header("Content-Type: application/json");

$json_data = file_get_contents('php://input');

// Decode the JSON data into an associative array
$data = json_decode($json_data, true);

if ($data === null) {
    // Handle JSON decoding error
    echo json_encode(array("error" => "Error decoding JSON data."));
} else {
    $username = isset($data['userLogName']) ? trim($data['userLogName']) : "";
    $password = isset($data['loginPassword']) ? trim($data['loginPassword']) : "";

    $response = array();

    // Check if the user exists and password is correct
    $stmt = $conn->prepare("SELECT uid, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Password is correct
            // Check if the user is an admin
            $admin_stmt = $conn->prepare("SELECT admin_id FROM admin WHERE uid = ?");
            $admin_stmt->bind_param("i", $user_id);
            $admin_stmt->execute();
            $admin_stmt->store_result();
            
            if ($admin_stmt->num_rows > 0) {
                // User is an admin
                session_start();
                $_SESSION['loggedin'] = true;
                $_SESSION['uid'] = $user_id;
                $_SESSION['username'] = $username;

                // Fetch admin_id and store it in session
                $admin_stmt->bind_result($admin_id);
                $admin_stmt->fetch();
                $_SESSION['admin_id'] = $admin_id;

                $response['message'] = "Admin Login Successful";
            } else {
                // User is not an admin
                session_start();
                $_SESSION['loggedin'] = true;
                $_SESSION['uid'] = $user_id;
                $_SESSION['username'] = $username;
                $response['message'] = "Login Successful";
            }
            $admin_stmt->close();
        } else {
            // Password is incorrect
            $response['error'] = "Incorrect password!";
        }
    } else {
        // User not found
        $response['error'] = "User not found!";
    }

    // Close the statement
    $stmt->close();
    // Close the connection 
    $conn->close();

    // Output the JSON response
    echo json_encode($response);
}
?>
