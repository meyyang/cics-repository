<?php
// This file should only handle the view
// Database connection should be in a separate file that's included

// Include database connection
require_once dirname(__FILE__, 3) . '/app/config/database.php';
session_start();

if (isset($_SESSION['user_id'])) {
    error_log("Current user_id in session: " . $_SESSION['user_id']);
} else {
    error_log("No user_id found in session");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Source Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once dirname(__FILE__, 3) . '/app/includes/header.php'; ?>
    <main>
    <div class="container mt-0 mb-5">
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h3>Create Source Code</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/cics-repository/app/controllers/SourceCodeController.php" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="source_file" class="form-label">Source Code File</label>
                                <input type="file" class="form-control" id="source_file" name="source_file" accept=".txt,.php,.js,.html,.css,.java,.py" onchange="previewFile(this)">
                                <small class="text-muted">Accepted file types: .txt, .php, .js, .html, .css, .java, .py</small>
                                
                                <?php if ($fileName): ?>
                                <div class="file-info">
                                    <strong>Selected file:</strong> <?= htmlspecialchars($fileName) ?>
                                </div>
                                <?php endif; ?>
                                </div>

                                <!-- Add file content display area -->
                                <div class="mb-3">
                                    <div id="file_content" class="border rounded p-3" 
                                        style="min-height: 200px; max-height: 500px; overflow-y: auto; background-color: #f8f9fa; font-family: 'Consolas', monospace; font-size: 14px;">
                                        <pre style="margin: 0;"></pre>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Create Source Code</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </main

    <?php require_once dirname(__FILE__, 3) . '/app/includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('source_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const reader = new FileReader();
            const contentDiv = document.querySelector('#file_content pre');

            if (file) {
                reader.onload = function(e) {
                    contentDiv.textContent = e.target.result;
                };
                reader.readAsText(file);
            } else {
                contentDiv.textContent = '';
            }
        });
    </script>
</body>
</html>