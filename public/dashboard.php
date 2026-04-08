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
    <title>Dashboard | MBSL Employee Directory</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
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
                        <li>
                            <a href="dashboard.php" class="active">
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li>
                            <a href="users.php">
                                <i class="bi bi-people-fill"></i>
                                <span>Users Management</span>
                            </a>
                        </li>

                        <li>
                            <a href="employees.php">
                                <i class="bi bi-person-vcard-fill"></i>
                                <span>Employees Management</span>
                            </a>
                        </li>

                        <li>
                            <a href="profile.php">
                                <i class="bi bi-person-circle"></i>
                                <span>My Profile</span>
                            </a>
                        </li>
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
                        <h2 class="page-title">Dashboard Overview</h2>
                        <p class="page-subtitle">Welcome back, <?php echo htmlspecialchars($full_name); ?> 👋</p>
                    </div>
                </div>

                <div class="topbar-right">
                    <span class="role-badge">
                        <i class="bi bi-person-badge-fill me-1"></i>
                        <?php echo ucfirst(htmlspecialchars($role)); ?>
                    </span>
                </div>
            </div>

            <div class="welcome-banner">
                <div class="welcome-text">
                    <h3>Hello, <?php echo htmlspecialchars($full_name); ?>!</h3>
                    <p>
                        Welcome to the MBSL Employee Directory dashboard. Here you can manage users,
                        employees, and view important staff statistics quickly and easily.
                    </p>
                </div>
                <div class="welcome-icon">
                    <i class="bi bi-bar-chart-line-fill"></i>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card purple-card">
                        <div class="stat-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="stat-info">
                            <h6>Total Users</h6>
                            <h3 id="totalUsers">0</h3>
                            <span class="stat-badge">System Accounts</span>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stat-card blue-card">
                        <div class="stat-icon">
                            <i class="bi bi-person-vcard-fill"></i>
                        </div>
                        <div class="stat-info">
                            <h6>Total Employees</h6>
                            <h3 id="totalEmployees">0</h3>
                            <span class="stat-badge">Company Records</span>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stat-card green-card">
                        <div class="stat-icon">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <div class="stat-info">
                            <h6>Active Employees</h6>
                            <h3 id="activeEmployees">0</h3>
                            <span class="stat-badge">Currently Active</span>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stat-card orange-card">
                        <div class="stat-icon">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <div class="stat-info">
                            <h6>Total Departments</h6>
                            <h3 id="totalDepartments">0</h3>
                            <span class="stat-badge">Distinct Units</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-card mb-4">
                <div class="card-header-custom">
                    <div>
                        <h4>Employee Status Overview</h4>
                        <p>Visual summary of employee status distribution</p>
                    </div>
                </div>

                <div class="chart-wrapper">
                    <canvas id="employeeStatusChart"></canvas>
                </div>
            </div>

            <div class="content-card">
                <div class="card-header-custom">
                    <div>
                        <h4>Recent Employees</h4>
                        <p>Latest employee records added to the system</p>
                    </div>
                    <a href="employees.php" class="btn view-all-btn">
                        <i class="bi bi-arrow-right-circle me-2"></i>View All
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle custom-table mb-0">
                        <thead>
                            <tr>
                                <th>Employee Code</th>
                                <th>Full Name</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Status</th>
                                <th>Join Date</th>
                            </tr>
                        </thead>
                        <tbody id="recentEmployeesTable">
                            <tr>
                                <td colspan="6" class="text-center py-4">Loading dashboard data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/auth.js"></script>
    <script src="../assets/js/dashboard.js"></script>
</body>

</html>