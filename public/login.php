<?php
require_once "../app/helpers/Session.php";

Session::start();

if (Session::has("user_id")) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | MBSL Employee Directory</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body>

    <div class="login-page">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-xl-9 col-lg-10">
                    <div class="login-card">

                        <div class="row g-0">
                            <div class="col-lg-5 login-left-panel">
                                <div class="left-panel-content">
                                    <div class="brand-icon">
                                        <i class="bi bi-buildings-fill"></i>
                                    </div>
                                    <h2 class="brand-title">Welcome Back</h2>
                                    <p class="brand-text">
                                        Login to access your employee directory dashboard and manage company staff
                                        records easily and securely.
                                    </p>

                                    <div class="feature-box">
                                        <div class="feature-item">
                                            <i class="bi bi-shield-lock-fill"></i>
                                            <span>Secure authentication system</span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="bi bi-bar-chart-fill"></i>
                                            <span>Dashboard and employee insights</span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="bi bi-person-workspace"></i>
                                            <span>Admin and staff access support</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-7 login-right-panel">
                                <div class="form-area">
                                    <div class="mb-4">
                                        <h3 class="form-title">Login to Your Account</h3>
                                        <p class="form-subtitle">Enter your email and password to continue</p>
                                    </div>

                                    <div id="messageBox"></div>

                                    <form id="loginForm" novalidate>
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="email" class="form-label">Email Address</label>
                                                <div class="input-group custom-input-group">
                                                    <span class="input-group-text">
                                                        <i class="bi bi-envelope-fill"></i>
                                                    </span>
                                                    <input type="email" class="form-control" id="email" name="email"
                                                        placeholder="Enter your email" required>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mb-3">
                                                <label for="password" class="form-label">Password</label>
                                                <div class="input-group custom-input-group">
                                                    <span class="input-group-text">
                                                        <i class="bi bi-lock-fill"></i>
                                                    </span>
                                                    <input type="password" class="form-control" id="password"
                                                        name="password" placeholder="Enter your password" required>
                                                    <button type="button" class="btn toggle-password"
                                                        data-target="password">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mb-4">
                                                <div class="login-note">
                                                    <i class="bi bi-info-circle-fill me-2"></i>
                                                    Admin and Staff users can login from this same page. Access will be
                                                    controlled by the stored role.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-grid mb-3">
                                            <button type="submit" class="btn login-btn" id="loginBtn">
                                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                                            </button>
                                        </div>

                                        <div class="text-center">
                                            <p class="bottom-text mb-0">
                                                Don’t have an account?
                                                <a href="register.php" class="register-link">Register here</a>
                                            </p>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/auth.js"></script>
</body>

</html>