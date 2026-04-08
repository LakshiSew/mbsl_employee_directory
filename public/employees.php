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
    <title>Employees Management | MBSL Employee Directory</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/employees.css">
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
                        <li><a href="employees.php" class="active"><i
                                    class="bi bi-person-vcard-fill"></i><span>Employees Management</span></a></li>
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
                        <h2 class="page-title">Employees Management</h2>
                        <p class="page-subtitle">Manage employee directory with photo, department, and status</p>
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
                <div class="col-lg-3 col-md-6">
                    <div class="summary-card purple-card">
                        <div class="summary-icon"><i class="bi bi-people-fill"></i></div>
                        <div>
                            <h6>Total Employees</h6>
                            <h3 id="summaryTotalEmployees">0</h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="summary-card green-card">
                        <div class="summary-icon"><i class="bi bi-person-check-fill"></i></div>
                        <div>
                            <h6>Active</h6>
                            <h3 id="summaryActiveEmployees">0</h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="summary-card red-card">
                        <div class="summary-icon"><i class="bi bi-person-x-fill"></i></div>
                        <div>
                            <h6>Inactive</h6>
                            <h3 id="summaryInactiveEmployees">0</h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="summary-card orange-card">
                        <div class="summary-icon"><i class="bi bi-person-dash-fill"></i></div>
                        <div>
                            <h6>On Leave</h6>
                            <h3 id="summaryOnLeaveEmployees">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-card">
                <div class="card-header-custom">
                    <div>
                        <h4>Employee Directory</h4>
                        <p>Search, filter, and view employee records</p>
                    </div>

                    <div class="header-action-buttons">
                        <a href="../api/employees/export.php?type=csv" class="btn export-csv-btn">
                            <i class="bi bi-file-earmark-excel-fill me-2"></i>Export CSV
                        </a>

                        <a href="../api/employees/export.php?type=pdf" class="btn export-pdf-btn">
                            <i class="bi bi-file-earmark-pdf-fill me-2"></i>Export PDF
                        </a>

                        <?php if ($role === "admin") { ?>
                        <button type="button" class="btn add-btn" data-bs-toggle="modal"
                            data-bs-target="#addEmployeeModal">
                            <i class="bi bi-person-plus-fill me-2"></i>Add Employee
                        </button>
                        <?php } ?>
                    </div>
                </div>

                <div id="messageBox"></div>

                <form id="employeeFilterForm" class="filter-form mb-4">
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Search</label>
                            <input type="text" id="employeeSearchInput" class="form-control custom-input"
                                placeholder="Code, name, email, designation">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Department</label>
                            <select id="departmentFilter" class="form-select custom-input">
                                <option value="">All Departments</option>
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">Status</label>
                            <select id="statusFilter" class="form-select custom-input">
                                <option value="">All Status</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="On Leave">On Leave</option>
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">Sort By</label>
                            <select id="employeeSortFilter" class="form-select custom-input">
                                <option value="id_desc">Newest First</option>
                                <option value="id_asc">Oldest First</option>
                                <option value="name_asc">Name A-Z</option>
                                <option value="name_desc">Name Z-A</option>
                                <option value="department_asc">Department A-Z</option>
                                <option value="department_desc">Department Z-A</option>
                                <option value="join_date_asc">Join Date ↑</option>
                                <option value="join_date_desc">Join Date ↓</option>
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
                                <th>Photo</th>
                                <th>Code</th>
                                <th>Full Name</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Status</th>
                                <th>Join Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="employeesTableBody">
                            <tr>
                                <td colspan="8" class="text-center py-4">Loading employees...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <nav class="mt-4">
                    <ul class="pagination justify-content-end custom-pagination" id="employeePaginationArea"></ul>
                </nav>
            </div>

        </main>
    </div>

    <?php if ($role === "admin") { ?>
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content custom-modal">
                <div class="modal-header custom-modal-header">
                    <div>
                        <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Add New Employee</h5>
                        <p class="modal-subtitle mb-0">Create employee record with all main details</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="addEmployeeForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-4 mb-3">
                                <label class="form-label">Employee Code</label>
                                <input type="text" name="employee_code" class="form-control custom-input"
                                    placeholder="EMP001">
                            </div>

                            <div class="col-lg-4 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" class="form-control custom-input"
                                    placeholder="Enter full name">
                            </div>

                            <div class="col-lg-4 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control custom-input"
                                    placeholder="Enter email">
                            </div>

                            <div class="col-lg-4 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control custom-input"
                                    placeholder="Enter phone number">
                            </div>

                            <div class="col-lg-4 mb-3">
                                <label class="form-label">Department</label>
                                <input type="text" name="department" class="form-control custom-input"
                                    placeholder="IT / HR / Finance">
                            </div>

                            <div class="col-lg-4 mb-3">
                                <label class="form-label">Designation</label>
                                <input type="text" name="designation" class="form-control custom-input"
                                    placeholder="Software Engineer">
                            </div>

                            <div class="col-lg-4 mb-3">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select custom-input">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other" selected>Other</option>
                                </select>
                            </div>

                            <div class="col-lg-4 mb-3">
                                <label class="form-label">Join Date</label>
                                <input type="date" name="join_date" class="form-control custom-input">
                            </div>

                            <div class="col-lg-4 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select custom-input">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                    <option value="On Leave">On Leave</option>
                                </select>
                            </div>

                            <div class="col-lg-6 mb-3">
                                <label class="form-label">Employee Photo</label>
                                <input type="file" name="photo" class="form-control file-input" accept="image/*">
                            </div>

                            <div class="col-lg-6 mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control textarea-input" rows="3"
                                    placeholder="Enter address"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer custom-modal-footer">
                        <button type="button" class="btn cancel-btn" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn save-btn">
                            <i class="bi bi-check2-circle me-2"></i>Save Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php } ?>

    <div class="modal fade" id="viewEmployeeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content custom-modal">
                <div class="modal-header custom-modal-header">
                    <div>
                        <h5 class="modal-title"><i class="bi bi-eye-fill me-2"></i>View Employee Details</h5>
                        <p class="modal-subtitle mb-0">Full employee record details</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="view-employee-wrapper">
                        <div class="view-employee-image-box">
                            <img src="../assets/images/default-user.jpg" id="view_employee_photo"
                                class="view-employee-image" alt="Employee">
                        </div>

                        <div class="view-employee-info">
                            <div class="view-info-item"><span>Employee Code</span><strong
                                    id="view_employee_code"></strong></div>
                            <div class="view-info-item"><span>Full Name</span><strong id="view_employee_name"></strong>
                            </div>
                            <div class="view-info-item"><span>Email</span><strong id="view_employee_email"></strong>
                            </div>
                            <div class="view-info-item"><span>Phone</span><strong id="view_employee_phone"></strong>
                            </div>
                            <div class="view-info-item"><span>Department</span><strong
                                    id="view_employee_department"></strong></div>
                            <div class="view-info-item"><span>Designation</span><strong
                                    id="view_employee_designation"></strong></div>
                            <div class="view-info-item"><span>Gender</span><strong id="view_employee_gender"></strong>
                            </div>
                            <div class="view-info-item"><span>Join Date</span><strong
                                    id="view_employee_join_date"></strong></div>
                            <div class="view-info-item"><span>Status</span><strong id="view_employee_status"></strong>
                            </div>
                            <div class="view-info-item"><span>Address</span><strong id="view_employee_address"></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer custom-modal-footer">
                    <button type="button" class="btn cancel-btn" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php if ($role === "admin") { ?>
    <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content custom-modal">
                <div class="modal-header custom-modal-header">
                    <div>
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Employee</h5>
                        <p class="modal-subtitle mb-0">Update employee details and photo</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editEmployeeForm" enctype="multipart/form-data">
                    <input type="hidden" name="edit_employee_id" id="edit_employee_id">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-3 mb-3">
                                <label class="form-label">Current Photo</label>
                                <div class="current-image-box">
                                    <img src="../assets/images/default-user.jpg" id="edit_employee_photo_preview"
                                        class="edit-preview-image" alt="Employee">
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="remove_current_photo"
                                        id="remove_current_photo">
                                    <label class="form-check-label" for="remove_current_photo">
                                        Remove current photo
                                    </label>
                                </div>
                            </div>

                            <div class="col-lg-9">
                                <div class="row">
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label">Employee Code</label>
                                        <input type="text" name="edit_employee_code" id="edit_employee_code"
                                            class="form-control custom-input">
                                    </div>

                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="edit_full_name" id="edit_full_name"
                                            class="form-control custom-input">
                                    </div>

                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="edit_email" id="edit_email"
                                            class="form-control custom-input">
                                    </div>

                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="edit_phone" id="edit_phone"
                                            class="form-control custom-input">
                                    </div>

                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label">Department</label>
                                        <input type="text" name="edit_department" id="edit_department"
                                            class="form-control custom-input">
                                    </div>

                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label">Designation</label>
                                        <input type="text" name="edit_designation" id="edit_designation"
                                            class="form-control custom-input">
                                    </div>

                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label">Gender</label>
                                        <select name="edit_gender" id="edit_gender" class="form-select custom-input">
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>

                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label">Join Date</label>
                                        <input type="date" name="edit_join_date" id="edit_join_date"
                                            class="form-control custom-input">
                                    </div>

                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="edit_status" id="edit_status" class="form-select custom-input">
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                            <option value="On Leave">On Leave</option>
                                        </select>
                                    </div>

                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label">New Photo</label>
                                        <input type="file" name="edit_photo" class="form-control file-input"
                                            accept="image/*">
                                    </div>

                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label">Address</label>
                                        <textarea name="edit_address" id="edit_address"
                                            class="form-control textarea-input" rows="3"></textarea>
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
                            <i class="bi bi-check2-circle me-2"></i>Update Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteEmployeeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content delete-modal">
                <form id="deleteEmployeeForm">
                    <input type="hidden" name="delete_employee_id" id="delete_employee_id">

                    <div class="modal-body text-center p-4">
                        <div class="delete-icon-box">
                            <i class="bi bi-trash-fill"></i>
                        </div>
                        <h4 class="delete-title mt-3">Delete Employee</h4>
                        <div class="delete-image-wrap">
                            <img src="../assets/images/default-user.jpg" id="delete_employee_photo"
                                class="delete-user-image" alt="Employee">
                        </div>
                        <p class="delete-text mt-3">
                            Are you sure you want to delete
                            <strong id="delete_employee_name"></strong>?
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

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    window.USER_ROLE = "<?php echo $role; ?>";
    </script>
    <script src="../assets/js/auth.js"></script>
    <script src="../assets/js/employees.js"></script>
    <script src="../assets/js/darkmode.js"></script>
</body>

</html>