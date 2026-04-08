document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("loginForm");
    const registerForm = document.getElementById("registerForm");
    const messageBox = document.getElementById("messageBox");
    const loginBtn = document.getElementById("loginBtn");
    const registerBtn = document.getElementById("registerBtn");
    const logoutBtn = document.getElementById("logoutBtn");

    const toggleButtons = document.querySelectorAll(".toggle-password");

    toggleButtons.forEach(button => {
        button.addEventListener("click", function () {
            const targetId = this.getAttribute("data-target");
            const input = document.getElementById(targetId);
            const icon = this.querySelector("i");

            if (input) {
                if (input.type === "password") {
                    input.type = "text";
                    icon.classList.remove("bi-eye-fill");
                    icon.classList.add("bi-eye-slash-fill");
                } else {
                    input.type = "password";
                    icon.classList.remove("bi-eye-slash-fill");
                    icon.classList.add("bi-eye-fill");
                }
            }
        });
    });

    if (loginForm) {
        loginForm.addEventListener("submit", async function (e) {
            e.preventDefault();

            clearMessage();

            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value.trim();

            if (email === "" || password === "") {
                showMessage("Please fill in all fields.", "danger");
                return;
            }

            if (loginBtn) {
                loginBtn.disabled = true;
                loginBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Logging in...`;
            }

            const formData = new FormData();
            formData.append("email", email);
            formData.append("password", password);

            try {
                const response = await fetch("../api/auth/login.php", {
                    method: "POST",
                    body: formData
                });

                const result = await response.json();

                if (result.status) {
                    showMessage(result.message, "success");

                    setTimeout(() => {
                        window.location.href = "dashboard.php";
                    }, 1000);
                } else {
                    showMessage(result.message, "danger");
                }
            } catch (error) {
                showMessage("Something went wrong. Please try again.", "danger");
            } finally {
                if (loginBtn) {
                    loginBtn.disabled = false;
                    loginBtn.innerHTML = `<i class="bi bi-box-arrow-in-right me-2"></i>Login`;
                }
            }
        });
    }

    if (registerForm) {
        registerForm.addEventListener("submit", async function (e) {
            e.preventDefault();

            clearMessage();

            const full_name = document.getElementById("full_name").value.trim();
            const email = document.getElementById("email").value.trim();
            const role = document.getElementById("role").value;
            const password = document.getElementById("password").value.trim();
            const confirm_password = document.getElementById("confirm_password").value.trim();
            const profile_picture = document.getElementById("profile_picture").files[0];

            if (full_name === "" || email === "" || password === "" || confirm_password === "") {
                showMessage("All fields are required.", "danger");
                return;
            }

            if (password !== confirm_password) {
                showMessage("Password and Confirm Password do not match.", "danger");
                return;
            }

            if (registerBtn) {
                registerBtn.disabled = true;
                registerBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Creating...`;
            }

            const formData = new FormData();
            formData.append("full_name", full_name);
            formData.append("email", email);
            formData.append("role", role);
            formData.append("password", password);
            formData.append("confirm_password", confirm_password);

            if (profile_picture) {
                formData.append("profile_picture", profile_picture);
            }

            try {
                const response = await fetch("../api/auth/register.php", {
                    method: "POST",
                    body: formData
                });

                const result = await response.json();

                if (result.status) {
                    showMessage(result.message, "success");
                    registerForm.reset();

                    setTimeout(() => {
                        window.location.href = "login.php";
                    }, 1200);
                } else {
                    showMessage(result.message, "danger");
                }
            } catch (error) {
                showMessage("Something went wrong. Please try again.", "danger");
            } finally {
                if (registerBtn) {
                    registerBtn.disabled = false;
                    registerBtn.innerHTML = `<i class="bi bi-person-plus-fill me-2"></i>Create Account`;
                }
            }
        });
    }

    if (logoutBtn) {
        logoutBtn.addEventListener("click", async function () {
            if (!confirm("Are you sure you want to logout?")) return;

            try {
                const response = await fetch("../api/auth/logout.php", {
                    method: "POST"
                });

                const result = await response.json();

                if (result.status) {
                    window.location.href = "login.php";
                } else {
                    alert("Logout failed");
                }
            } catch (error) {
                alert("Something went wrong");
            }
        });
    }

    function showMessage(message, type) {
        if (!messageBox) return;

        messageBox.innerHTML = `
            <div class="alert alert-${type} custom-alert">
                <i class="bi ${type === "success" ? "bi-check-circle-fill" : "bi-exclamation-triangle-fill"} me-2"></i>
                ${message}
            </div>
        `;
    }

    function clearMessage() {
        if (messageBox) {
            messageBox.innerHTML = "";
        }
    }
});