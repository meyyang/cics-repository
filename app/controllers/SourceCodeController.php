<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['error'] = "You must log in to access this page.";
    header("Location: /cics-repository/app/views/login.php");
    exit();
}

// Include database connection
require_once dirname(__FILE__, 3) . '/app/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    try {
        $id = (int)$_POST['id'];
        $user_id = $_SESSION['user_id'];

        // Verify ownership before deleting
        $stmt = $conn->prepare("SELECT user_id FROM source_codes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $sourceCode = $result->fetch_assoc();

        if (!$sourceCode || $sourceCode['user_id'] != $user_id) {
            throw new Exception("Unauthorized access");
        }

        // Delete the source code
        $stmt = $conn->prepare("DELETE FROM source_codes WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception("Delete failed");
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $content = trim($_POST['content']);
    $created_at = date('Y-m-d H:i:s');
    
    // Verify user_id exists in session
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "User session error. Please login again.";
        header("Location: /cics-repository/app/views/login.php");
        exit();
    }
    
    $user_id = $_SESSION['user_id'];

    try {
        // Debug information
        error_log("Attempting to insert source code for user_id: " . $user_id);
        
        // Prepare SQL statement
        $sql = "INSERT INTO source_codes (user_id, title, description, content, created_at) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Database prepare error: " . $conn->error);
        }
        
        $stmt->bind_param("issss", $user_id, $title, $description, $content, $created_at);
        
        // Execute the statement
        if ($stmt->execute()) {
            $_SESSION['success'] = "Source code successfully added!";
            header("Location: /cics-repository/app/views/source_code_list.php");
            exit();
        } else {
            throw new Exception("Database execution error: " . $stmt->error);
        }
    } catch (Exception $e) {
        error_log("Source code insertion error: " . $e->getMessage());
        $_SESSION['error'] = "Error saving the source code: " . $e->getMessage();
        header("Location: /cics-repository/app/views/source_code_form.php");
        exit();
    }
}
?>