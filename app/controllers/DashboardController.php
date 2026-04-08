<?php

require_once __DIR__ . "/../helpers/Response.php";

class DashboardController
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getStats()
    {
        $total_users = 0;
        $total_employees = 0;
        $active_employees = 0;
        $inactive_employees = 0;
        $on_leave_employees = 0;
        $total_departments = 0;
        $recent_employees = [];

        $user_query = "SELECT COUNT(*) AS total FROM users";
        $user_result = mysqli_query($this->conn, $user_query);
        if ($user_result) {
            $user_data = mysqli_fetch_assoc($user_result);
            $total_users = $user_data["total"];
        }

        $employee_query = "SELECT COUNT(*) AS total FROM employees";
        $employee_result = mysqli_query($this->conn, $employee_query);
        if ($employee_result) {
            $employee_data = mysqli_fetch_assoc($employee_result);
            $total_employees = $employee_data["total"];
        }

        $active_query = "SELECT COUNT(*) AS total FROM employees WHERE status = 'Active'";
        $active_result = mysqli_query($this->conn, $active_query);
        if ($active_result) {
            $active_data = mysqli_fetch_assoc($active_result);
            $active_employees = $active_data["total"];
        }

        $inactive_query = "SELECT COUNT(*) AS total FROM employees WHERE status = 'Inactive'";
        $inactive_result = mysqli_query($this->conn, $inactive_query);
        if ($inactive_result) {
            $inactive_data = mysqli_fetch_assoc($inactive_result);
            $inactive_employees = $inactive_data["total"];
        }

        $leave_query = "SELECT COUNT(*) AS total FROM employees WHERE status = 'On Leave'";
        $leave_result = mysqli_query($this->conn, $leave_query);
        if ($leave_result) {
            $leave_data = mysqli_fetch_assoc($leave_result);
            $on_leave_employees = $leave_data["total"];
        }

        $department_query = "SELECT COUNT(DISTINCT department) AS total FROM employees";
        $department_result = mysqli_query($this->conn, $department_query);
        if ($department_result) {
            $department_data = mysqli_fetch_assoc($department_result);
            $total_departments = $department_data["total"];
        }

        $recent_query = "SELECT employee_code, full_name, department, designation, status, join_date
                         FROM employees
                         ORDER BY created_at DESC
                         LIMIT 5";
        $recent_result = mysqli_query($this->conn, $recent_query);

        if ($recent_result) {
            while ($row = mysqli_fetch_assoc($recent_result)) {
                $recent_employees[] = $row;
            }
        }

        Response::success("Dashboard data fetched successfully", [
            "total_users" => $total_users,
            "total_employees" => $total_employees,
            "active_employees" => $active_employees,
            "inactive_employees" => $inactive_employees,
            "on_leave_employees" => $on_leave_employees,
            "total_departments" => $total_departments,
            "recent_employees" => $recent_employees
        ]);
    }
}