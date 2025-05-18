<?php
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

require_once dirname(__FILE__, 3) . '/app/config/database.php';

// Validate ID parameter
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['error'] = "Invalid source code ID";
    header("Location: /cics-repository/app/views/source_code_list.php");
    exit();
}

try {
    // Fetch source code with user verification
    $stmt = $conn->prepare("SELECT * FROM source_codes WHERE id = ? AND user_id = ?");
    if (!$stmt) {
        throw new Exception("Database error: " . $conn->error);
    }
    
    $user_id = $_SESSION['user_id'];
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $code = $result->fetch_assoc();

    if (!$code) {
        throw new Exception("Source code not found or access denied");
    }
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: /cics-repository/app/views/source_code_list.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Source Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once dirname(__FILE__, 3) . '/app/includes/header.php'; ?>

    <div class="container mt-5 mb-5">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Edit Source Code</h3>
                <a href="/cics-repository/app/views/source_code_list.php" class="btn btn-light">Back to List</a>
            </div>
            <div class="card-body">
                <form method="POST" action="/cics-repository/app/controllers/update_source_code.php">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($code['id']); ?>">
                    <input type="hidden" name="action" value="update">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo htmlspecialchars($code['title']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="3"><?php echo htmlspecialchars($code['description']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Source Code</label>
                        <textarea class="form-control" id="content" name="content" 
                                  rows="10" required><?php echo htmlspecialchars($code['content']); ?></textarea>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="/cics-repository/app/views/source_code_list.php" class="btn btn-secondary me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Source Code</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>