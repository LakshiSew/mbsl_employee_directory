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
    <title>Register | MBSL Employee Directory</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/register.css">
</head>

<body>

    <div class="register-page">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-xl-10 col-lg-11">
                    <div class="register-card">

                        <div class="row g-0">
                            <div class="col-lg-5 register-left-panel">
                                <div class="left-panel-content">
                                    <div class="brand-icon">
                                        <i class="bi bi-buildings-fill"></i>
                                    </div>
                                    <h2 class="brand-title">MBSL Employee Directory</h2>
                                    <p class="brand-text">
                                        Create your account to manage employee records, departments, and company staff
                                        details in one secure system.
                                    </p>

                                    <div class="feature-box">
                                        <div class="feature-item">
                                            <i class="bi bi-person-check-fill"></i>
                                            <span>Secure user registration</span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="bi bi-grid-fill"></i>
                                            <span>Clean dashboard access</span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="bi bi-people-fill"></i>
                                            <span>Easy employee management</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-7 register-right-panel">
                                <div class="form-area">
                                    <div class="mb-4">
                                        <h3 class="form-title">Create Account</h3>
                                        <p class="form-subtitle">Fill in your details to get started</p>
                                    </div>

                                    <div id="messageBox"></div>

                                    <form id="registerForm" novalidate enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="full_name" class="form-label">Full Name</label>
                                                <div class="input-group custom-input-group">
                                                    <span class="input-group-text"><i
                                                            class="bi bi-person-fill"></i></span>
                                                    <input type="text" class="form-control" id="full_name"
                                                        name="full_name" placeholder="Enter full name" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="email" class="form-label">Email Address</label>
                                                <div class="input-group custom-input-group">
                                                    <span class="input-group-text"><i
                                                            class="bi bi-envelope-fill"></i></span>
                                                    <input type="email" class="form-control" id="email" name="email"
                                                        placeholder="Enter email" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="role" class="form-label">Role</label>
                                                <div class="input-group custom-input-group">
                                                    <span class="input-group-text"><i
                                                            class="bi bi-person-badge-fill"></i></span>
                                                    <select class="form-select" id="role" name="role" required>
                                                        <option value="user">Staff User</option>
                                                        <option value="admin">Admin</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="profile_picture" class="form-label">Profile Image</label>
                                                <div class="file-upload-box">
                                                    <input type="file" class="form-control custom-file-input"
                                                        id="profile_picture" name="profile_picture" accept="image/*">
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="password" class="form-label">Password</label>
                                                <div class="input-group custom-input-group">
                                                    <span class="input-group-text"><i
                                                            class="bi bi-lock-fill"></i></span>
                                                    <input type="password" class="form-control" id="password"
                                                        name="password" placeholder="Enter password" required>
                                                    <button type="button" class="btn toggle-password"
                                                        data-target="password">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-4">
                                                <label for="confirm_password" class="form-label">Confirm
                                                    Password</label>
                                                <div class="input-group custom-input-group">
                                                    <span class="input-group-text"><i
                                                            class="bi bi-shield-lock-fill"></i></span>
                                                    <input type="password" class="form-control" id="confirm_password"
                                                        name="confirm_password" placeholder="Confirm password" required>
                                                    <button type="button" class="btn toggle-password"
                                                        data-target="confirm_password">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="col-12 mb-4">
                                                <div class="role-note w-100">
                                                    <i class="bi bi-info-circle-fill me-2"></i>
                                                    Create Admin or Staff accounts and optionally upload a profile
                                                    image.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-grid mb-3">
                                            <button type="submit" class="btn register-btn" id="registerBtn">
                                                <i class="bi bi-person-plus-fill me-2"></i>Create Account
                                            </button>
                                        </div>

                                        <div class="text-center">
                                            <p class="bottom-text mb-0">
                                                Already have an account?
                                                <a href="login.php" class="login-link">Login here</a>
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