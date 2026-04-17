<?php
session_start();
require 'conn.php';
require_once __DIR__ . '/contact_inquiry_mail.php';

if(!isset($_SESSION['id'])){
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$u_id = $_SESSION['id'];
$response = ['success' => false, 'message' => ''];

// Handle profile picture upload
if(isset($_POST['upload_picture']) && isset($_FILES['profile_picture'])) {
    $upload_dir = '../uploads/profiles/';
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    if($_FILES['profile_picture']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['profile_picture']['type'];
        
        if(in_array($file_type, $allowed_types)) {
            // Get current profile picture
            $sql = "SELECT profile_picture FROM accounts WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $u_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $current_pic = $result->fetch_assoc()['profile_picture'];
            
            // Delete old profile picture if exists
            if(!empty($current_pic) && file_exists($upload_dir . $current_pic)) {
                unlink($upload_dir . $current_pic);
            }
            
            // Upload new picture
            $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $new_filename = 'profile_' . $u_id . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if(move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                $sql = "UPDATE accounts SET profile_picture = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $new_filename, $u_id);
                
                if($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Profile picture updated successfully!';
                    $response['image_path'] = '../uploads/profiles/' . $new_filename;
                } else {
                    $response['message'] = 'Error updating database!';
                }
            } else {
                $response['message'] = 'Error uploading file!';
            }
        } else {
            $response['message'] = 'Invalid file type. Please upload JPG, PNG, GIF, or WEBP.';
        }
    } else {
        $response['message'] = 'File upload error!';
    }
    
    echo json_encode($response);
    exit();
}

// Handle profile picture reset
if(isset($_POST['reset_picture'])) {
    $upload_dir = '../uploads/profiles/';
    
    // Get current profile picture
    $sql = "SELECT profile_picture FROM accounts WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $u_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_pic = $result->fetch_assoc()['profile_picture'];
    
    // Delete current profile picture
    if(!empty($current_pic) && file_exists($upload_dir . $current_pic)) {
        unlink($upload_dir . $current_pic);
    }
    
    // Set to NULL in database
    $sql = "UPDATE accounts SET profile_picture = NULL WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $u_id);
    
    if($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Profile picture reset successfully!';
        $response['image_path'] = '../assets/img/avatars/5.png';
    } else {
        $response['message'] = 'Error resetting picture!';
    }
    
    echo json_encode($response);
    exit();
}

// Handle profile information update
if(isset($_POST['update_profile'])) {
    $firstName = trim($_POST['firstName']);
    $middleName = trim($_POST['middleName']);
    $lastName = trim($_POST['lastName']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    $sqlUt = 'SELECT ut_id, email FROM accounts WHERE user_id = ?';
    $stmtUt = $conn->prepare($sqlUt);
    $stmtUt->bind_param('i', $u_id);
    $stmtUt->execute();
    $utRow = $stmtUt->get_result()->fetch_assoc();
    $stmtUt->close();
    $ut_id = (int) ($utRow['ut_id'] ?? 0);
    $accountEmail = trim((string) ($utRow['email'] ?? ''));
    
    // Validate inputs
    if(empty($firstName) || empty($lastName) || empty($username)) {
        $response['message'] = 'First name, last name, and username are required!';
        echo json_encode($response);
        exit();
    }

    if ($accountEmail === '' || !filter_var($accountEmail, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Account email on file is missing or invalid. Contact an administrator.';
        echo json_encode($response);
        exit();
    }
    
    // Check if username already exists (for other users)
    $sql = "SELECT user_id FROM accounts WHERE username = ? AND user_id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $username, $u_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $response['message'] = 'Username already taken!';
        echo json_encode($response);
        exit();
    }
    
    // Update profile
    if(!empty($password)) {
        // Validate password
        if($password !== $confirmPassword) {
            $response['message'] = 'Passwords do not match!';
            echo json_encode($response);
            exit();
        }
        
        if(strlen($password) < 6) {
            $response['message'] = 'Password must be at least 6 characters!';
            echo json_encode($response);
            exit();
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Update with password
        $sql = "UPDATE accounts SET f_name = ?, m_name = ?, l_name = ?, username = ?, password = ?, email = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $firstName, $middleName, $lastName, $username, $hashed_password, $accountEmail, $u_id);
    } else {
        // Update without password
        $sql = "UPDATE accounts SET f_name = ?, m_name = ?, l_name = ?, username = ?, email = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $firstName, $middleName, $lastName, $username, $accountEmail, $u_id);
    }
    
    if($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Profile updated successfully!';
    } else {
        $response['message'] = 'Error updating profile!';
    }
    
    echo json_encode($response);
    exit();
}

// Handle account deactivation
if(isset($_POST['deactivate_account'])) {
    $sql = "UPDATE accounts SET user_status = 0 WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $u_id);
    
    if($stmt->execute()) {
        session_destroy();
        $response['success'] = true;
        $response['message'] = 'Account deactivated successfully!';
        $response['redirect'] = '../../../login.php';
    } else {
        $response['message'] = 'Error deactivating account!';
    }
    
    echo json_encode($response);
    exit();
}

$conn->close();
?>