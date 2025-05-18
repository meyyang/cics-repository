<!DOCTYPE html>
<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug logging
error_log("Session data: " . print_r($_SESSION, true));

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['error'] = "You must log in to access this page.";
    header("Location: /cics-repository/app/views/login.php");
    exit();
}

// Include database connection
require_once dirname(__FILE__, 3) . '/app/config/database.php';

// Get user_id from session
$user_id = $_SESSION['user_id'];
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Source Code List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        .btn-action {
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <?php require_once dirname(__FILE__, 3) . '/app/includes/header.php'; ?>

    <div class="container mt-0 mb-5">
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0">My Source Codes</h3>
                <a href="/cics-repository/app/views/source_code_form.php" class="btn btn-light">Add New Source Code</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Date Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                // Fetch only source codes belonging to the logged-in user
                                $sql = "SELECT id, user_id, title, description, created_at 
                                       FROM source_codes 
                                       WHERE user_id = ? 
                                       ORDER BY created_at DESC";
                                       
                                $stmt = $conn->prepare($sql);
                                
                                if (!$stmt) {
                                    throw new Exception("Prepare failed: " . $conn->error);
                                }
                                
                                $stmt->bind_param("i", $user_id);
                                
                                if (!$stmt->execute()) {
                                    throw new Exception("Execute failed: " . $stmt->error);
                                }
                                
                                $result = $stmt->get_result();

                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                            ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                                            <td><?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="/cics-repository/app/views/view_source_code.php?id=<?php echo $row['id']; ?>" 
                                                       class="btn btn-sm btn-info btn-action" 
                                                       title="View Code">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="/cics-repository/app/views/edit_source_code.php?id=<?php echo $row['id']; ?>" 
                                                       class="btn btn-sm btn-warning btn-action" 
                                                       title="Edit Code">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger btn-action" 
                                                            title="Delete Code"
                                                            onclick="deleteCode(<?php echo $row['id']; ?>)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                            <?php
                                    }
                                } else {
                            ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No source codes found. Add your first source code!</td>
                                    </tr>
                            <?php
                                }
                            } catch (Exception $e) {
                                error_log("Database error: " . $e->getMessage());
                            ?>
                                <tr>
                                    <td colspan="4" class="text-center text-danger">
                                        An error occurred while fetching the data. Please try again later.
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php require_once dirname(__FILE__, 3) . '/app/includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function deleteCode(id) {
        if (confirm('Are you sure you want to delete this source code?')) {
            fetch('/cics-repository/app/controllers/SourceCodeController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete&id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error deleting source code: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting source code');
            });
        }
    }
    </script>
</body>
</html>