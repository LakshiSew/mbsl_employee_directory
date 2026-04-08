<?php
require_once "../app/helpers/Session.php";

Session::start();

if (!Session::has("user_id")) {
    header("Location: login.php");
    exit();
}

$full_name = Session::get("full_name");
$role = Session::get("role");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | MBSL Employee Directory</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>

<body>

    <div class="dashboard-wrapper">

        <aside class="sidebar" id="sidebar">
            <div class="sidebar-inner">
                <button class="sidebar-close-btn" id="sidebarCloseBtn" type="button">
                    <i class="bi bi-x-lg"></i>
                </button>

                <div class="sidebar-top">
                    <div class="brand-box">
                        <div class="brand-icon">
                            <i class="bi bi-buildings-fill"></i>
                        </div>
                        <div>
                            <h4 class="brand-title">MBSL Directory</h4>
                            <p class="brand-subtitle">Admin Panel</p>
                        </div>
                    </div>
                </div>

                <div class="sidebar-middle">
                    <ul class="sidebar-menu">
                        <li><a href="dashboard.php"><i class="bi bi-grid-fill"></i><span>Dashboard</span></a></li>
                        <li><a href="users.php"><i class="bi bi-people-fill"></i><span>Users Management</span></a></li>
                        <li><a href="employees.php"><i class="bi bi-person-vcard-fill"></i><span>Employees
                                    Management</span></a></li>
                        <li><a href="profile.php" class="active"><i class="bi bi-person-circle"></i><span>My
                                    Profile</span></a></li>
                    </ul>
                </div>

                <div class="sidebar-bottom">
                    <button id="logoutBtn" class="logout-btn" type="button">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </button>
                </div>
            </div>
        </aside>

        <main class="main-content">

            <div class="topbar">
                <div class="topbar-left">
                    <button class="mobile-menu-btn" id="mobileMenuBtn" type="button">
                        <i class="bi bi-list"></i>
                    </button>

                    <div>
                        <h2 class="page-title">My Profile</h2>
                        <p class="page-subtitle">View and update your account details</p>
                    </div>
                </div>

                <div class="topbar-right">
                    <span class="role-badge">
                        <i class="bi bi-person-badge-fill me-1"></i>
                        <?php echo ucfirst(htmlspecialchars($role)); ?>
                    </span>
                </div>
            </div>

            <div id="messageBox"></div>

            <div class="profile-card">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="profile-view-box">
                            <img src="../assets/images/default-user.jpg" id="profileImagePreview"
                                class="profile-main-image" alt="Profile">
                            <h4 id="profileName">Loading...</h4>
                            <p id="profileEmail">Loading...</p>
                            <span class="profile-role-badge" id="profileRole">Loading...</span>

                            <div class="profile-meta">
                                <div class="meta-item">
                                    <span>Member Since</span>
                                    <strong id="profileCreatedAt">-</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="profile-form-box">
                            <h4 class="form-section-title">Update Profile</h4>

                            <form id="profileForm" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="full_name" id="full_name"
                                            class="form-control custom-input" placeholder="Enter full name">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" name="email" id="email" class="form-control custom-input"
                                            placeholder="Enter email">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">New Profile Picture</label>
                                        <input type="file" name="profile_picture" id="profile_picture"
                                            class="form-control file-input" accept="image/*">
                                    </div>

                                    <div class="col-md-6 mb-3 d-flex align-items-end">
                                        <div class="form-check remove-check">
                                            <input class="form-check-input" type="checkbox" name="remove_current_image"
                                                id="remove_current_image">
                                            <label class="form-check-label" for="remove_current_image">
                                                Remove current profile image
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">New Password</label>
                                        <div class="input-group custom-input-group">
                                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                            <input type="password" name="password" id="password" class="form-control"
                                                placeholder="Leave blank to keep current password">
                                            <button type="button" class="btn toggle-password" data-target="password">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Confirm Password</label>
                                        <div class="input-group custom-input-group">
                                            <span class="input-group-text"><i class="bi bi-shield-lock-fill"></i></span>
                                            <input type="password" name="confirm_password" id="confirm_password"
                                                class="form-control" placeholder="Confirm password">
                                            <button type="button" class="btn toggle-password"
                                                data-target="confirm_password">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-12 mb-4">
                                        <div class="profile-note">
                                            <i class="bi bi-info-circle-fill me-2"></i>
                                            Leave password fields empty if you do not want to change your password.
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn save-btn" id="profileUpdateBtn">
                                        <i class="bi bi-check2-circle me-2"></i>Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/auth.js"></script>
    <script src="../assets/js/profile.js"></script>
</body>

</html>