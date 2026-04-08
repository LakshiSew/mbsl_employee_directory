document.addEventListener("DOMContentLoaded", function () {
    const usersTableBody = document.getElementById("usersTableBody");
    const paginationArea = document.getElementById("paginationArea");
    const messageBox = document.getElementById("messageBox");

    const searchInput = document.getElementById("searchInput");
    const roleFilter = document.getElementById("roleFilter");
    const sortFilter = document.getElementById("sortFilter");
    const filterForm = document.getElementById("filterForm");

    const addUserForm = document.getElementById("addUserForm");
    const editUserForm = document.getElementById("editUserForm");
    const deleteUserForm = document.getElementById("deleteUserForm");

    const summaryTotalUsers = document.getElementById("summaryTotalUsers");
    const summaryAdminUsers = document.getElementById("summaryAdminUsers");
    const summaryStaffUsers = document.getElementById("summaryStaffUsers");

    const mobileMenuBtn = document.getElementById("mobileMenuBtn");
    const sidebar = document.getElementById("sidebar");
    const sidebarCloseBtn = document.getElementById("sidebarCloseBtn");
    const sidebarOverlay = document.getElementById("sidebarOverlay");

    let currentPage = 1;

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

    filterForm?.addEventListener("submit", function (e) {
        e.preventDefault();
        currentPage = 1;
        loadUsers();
    });

    async function loadUsers() {
        usersTableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4">Loading users...</td></tr>`;

        const params = new URLSearchParams({
            search: searchInput.value.trim(),
            role: roleFilter.value,
            sort: sortFilter.value,
            page: currentPage
        });

        try {
            const response = await fetch(`../api/users/index.php?${params.toString()}`);
            const result = await response.json();

            if (!result.status) {
                usersTableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">${result.message}</td></tr>`;
                return;
            }

            const users = result.data.users;
            const pagination = result.data.pagination;
            const summary = result.data.summary;

            summaryTotalUsers.textContent = summary.total_users;
            summaryAdminUsers.textContent = summary.admin_users;
            summaryStaffUsers.textContent = summary.staff_users;

            if (users.length === 0) {
                usersTableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4">No users found.</td></tr>`;
            } else {
                usersTableBody.innerHTML = "";

                users.forEach(user => {
                    const userImage = user.profile_picture ? user.profile_picture : "../assets/images/default-user.jpg";
                    const roleBadge = user.role === "admin"
                        ? `<span class="badge role-admin">Admin</span>`
                        : `<span class="badge role-user">Staff User</span>`;

                    const adminActions = window.USER_ROLE === "admin" ? `
                        <button type="button" class="btn action-btn edit-btn editUserBtn"
                            data-bs-toggle="modal" data-bs-target="#editUserModal"
                            data-id="${user.id}"
                            data-name="${escapeHtml(user.full_name)}"
                            data-email="${escapeHtml(user.email)}"
                            data-role="${escapeHtml(user.role)}"
                            data-image="${escapeHtml(userImage)}">
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        <button type="button" class="btn action-btn delete-btn deleteUserBtn"
                            data-bs-toggle="modal" data-bs-target="#deleteUserModal"
                            data-id="${user.id}"
                            data-name="${escapeHtml(user.full_name)}"
                            data-image="${escapeHtml(userImage)}">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    ` : "";

                    usersTableBody.innerHTML += `
                        <tr>
                            <td><img src="${userImage}" alt="User" class="table-user-image"></td>
                            <td>#${user.id}</td>
                            <td>${escapeHtml(user.full_name)}</td>
                            <td>${escapeHtml(user.email)}</td>
                            <td>${roleBadge}</td>
                            <td>${formatDate(user.created_at)}</td>
                            <td class="text-center action-cell">
                                <button type="button" class="btn action-btn view-btn viewUserBtn"
                                    data-bs-toggle="modal" data-bs-target="#viewUserModal"
                                    data-id="${user.id}"
                                    data-name="${escapeHtml(user.full_name)}"
                                    data-email="${escapeHtml(user.email)}"
                                    data-role="${escapeHtml(user.role)}"
                                    data-created="${formatDate(user.created_at)}"
                                    data-image="${escapeHtml(userImage)}">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                                ${adminActions}
                            </td>
                        </tr>
                    `;
                });
            }

            renderPagination(pagination.total_pages, pagination.current_page);
            bindDynamicButtons();
        } catch (error) {
            usersTableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">Failed to load users.</td></tr>`;
        }
    }

    function renderPagination(totalPages, current) {
        paginationArea.innerHTML = "";

        if (totalPages <= 1) return;

        for (let i = 1; i <= totalPages; i++) {
            paginationArea.innerHTML += `
                <li class="page-item ${i === current ? "active" : ""}">
                    <a href="#" class="page-link pagination-link" data-page="${i}">${i}</a>
                </li>
            `;
        }

        document.querySelectorAll(".pagination-link").forEach(link => {
            link.addEventListener("click", function (e) {
                e.preventDefault();
                currentPage = parseInt(this.dataset.page);
                loadUsers();
            });
        });
    }

    function bindDynamicButtons() {
        document.querySelectorAll(".viewUserBtn").forEach(button => {
            button.addEventListener("click", function () {
                document.getElementById("view_user_id").textContent = "#" + this.dataset.id;
                document.getElementById("view_user_name").textContent = this.dataset.name;
                document.getElementById("view_user_email").textContent = this.dataset.email;
                document.getElementById("view_user_role").textContent = this.dataset.role === "admin" ? "Admin" : "Staff User";
                document.getElementById("view_user_created").textContent = this.dataset.created;
                document.getElementById("view_user_image").src = this.dataset.image;
            });
        });

        document.querySelectorAll(".editUserBtn").forEach(button => {
            button.addEventListener("click", function () {
                document.getElementById("edit_user_id").value = this.dataset.id;
                document.getElementById("edit_full_name").value = this.dataset.name;
                document.getElementById("edit_email").value = this.dataset.email;
                document.getElementById("edit_role").value = this.dataset.role;
                document.getElementById("edit_password").value = "";
                document.getElementById("edit_confirm_password").value = "";
                document.getElementById("remove_current_image").checked = false;
                document.getElementById("edit_user_image_preview").src = this.dataset.image;
            });
        });

        document.querySelectorAll(".deleteUserBtn").forEach(button => {
            button.addEventListener("click", function () {
                document.getElementById("delete_user_id").value = this.dataset.id;
                document.getElementById("delete_user_name").textContent = this.dataset.name;
                document.getElementById("delete_user_image").src = this.dataset.image;
            });
        });
    }

    addUserForm?.addEventListener("submit", async function (e) {
        e.preventDefault();
        clearMessage();

        const formData = new FormData(addUserForm);

        try {
            const response = await fetch("../api/users/index.php", {
                method: "POST",
                body: formData
            });

            const result = await response.json();

            if (result.status) {
                showMessage(result.message, "success");
                addUserForm.reset();

                const modal = bootstrap.Modal.getInstance(document.getElementById("addUserModal"));
                modal.hide();

                loadUsers();
            } else {
                showMessage(result.message, "danger");
            }
        } catch (error) {
            showMessage("Something went wrong. Please try again.", "danger");
        }
    });

    editUserForm?.addEventListener("submit", async function (e) {
        e.preventDefault();
        clearMessage();

        const formData = new FormData(editUserForm);
        formData.append("_method", "PUT");

        try {
            const response = await fetch("../api/users/index.php", {
                method: "POST",
                body: formData
            });

            const result = await response.json();

            if (result.status) {
                showMessage(result.message, "success");

                const modal = bootstrap.Modal.getInstance(document.getElementById("editUserModal"));
                modal.hide();

                loadUsers();
            } else {
                showMessage(result.message, "danger");
            }
        } catch (error) {
            showMessage("Something went wrong. Please try again.", "danger");
        }
    });

    deleteUserForm?.addEventListener("submit", async function (e) {
        e.preventDefault();
        clearMessage();

        const formData = new FormData(deleteUserForm);
        formData.append("_method", "DELETE");

        try {
            const response = await fetch("../api/users/index.php", {
                method: "POST",
                body: formData
            });

            const result = await response.json();

            if (result.status) {
                showMessage(result.message, "success");

                const modal = bootstrap.Modal.getInstance(document.getElementById("deleteUserModal"));
                modal.hide();

                loadUsers();
            } else {
                showMessage(result.message, "danger");
            }
        } catch (error) {
            showMessage("Something went wrong. Please try again.", "danger");
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
        const date = new Date(dateString);
        if (isNaN(date)) return "-";
        return date.toISOString().split("T")[0];
    }

    function escapeHtml(text) {
        if (!text) return "";
        return text
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");
    }

    loadUsers();
});