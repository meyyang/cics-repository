<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CICS Repository</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/cics-repository/assets/css/style.css">
    <style>
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar-brand img {
            height: 40px;
            width: auto;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-success bg-success text-white">
            <div class="container">
                <a class="navbar-brand" href="/cics-repository/">
                    <img src="/cics-repository/app/includes/sjcb logo.png" alt="SJCB Logo">
                    CICS Repository
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/cics-repository/app/views/dashboard.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/cics-repository/app/views/source_code_list.php">Browse</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/cics-repository/about.php">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/source-code">Source Codes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/documents">Documents</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-white" href="#" id="profileDropdown" 
                                   role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle me-1"></i>
                                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                    <li>
                                        <div class="dropdown-item-text">
                                            <small class="d-block text-muted">Signed in as</small>
                                            <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                                        </div>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="/cics-repository/app/views/profile.php">
                                            <i class="bi bi-person me-2"></i>My Profile
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="/cics-repository/app/views/source_code_list.php">
                                            <i class="bi bi-code-square me-2"></i>My Source Codes
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="/cics-repository/app/views/logout.php">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="/cics-repository/app/views/login.php">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Login
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-4">

        <!-- Page-specific content goes here -->    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>