<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once dirname(__FILE__, 3) . '/app/config/database.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['error'] = "You must log in to access this page.";
    header("Location: /cics-repository/app/views/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    try {
        // Validate input
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $user_id = $_SESSION['user_id'];

        if ($id <= 0 || empty($title) || empty($content)) {
            throw new Exception("Please fill in all required fields");
        }

        // Update the source code with user verification
        $stmt = $conn->prepare("UPDATE source_codes SET title = ?, description = ?, content = ? WHERE id = ? AND user_id = ?");
        if (!$stmt) {
            throw new Exception("Database error: " . $conn->error);
        }

        $stmt->bind_param("sssii", $title, $description, $content, $id, $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to update source code: " . $stmt->error);
        }

        if ($stmt->affected_rows > 0) {
            $_SESSION['success'] = "Source code updated successfully!";
            header("Location: /cics-repository/app/views/source_code_list.php");
            exit();
        } else {
            throw new Exception("No changes made or unauthorized access");
        }

    } catch (Exception $e) {
        error_log("Update error: " . $e->getMessage());
        $_SESSION['error'] = $e->getMessage();
        header("Location: /cics-repository/app/views/edit_source_code.php?id=" . $id);
        exit();
    }
} else {
    header("Location: /cics-repository/app/views/source_code_list.php");
    exit();
}