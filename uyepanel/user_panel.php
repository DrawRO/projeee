<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/db.php';

$lang = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
$messages = include "lang/{$lang}.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Control Panel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #74ebd5, #9face6);
            color: #333;
            overflow-x: hidden;
        }
        /* Sidebar styles */
        #sidebar {
            min-height: 100vh;
            background: #2f3542;
            color: #f1f2f6;
            position: fixed;
            left: 0;
            width: 250px;
            transition: left 0.3s ease;
            z-index: 1000;
        }
        #sidebar .nav-link {
            color: #f1f2f6;
            transition: all 0.2s ease-in-out;
            padding: 12px 20px;
            font-weight: 500;
        }
        #sidebar .nav-link:hover, #sidebar .nav-link.active {
            color: #2ed573;
            background: #57606f;
        }
        #sidebar .nav-link i {
            margin-right: 10px;
        }
        .main-content {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin-left: 250px;
            transition: margin-left 0.3s ease;
        }
        /* Hamburger Menu and Overlay for Mobile */
        .hamburger-menu {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            font-size: 1.5rem;
            color: #2f3542;
            cursor: pointer;
            z-index: 1050;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 900;
        }
        /* Mobile Specific Styles */
        @media (max-width: 768px) {
            #sidebar {
                width: 100%;
                left: -100%;
            }
            #sidebar.active {
                left: 0;
            }
            .main-content {
                margin-left: 0;
            }
            .hamburger-menu {
                display: block;
            }
            .overlay.active {
                display: block;
            }
        }
    </style>
</head>
<body>
    <!-- Overlay for sidebar -->
    <div class="overlay" onclick="toggleSidebar()"></div>

    <!-- Hamburger Menu Button -->
    <div class="hamburger-menu" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar">
                <ul class="nav flex-column pt-3">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="user_panel.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <i class="fas fa-user-circle"></i> <?php echo $messages['profile']; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="notifications.php">
                            <i class="fas fa-bell"></i> Notifications
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="chat.php">
                            <i class="fas fa-comments"></i> Chat
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">
                            <i class="fas fa-cogs"></i> Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content" id="main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                    <h1 class="h2">Dashboard</h1>
                </div>
                
                <!-- Quick Stats Section -->
                <div class="row mb-4 text-center">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h4>Total Logins</h4>
                            <p>45</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h4>New Messages</h4>
                            <p>3</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h4>Tasks Completed</h4>
                            <p>8</p>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Task List -->
                    <div class="col-md-6">
                        <div class="task-card">
                            <h5>Today's Tasks</h5>
                            <ul class="list-group">
                                <li class="list-group-item">Check new messages</li>
                                <li class="list-group-item">Update profile details</li>
                                <li class="list-group-item">Review recent notifications</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="col-md-6">
                        <div class="recent-activity">
                            <h5>Recent Activity</h5>
                            <ul class="list-group">
                                <li class="list-group-item">Logged in at 10:00 AM</li>
                                <li class="list-group-item">Viewed notifications</li>
                                <li class="list-group-item">Updated profile picture</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS, Popper.js, and Font Awesome Icons -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Sidebar Toggle Script -->
    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("active");
            document.querySelector(".overlay").classList.toggle("active");
        }
    </script>
</body>
</html>
