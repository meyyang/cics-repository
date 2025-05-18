<?php
<?php
session_start();
require_once dirname(__FILE__, 3) . '/app/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $content = trim($_POST['content']);
    
    if (empty($title) || empty($content)) {
        $_SESSION['error'] = "Title and content are required fields.";
        header("Location: ../views/source_code_form.php");
        exit();
    }

    try {
        $sql = "INSERT INTO source_codes (title, description, content, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$title, $description, $content]);
        
        $_SESSION['success'] = "Source code successfully added!";
        header("Location: ../views/display_source_code.php?id=" . $conn->lastInsertId());
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error saving source code: " . $e->getMessage();
        header("Location: ../views/source_code_form.php");
        exit();
    }
} else {
    header("Location: ../views/source_code_form.php");
    exit();
}