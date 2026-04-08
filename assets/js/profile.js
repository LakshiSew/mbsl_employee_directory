document.addEventListener("DOMContentLoaded", function () {
    const profileForm = document.getElementById("profileForm");
    const messageBox = document.getElementById("messageBox");
    const profileUpdateBtn = document.getElementById("profileUpdateBtn");

    const profileName = document.getElementById("profileName");
    const profileEmail = document.getElementById("profileEmail");
    const profileRole = document.getElementById("profileRole");
    const profileCreatedAt = document.getElementById("profileCreatedAt");
    const profileImagePreview = document.getElementById("profileImagePreview");

    const mobileMenuBtn = document.getElementById("mobileMenuBtn");
    const sidebar = document.getElementById("sidebar");
    const sidebarCloseBtn = document.getElementById("sidebarCloseBtn");
    const sidebarOverlay = document.getElementById("sidebarOverlay");

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener("click", function () {
            sidebar.classList.add("show-sidebar");
            sidebarOverlay.classList.add("show-overlay");
        });
    }

    if (sidebarCloseBtn) {
        sidebarCloseBtn.addEventListener("click", function () {
            sidebar.classList.remove("show-sidebar");
            sidebarOverlay.classList.remove("show-overlay");
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener("click", function () {
            sidebar.classList.remove("show-sidebar");
            sidebarOverlay.classList.remove("show-overlay");
        });
    }

    async function loadProfile() {
        try {
            const response = await fetch("../api/profile/index.php");
            const result = await response.json();

            if (result.status) {
                const user = result.data.user;

                document.getElementById("full_name").value = user.full_name || "";
                document.getElementById("email").value = user.email || "";

                profileName.textContent = user.full_name || "-";
                profileEmail.textContent = user.email || "-";
                profileRole.textContent = user.role === "admin" ? "Admin" : "Staff User";
                profileCreatedAt.textContent = formatDate(user.created_at);

                profileImagePreview.src = user.profile_picture && user.profile_picture.trim() !== ""
                    ? user.profile_picture
                    : "../assets/images/default-user.jpg";
            } else {
                showMessage(result.message, "danger");
            }
        } catch (error) {
            showMessage("Failed to load profile data.", "danger");
        }
    }

    profileForm?.addEventListener("submit", async function (e) {
        e.preventDefault();
        clearMessage();

        const formData = new FormData(profileForm);
        formData.append("_method", "PUT");

        profileUpdateBtn.disabled = true;
        profileUpdateBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Updating...`;

        try {
            const response = await fetch("../api/profile/index.php", {
                method: "POST",
                body: formData
            });

            const result = await response.json();

            if (result.status) {
                showMessage(result.message, "success");
                document.getElementById("password").value = "";
                document.getElementById("confirm_password").value = "";
                loadProfile();
            } else {
                showMessage(result.message, "danger");
            }
        } catch (error) {
            showMessage("Something went wrong. Please try again.", "danger");
        } finally {
            profileUpdateBtn.disabled = false;
            profileUpdateBtn.innerHTML = `<i class="bi bi-check2-circle me-2"></i>Update Profile`;
        }
    });

    function showMessage(message, type) {
        if (!messageBox) return;

        messageBox.innerHTML = `
            <div class="alert alert-${type} custom-alert">
                <i class="bi ${type === "success" ? "bi-check-circle-fill" : "bi-exclamation-triangle-fill"} me-2"></i>
                ${message}
            </div>
        `;

        setTimeout(() => {
            messageBox.innerHTML = "";
        }, 3000);
    }

    function clearMessage() {
        if (messageBox) {
            messageBox.innerHTML = "";
        }
    }

    function formatDate(dateString) {
        if (!dateString) return "-";
        const date = new Date(dateString);
        if (isNaN(date)) return "-";
        return date.toISOString().split("T")[0];
    }

    loadProfile();
});