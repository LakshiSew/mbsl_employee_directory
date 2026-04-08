<?php

require_once __DIR__ . "/../models/Employee.php";
require_once __DIR__ . "/../helpers/Response.php";
require_once __DIR__ . "/../helpers/ImageUpload.php";
require_once __DIR__ . "/../helpers/Session.php";

class EmployeeController
{
    private $employeeModel;

    public function __construct($db)
    {
        $this->employeeModel = new Employee($db);
    }

    public function index()
    {
        $search = trim($_GET["search"] ?? "");
        $department = trim($_GET["department"] ?? "");
        $status = trim($_GET["status"] ?? "");
        $sort = trim($_GET["sort"] ?? "id_desc");
        $page = isset($_GET["page"]) ? (int) $_GET["page"] : 1;
        $limit = 5;

        if ($page < 1) $page = 1;

        $offset = ($page - 1) * $limit;

        $totalEmployees = $this->employeeModel->countAll($search, $department, $status);
        $employees = $this->employeeModel->getAll($search, $department, $status, $sort, $limit, $offset);
        $departments = $this->employeeModel->getDepartments();

        Response::success("Employees fetched successfully", [
            "employees" => $employees,
            "summary" => [
                "total_employees" => $totalEmployees,
                "active" => $this->employeeModel->countByStatus("Active"),
                "inactive" => $this->employeeModel->countByStatus("Inactive"),
                "on_leave" => $this->employeeModel->countByStatus("On Leave")
            ],
            "departments" => $departments,
            "pagination" => [
                "current_page" => $page,
                "per_page" => $limit,
                "total_rows" => $totalEmployees,
                "total_pages" => ceil($totalEmployees / $limit)
            ]
        ]);
    }

    public function store()
    {
        Session::start();

        if (Session::get("role") !== "admin") {
            Response::error("Only admin can add employees.", 403);
        }

        $data = [
            "employee_code" => trim($_POST["employee_code"] ?? ""),
            "full_name" => trim($_POST["full_name"] ?? ""),
            "email" => trim($_POST["email"] ?? ""),
            "phone" => trim($_POST["phone"] ?? ""),
            "department" => trim($_POST["department"] ?? ""),
            "designation" => trim($_POST["designation"] ?? ""),
            "gender" => trim($_POST["gender"] ?? ""),
            "join_date" => trim($_POST["join_date"] ?? ""),
            "status" => trim($_POST["status"] ?? ""),
            "address" => trim($_POST["address"] ?? ""),
            "photo" => null
        ];

        if (
            empty($data["employee_code"]) || empty($data["full_name"]) || empty($data["email"]) ||
            empty($data["phone"]) || empty($data["department"]) || empty($data["designation"]) ||
            empty($data["gender"]) || empty($data["join_date"]) || empty($data["status"])
        ) {
            Response::error("All required fields must be filled.", 422);
        }

        if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            Response::error("Please enter a valid email address.", 422);
        }

        if (!in_array($data["gender"], ["Male", "Female", "Other"])) {
            Response::error("Invalid gender selected.", 422);
        }

        if (!in_array($data["status"], ["Active", "Inactive", "On Leave"])) {
            Response::error("Invalid status selected.", 422);
        }

        if ($this->employeeModel->exists($data["employee_code"], $data["email"])) {
            Response::error("Employee code or email already exists.", 409);
        }

