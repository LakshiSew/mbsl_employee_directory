document.addEventListener("DOMContentLoaded", function () {
    const employeesTableBody = document.getElementById("employeesTableBody");
    const employeePaginationArea = document.getElementById("employeePaginationArea");
    const messageBox = document.getElementById("messageBox");

    const employeeSearchInput = document.getElementById("employeeSearchInput");
    const departmentFilter = document.getElementById("departmentFilter");
    const statusFilter = document.getElementById("statusFilter");
    const employeeSortFilter = document.getElementById("employeeSortFilter");
    const employeeFilterForm = document.getElementById("employeeFilterForm");

    const addEmployeeForm = document.getElementById("addEmployeeForm");
    const editEmployeeForm = document.getElementById("editEmployeeForm");
    const deleteEmployeeForm = document.getElementById("deleteEmployeeForm");

    const summaryTotalEmployees = document.getElementById("summaryTotalEmployees");
    const summaryActiveEmployees = document.getElementById("summaryActiveEmployees");
    const summaryInactiveEmployees = document.getElementById("summaryInactiveEmployees");
    const summaryOnLeaveEmployees = document.getElementById("summaryOnLeaveEmployees");

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

    employeeFilterForm?.addEventListener("submit", function (e) {
        e.preventDefault();
        currentPage = 1;
        loadEmployees();
    });

    async function loadEmployees() {
        employeesTableBody.innerHTML = `<tr><td colspan="8" class="text-center py-4">Loading employees...</td></tr>`;

        const params = new URLSearchParams({
            search: employeeSearchInput.value.trim(),
            department: departmentFilter.value,
            status: statusFilter.value,
            sort: employeeSortFilter.value,
            page: currentPage
        });

        try {
            const response = await fetch(`../api/employees/index.php?${params.toString()}`);
            const result = await response.json();

            if (!result.status) {
                employeesTableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4">${result.message}</td></tr>`;
                return;
            }

            const employees = result.data.employees;
            const pagination = result.data.pagination;
            const summary = result.data.summary;
            const departments = result.data.departments;

            summaryTotalEmployees.textContent = summary.total_employees;
            summaryActiveEmployees.textContent = summary.active;
            summaryInactiveEmployees.textContent = summary.inactive;
            summaryOnLeaveEmployees.textContent = summary.on_leave;

            const currentDept = departmentFilter.value;
            departmentFilter.innerHTML = `<option value="">All Departments</option>`;
            departments.forEach(dept => {
                departmentFilter.innerHTML += `<option value="${escapeHtml(dept)}">${escapeHtml(dept)}</option>`;
            });
            departmentFilter.value = currentDept;

            if (employees.length === 0) {
                employeesTableBody.innerHTML = `<tr><td colspan="8" class="text-center py-4">No employees found.</td></tr>`;
            } else {
                employeesTableBody.innerHTML = "";

                employees.forEach(employee => {
                    const employeePhoto = employee.photo ? employee.photo : "../assets/images/default-user.jpg";
                    let statusClass = "status-gray";

                    if (employee.status === "Active") statusClass = "status-green";
                    else if (employee.status === "Inactive") statusClass = "status-red";
                    else if (employee.status === "On Leave") statusClass = "status-orange";

                    const adminActions = window.USER_ROLE === "admin" ? `
                        <button type="button" class="btn action-btn edit-btn editEmployeeBtn"
                            data-bs-toggle="modal" data-bs-target="#editEmployeeModal"
                            data-id="${employee.id}"
                            data-code="${escapeHtml(employee.employee_code)}"
                            data-name="${escapeHtml(employee.full_name)}"
                            data-email="${escapeHtml(employee.email)}"
                            data-phone="${escapeHtml(employee.phone)}"
                            data-department="${escapeHtml(employee.department)}"
                            data-designation="${escapeHtml(employee.designation)}"
                            data-gender="${escapeHtml(employee.gender)}"
                            data-join-date="${escapeHtml(employee.join_date)}"
                            data-status="${escapeHtml(employee.status)}"
                            data-address="${escapeHtml(employee.address || '')}"
                            data-photo="${escapeHtml(employeePhoto)}">
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        <button type="button" class="btn action-btn delete-btn deleteEmployeeBtn"
                            data-bs-toggle="modal" data-bs-target="#deleteEmployeeModal"
                            data-id="${employee.id}"
                            data-name="${escapeHtml(employee.full_name)}"
                            data-photo="${escapeHtml(employeePhoto)}">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    ` : "";

                    employeesTableBody.innerHTML += `
                        <tr>
                            <td><img src="${employeePhoto}" alt="Employee" class="table-employee-image"></td>
                            <td>${escapeHtml(employee.employee_code)}</td>
                            <td>${escapeHtml(employee.full_name)}</td>
                            <td>${escapeHtml(employee.department)}</td>
                            <td>${escapeHtml(employee.designation)}</td>
                            <td><span class="badge ${statusClass}">${escapeHtml(employee.status)}</span></td>
                            <td>${escapeHtml(employee.join_date)}</td>
                            <td class="text-center action-cell">
                                <button type="button" class="btn action-btn view-btn viewEmployeeBtn"
                                    data-bs-toggle="modal" data-bs-target="#viewEmployeeModal"
                                    data-id="${employee.id}"
                                    data-code="${escapeHtml(employee.employee_code)}"
                                    data-name="${escapeHtml(employee.full_name)}"
                                    data-email="${escapeHtml(employee.email)}"
                                    data-phone="${escapeHtml(employee.phone)}"
                                    data-department="${escapeHtml(employee.department)}"
                                    data-designation="${escapeHtml(employee.designation)}"
                                    data-gender="${escapeHtml(employee.gender)}"
                                    data-join-date="${escapeHtml(employee.join_date)}"
                                    data-status="${escapeHtml(employee.status)}"
                                    data-address="${escapeHtml(employee.address || '')}"
                                    data-photo="${escapeHtml(employeePhoto)}">
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
            employeesTableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4">Failed to load employees.</td></tr>`;
        }
    }

    function renderPagination(totalPages, current) {
        employeePaginationArea.innerHTML = "";
        if (totalPages <= 1) return;

        for (let i = 1; i <= totalPages; i++) {
            employeePaginationArea.innerHTML += `
                <li class="page-item ${i === current ? "active" : ""}">
                    <a href="#" class="page-link employee-pagination-link" data-page="${i}">${i}</a>
                </li>
            `;
        }

        document.querySelectorAll(".employee-pagination-link").forEach(link => {
            link.addEventListener("click", function (e) {
                e.preventDefault();
                currentPage = parseInt(this.dataset.page);
                loadEmployees();
            });
        });
    }

    function bindDynamicButtons() {
        document.querySelectorAll(".viewEmployeeBtn").forEach(button => {
            button.addEventListener("click", function () {
                document.getElementById("view_employee_photo").src = this.dataset.photo;
                document.getElementById("view_employee_code").textContent = this.dataset.code;
                document.getElementById("view_employee_name").textContent = this.dataset.name;
                document.getElementById("view_employee_email").textContent = this.dataset.email;
                document.getElementById("view_employee_phone").textContent = this.dataset.phone;
                document.getElementById("view_employee_department").textContent = this.dataset.department;
                document.getElementById("view_employee_designation").textContent = this.dataset.designation;
                document.getElementById("view_employee_gender").textContent = this.dataset.gender;
                document.getElementById("view_employee_join_date").textContent = this.dataset.joinDate;
                document.getElementById("view_employee_status").textContent = this.dataset.status;
                document.getElementById("view_employee_address").textContent = this.dataset.address;
            });
        });

        document.querySelectorAll(".editEmployeeBtn").forEach(button => {
            button.addEventListener("click", function () {
                document.getElementById("edit_employee_id").value = this.dataset.id;
                document.getElementById("edit_employee_code").value = this.dataset.code;
                document.getElementById("edit_full_name").value = this.dataset.name;
                document.getElementById("edit_email").value = this.dataset.email;
                document.getElementById("edit_phone").value = this.dataset.phone;
                document.getElementById("edit_department").value = this.dataset.department;
                document.getElementById("edit_designation").value = this.dataset.designation;
                document.getElementById("edit_gender").value = this.dataset.gender;
                document.getElementById("edit_join_date").value = this.dataset.joinDate;
                document.getElementById("edit_status").value = this.dataset.status;
                document.getElementById("edit_address").value = this.dataset.address;
                document.getElementById("edit_employee_photo_preview").src = this.dataset.photo;
                document.getElementById("remove_current_photo").checked = false;
            });
        });

        document.querySelectorAll(".deleteEmployeeBtn").forEach(button => {
            button.addEventListener("click", function () {
                document.getElementById("delete_employee_id").value = this.dataset.id;
                document.getElementById("delete_employee_name").textContent = this.dataset.name;
                document.getElementById("delete_employee_photo").src = this.dataset.photo;
            });
        });
    }

    addEmployeeForm?.addEventListener("submit", async function (e) {
        e.preventDefault();
        clearMessage();

        const formData = new FormData(addEmployeeForm);

        try {
            const response = await fetch("../api/employees/index.php", {
                method: "POST",
                body: formData
            });

            const result = await response.json();

            if (result.status) {
                showMessage(result.message, "success");
                addEmployeeForm.reset();

                const modal = bootstrap.Modal.getInstance(document.getElementById("addEmployeeModal"));
                modal.hide();

                loadEmployees();
            } else {
                showMessage(result.message, "danger");
            }
        } catch (error) {
            showMessage("Something went wrong. Please try again.", "danger");
        }
    });

    editEmployeeForm?.addEventListener("submit", async function (e) {
        e.preventDefault();
        clearMessage();

        const formData = new FormData(editEmployeeForm);
        formData.append("_method", "PUT");

        try {
            const response = await fetch("../api/employees/index.php", {
                method: "POST",
                body: formData
            });

            const result = await response.json();

            if (result.status) {
                showMessage(result.message, "success");

                const modal = bootstrap.Modal.getInstance(document.getElementById("editEmployeeModal"));
                modal.hide();

                loadEmployees();
            } else {
                showMessage(result.message, "danger");
            }
        } catch (error) {
            showMessage("Something went wrong. Please try again.", "danger");
        }
    });

    deleteEmployeeForm?.addEventListener("submit", async function (e) {
        e.preventDefault();
        clearMessage();

        const formData = new FormData(deleteEmployeeForm);
        formData.append("_method", "DELETE");

        try {
            const response = await fetch("../api/employees/index.php", {
                method: "POST",
                body: formData
            });

            const result = await response.json();

            if (result.status) {
                showMessage(result.message, "success");

                const modal = bootstrap.Modal.getInstance(document.getElementById("deleteEmployeeModal"));
                modal.hide();

                loadEmployees();
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

    function escapeHtml(text) {
        if (!text) return "";
        return text
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");
    }

    loadEmployees();
});