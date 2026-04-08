document.addEventListener("DOMContentLoaded", async function () {
    const totalUsers = document.getElementById("totalUsers");
    const totalEmployees = document.getElementById("totalEmployees");
    const activeEmployees = document.getElementById("activeEmployees");
    const totalDepartments = document.getElementById("totalDepartments");
    const recentEmployeesTable = document.getElementById("recentEmployeesTable");

    const mobileMenuBtn = document.getElementById("mobileMenuBtn");
    const sidebar = document.getElementById("sidebar");
    const sidebarCloseBtn = document.getElementById("sidebarCloseBtn");
    const sidebarOverlay = document.getElementById("sidebarOverlay");

    const chartCanvas = document.getElementById("employeeStatusChart");

    let employeeStatusChart = null;

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

    try {
        const response = await fetch("../api/dashboard/stats.php");
        const result = await response.json();

        if (result.status) {
            totalUsers.textContent = result.data.total_users;
            totalEmployees.textContent = result.data.total_employees;
            activeEmployees.textContent = result.data.active_employees;
            totalDepartments.textContent = result.data.total_departments;

            if (chartCanvas) {
                if (employeeStatusChart) {
                    employeeStatusChart.destroy();
                }

                employeeStatusChart = new Chart(chartCanvas, {
                    type: "bar",
                    data: {
                        labels: ["Active", "Inactive", "On Leave"],
                        datasets: [{
                            label: "Employees",
                            data: [
                                result.data.active_employees,
                                result.data.inactive_employees,
                                result.data.on_leave_employees
                            ],
                            backgroundColor: [
                                "rgba(16, 185, 129, 0.75)",
                                "rgba(239, 68, 68, 0.75)",
                                "rgba(245, 158, 11, 0.75)"
                            ],
                            borderColor: [
                                "rgba(16, 185, 129, 1)",
                                "rgba(239, 68, 68, 1)",
                                "rgba(245, 158, 11, 1)"
                            ],
                            borderWidth: 1,
                            borderRadius: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }

            if (result.data.recent_employees.length > 0) {
                recentEmployeesTable.innerHTML = "";

                result.data.recent_employees.forEach(employee => {
                    let badgeClass = "bg-secondary";

                    if (employee.status === "Active") {
                        badgeClass = "bg-success";
                    } else if (employee.status === "Inactive") {
                        badgeClass = "bg-danger";
                    } else if (employee.status === "On Leave") {
                        badgeClass = "bg-warning text-dark";
                    }

                    recentEmployeesTable.innerHTML += `
                        <tr>
                            <td>${employee.employee_code}</td>
                            <td>${employee.full_name}</td>
                            <td>${employee.department}</td>
                            <td>${employee.designation}</td>
                            <td><span class="badge ${badgeClass}">${employee.status}</span></td>
                            <td>${employee.join_date}</td>
                        </tr>
                    `;
                });
            } else {
                recentEmployeesTable.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4">No employee records found.</td>
                    </tr>
                `;
            }
        } else {
            recentEmployeesTable.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger py-4">${result.message}</td>
                </tr>
            `;
        }
    } catch (error) {
        recentEmployeesTable.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-danger py-4">Failed to load dashboard data.</td>
            </tr>
        `;
    }
});