        if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] === 0) {
            $uploadResult = ImageUpload::upload($_FILES["photo"], "employees", "employee");

            if (!$uploadResult["status"]) {
                Response::error($uploadResult["message"], 422);
            }

            $data["photo"] = $uploadResult["path"];
        }

        if ($this->employeeModel->create($data)) {
            Response::success("Employee added successfully!");
        } else {
            Response::error("Failed to add employee.", 500);
        }
    }

    public function update()
    {
        Session::start();

        if (Session::get("role") !== "admin") {
            Response::error("Only admin can update employees.", 403);
        }

        $id = (int) ($_POST["edit_employee_id"] ?? 0);

        if ($id <= 0) {
            Response::error("Invalid employee ID.", 422);
        }

        $data = [
            "employee_code" => trim($_POST["edit_employee_code"] ?? ""),
            "full_name" => trim($_POST["edit_full_name"] ?? ""),
            "email" => trim($_POST["edit_email"] ?? ""),
            "phone" => trim($_POST["edit_phone"] ?? ""),
            "department" => trim($_POST["edit_department"] ?? ""),
            "designation" => trim($_POST["edit_designation"] ?? ""),
            "gender" => trim($_POST["edit_gender"] ?? ""),
            "join_date" => trim($_POST["edit_join_date"] ?? ""),
            "status" => trim($_POST["edit_status"] ?? ""),
            "address" => trim($_POST["edit_address"] ?? ""),
            "photo" => null
        ];

        $removeCurrentPhoto = isset($_POST["remove_current_photo"]) ? 1 : 0;

        if (
            empty($data["employee_code"]) || empty($data["full_name"]) || empty($data["email"]) ||
            empty($data["phone"]) || empty($data["department"]) || empty($data["designation"]) ||
            empty($data["gender"]) || empty($data["join_date"]) || empty($data["status"])
        ) {
            Response::error("All required fields must be filled.", 422);
        }

        if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            Response::error("Please enter a valid email address.", 422);
        }

        if (!in_array($data["gender"], ["Male", "Female", "Other"])) {
            Response::error("Invalid gender selected.", 422);
        }

        if (!in_array($data["status"], ["Active", "Inactive", "On Leave"])) {
            Response::error("Invalid status selected.", 422);
        }

        if ($this->employeeModel->exists($data["employee_code"], $data["email"], $id)) {
            Response::error("Another employee already uses this code or email.", 409);
        }

        $oldEmployee = $this->employeeModel->getById($id);

        if (!$oldEmployee) {
            Response::error("Employee not found.", 404);
        }

        $data["photo"] = $oldEmployee["photo"];

        if ($removeCurrentPhoto && !empty($oldEmployee["photo"])) {
            $oldPath = __DIR__ . "/../../" . ltrim(str_replace("../", "", $oldEmployee["photo"]), "/");
            if (file_exists($oldPath)) unlink($oldPath);
            $data["photo"] = null;
        }

        if (isset($_FILES["edit_photo"]) && $_FILES["edit_photo"]["error"] === 0) {
            $uploadResult = ImageUpload::upload($_FILES["edit_photo"], "employees", "employee");

            if (!$uploadResult["status"]) {
                Response::error($uploadResult["message"], 422);
            }

            if (!empty($oldEmployee["photo"])) {
                $oldPath = __DIR__ . "/../../" . ltrim(str_replace("../", "", $oldEmployee["photo"]), "/");
                if (file_exists($oldPath)) unlink($oldPath);
            }

            $data["photo"] = $uploadResult["path"];
        }

        if ($this->employeeModel->update($id, $data)) {
            Response::success("Employee updated successfully!");
        } else {
            Response::error("Failed to update employee.", 500);
        }
    }

    public function destroy()
    {
        Session::start();

        if (Session::get("role") !== "admin") {
            Response::error("Only admin can delete employees.", 403);
        }

        $id = (int) ($_POST["delete_employee_id"] ?? 0);

        if ($id <= 0) {
            Response::error("Invalid employee ID.", 422);
        }

        $employee = $this->employeeModel->getById($id);

        if (!$employee) {
            Response::error("Employee not found.", 404);
        }

        if ($this->employeeModel->delete($id)) {
            if (!empty($employee["photo"])) {
                $imagePath = __DIR__ . "/../../" . ltrim(str_replace("../", "", $employee["photo"]), "/");
                if (file_exists($imagePath)) unlink($imagePath);
            }

            Response::success("Employee deleted successfully!");
        } else {
            Response::error("Failed to delete employee.", 500);
        }
    }
}