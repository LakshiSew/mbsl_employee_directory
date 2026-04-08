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
    <title>Users Management | MBSL Employee Directory</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/users.css">
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
                        <li><a href="users.php" class="active"><i class="bi bi-people-fill"></i><span>Users
                                    Management</span></a></li>
                        <li><a href="employees.php"><i class="bi bi-person-vcard-fill"></i><span>Employees
                                    Management</span></a></li>
                        <li><a href="profile.php"><i class="bi bi-person-circle"></i><span>My Profile</span></a></li>
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
                        <h2 class="page-title">Users Management</h2>
                        <p class="page-subtitle">Manage admin and staff user accounts with profile images</p>
                    </div>
                </div>

                <div class="topbar-right">
                    <button id="darkModeToggle" class="dark-toggle-btn me-3">
                        <i class="bi bi-moon-fill"></i>
                    </button>

                    <span class="role-badge">
                        <i class="bi bi-person-badge-fill me-1"></i>
                        <?php echo ucfirst(htmlspecialchars($role)); ?>
                    </span>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="summary-card purple-card">
                        <div class="summary-icon"><i class="bi bi-people-fill"></i></div>
                        <div>
                            <h6>Total Users</h6>
                            <h3 id="summaryTotalUsers">0</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="summary-card blue-card">
                        <div class="summary-icon"><i class="bi bi-shield-lock-fill"></i></div>
                        <div>
                            <h6>Admin Users</h6>
                            <h3 id="summaryAdminUsers">0</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="summary-card green-card">
                        <div class="summary-icon"><i class="bi bi-person-workspace"></i></div>
                        <div>
                            <h6>Staff Users</h6>
                            <h3 id="summaryStaffUsers">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-card">
                <div class="card-header-custom">
                    <div>
                        <h4>User Accounts</h4>
                        <p>Search, filter, view, add, edit, and delete system users</p>
                    </div>

                    <?php if ($role === "admin") { ?>
                    <button type="button" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-person-plus-fill me-2"></i>Add User
                    </button>
                    <?php } ?>
                </div>

                <div id="messageBox"></div>

                <form id="filterForm" class="filter-form mb-4">
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label">Search</label>
                            <input type="text" id="searchInput" class="form-control custom-input"
                                placeholder="Search by name or email">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Role Filter</label>
                            <select id="roleFilter" class="form-select custom-input">
                                <option value="">All Roles</option>
                                <option value="admin">Admin</option>
                                <option value="user">Staff User</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Sort By</label>
                            <select id="sortFilter" class="form-select custom-input">
                                <option value="id_desc">Newest First</option>
                                <option value="id_asc">Oldest First</option>
                                <option value="name_asc">Name A-Z</option>
                                <option value="name_desc">Name Z-A</option>
                                <option value="email_asc">Email A-Z</option>
                                <option value="email_desc">Email Z-A</option>
                                <option value="role_asc">Role A-Z</option>
                                <option value="role_desc">Role Z-A</option>
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn filter-btn w-100">
                                <i class="bi bi-funnel-fill me-2"></i>Apply
                            </button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table align-middle custom-table mb-0">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <tr>
                                <td colspan="7" class="text-center py-4">Loading users...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <nav class="mt-4">
                    <ul class="pagination justify-content-end custom-pagination" id="paginationArea"></ul>
                </nav>
            </div>

        </main>
    </div>

    <?php if ($role === "admin") { ?>
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content custom-modal">
                <div class="modal-header custom-modal-header">
                    <div>
                        <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Add New User</h5>
                        <p class="modal-subtitle mb-0">Create a new admin or staff account with profile image</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="addUserForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                    <input type="text" name="full_name" class="form-control"
                                        placeholder="Enter full name">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address</label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                    <input type="email" name="email" class="form-control"
                                        placeholder="Enter email address">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role</label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text"><i class="bi bi-person-badge-fill"></i></span>
                                    <select name="role" class="form-select">
                                        <option value="user">Staff User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Profile Image</label>
                                <input type="file" name="profile_picture" class="form-control file-input"
                                    accept="image/*">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" name="password" id="password" class="form-control"
                                        placeholder="Enter password">
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
                                    <button type="button" class="btn toggle-password" data-target="confirm_password">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6 d-flex align-items-end">
                                <div class="role-note w-100">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    Admin has full access. Staff user has limited access.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer custom-modal-footer">
                        <button type="button" class="btn cancel-btn" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn save-btn">
                            <i class="bi bi-check2-circle me-2"></i>Save User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content custom-modal">
                <div class="modal-header custom-modal-header">
                    <div>
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit User</h5>
                        <p class="modal-subtitle mb-0">Update user details and profile image</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editUserForm" enctype="multipart/form-data">
                    <input type="hidden" name="edit_user_id" id="edit_user_id">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Current Image</label>
                                <div class="current-image-box">
                                    <img src="../assets/images/default-user.jpg" id="edit_user_image_preview"
                                        class="edit-preview-image" alt="Current User Image">
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="remove_current_image"
                                        id="remove_current_image">
                                    <label class="form-check-label" for="remove_current_image">
                                        Remove current image
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Full Name</label>
                                        <div class="input-group custom-input-group">
                                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                            <input type="text" name="edit_full_name" id="edit_full_name"
                                                class="form-control" placeholder="Enter full name">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email Address</label>
                                        <div class="input-group custom-input-group">
                                            <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                            <input type="email" name="edit_email" id="edit_email" class="form-control"
                                                placeholder="Enter email address">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Role</label>
                                        <div class="input-group custom-input-group">
                                            <span class="input-group-text"><i
                                                    class="bi bi-person-badge-fill"></i></span>
                                            <select name="edit_role" id="edit_role" class="form-select">
                                                <option value="user">Staff User</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">New Profile Image</label>
                                        <input type="file" name="edit_profile_picture" class="form-control file-input"
                                            accept="image/*">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">New Password</label>
                                        <div class="input-group custom-input-group">
                                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                            <input type="password" name="edit_password" id="edit_password"
                                                class="form-control" placeholder="Leave blank to keep current password">
                                            <button type="button" class="btn toggle-password"
                                                data-target="edit_password">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Confirm New Password</label>
                                        <div class="input-group custom-input-group">
                                            <span class="input-group-text"><i class="bi bi-shield-lock-fill"></i></span>
                                            <input type="password" name="edit_confirm_password"
                                                id="edit_confirm_password" class="form-control"
                                                placeholder="Confirm new password">
                                            <button type="button" class="btn toggle-password"
                                                data-target="edit_confirm_password">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="role-note w-100">
                                            <i class="bi bi-info-circle-fill me-2"></i>
                                            Leave password fields empty if you do not want to change the password.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer custom-modal-footer">
                        <button type="button" class="btn cancel-btn" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn save-btn">
                            <i class="bi bi-check2-circle me-2"></i>Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content delete-modal">
                <form id="deleteUserForm">
                    <input type="hidden" name="delete_user_id" id="delete_user_id">

                    <div class="modal-body text-center p-4">
                        <div class="delete-icon-box">
                            <i class="bi bi-trash-fill"></i>
                        </div>
                        <h4 class="delete-title mt-3">Delete User</h4>
                        <div class="delete-image-wrap">
                            <img src="../assets/images/default-user.jpg" id="delete_user_image"
                                class="delete-user-image" alt="User">
                        </div>
                        <p class="delete-text mt-3">
                            Are you sure you want to delete
                            <strong id="delete_user_name"></strong>?
                        </p>
                    </div>

                    <div class="modal-footer border-0 justify-content-center pb-4">
                        <button type="button" class="btn cancel-btn" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn confirm-delete-btn">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php } ?>

    <div class="modal fade" id="viewUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content custom-modal">
                <div class="modal-header custom-modal-header">
                    <div>
                        <h5 class="modal-title"><i class="bi bi-eye-fill me-2"></i>View User Details</h5>
                        <p class="modal-subtitle mb-0">Profile information of selected user</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="view-user-wrapper">
                        <div class="view-user-image-box">
                            <img src="../assets/images/default-user.jpg" id="view_user_image" class="view-user-image"
                                alt="User">
                        </div>

                        <div class="view-user-info">
                            <div class="view-info-item"><span>ID</span><strong id="view_user_id"></strong></div>
                            <div class="view-info-item"><span>Full Name</span><strong id="view_user_name"></strong>
                            </div>
                            <div class="view-info-item"><span>Email</span><strong id="view_user_email"></strong></div>
                            <div class="view-info-item"><span>Role</span><strong id="view_user_role"></strong></div>
                            <div class="view-info-item"><span>Created Date</span><strong
                                    id="view_user_created"></strong></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer custom-modal-footer">
                    <button type="button" class="btn cancel-btn" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    window.USER_ROLE = "<?php echo $role; ?>";
    </script>
    <script src="../assets/js/auth.js"></script>
    <script src="../assets/js/users.js"></script>
    <script src="../assets/js/darkmode.js"></script>
</body>

</html>