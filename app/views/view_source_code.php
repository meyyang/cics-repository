<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['error'] = "You must log in to access this page.";
    header("Location: /cics-repository/app/views/login.php");
    exit();
}

// Include database connection
require_once dirname(__FILE__, 3) . '/app/config/database.php';

// Get the source code ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

try {
    // Fetch the source code details
    $stmt = $conn->prepare("SELECT title, description, content, created_at FROM source_codes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $sourceCode = $result->fetch_assoc();

    if (!$sourceCode) {
        throw new Exception("Source code not found or access denied");
    }
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: source_code_list.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Source Code - <?php echo htmlspecialchars($sourceCode['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/themes/prism.css" rel="stylesheet">
    <style>
        pre {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.25rem;
            max-height: 500px;
            overflow-y: auto;
        }
        .code-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .code-metadata {
            color: #6c757d;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <?php require_once dirname(__FILE__, 3) . '/app/includes/header.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0"><?php echo htmlspecialchars($sourceCode['title']); ?></h3>
                <a href="source_code_list.php" class="btn btn-light">Back to List</a>
            </div>
            <div class="card-body">
                <div class="code-metadata mb-3">
                    <strong>Created:</strong> <?php echo date('M d, Y h:i A', strtotime($sourceCode['created_at'])); ?>
                </div>
                
                <?php if (!empty($sourceCode['description'])): ?>
                <div class="mb-4">
                    <h5>Description:</h5>
                    <p><?php echo nl2br(htmlspecialchars($sourceCode['description'])); ?></p>
                </div>
                <?php endif; ?>

                <h5>Source Code:</h5>
                <pre><code class="language-php"><?php echo htmlspecialchars($sourceCode['content']); ?></code></pre>
                
                <div class="mt-4">
                    <button class="btn btn-secondary" onclick="copyToClipboard()">
                        Copy Code
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php require_once dirname(__FILE__, 3) . '/app/includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/prism.min.js"></script>
    <script>
    function copyToClipboard() {
        const codeElement = document.querySelector('pre code');
        const textArea = document.createElement('textarea');
        textArea.value = codeElement.textContent;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            alert('Code copied to clipboard!');
        } catch (err) {
            console.error('Failed to copy code:', err);
            alert('Failed to copy code');
        }
        document.body.removeChild(textArea);
    }
    </script>
</body>
</html>