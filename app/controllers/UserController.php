<?php

require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../helpers/Response.php";
require_once __DIR__ . "/../helpers/ImageUpload.php";
require_once __DIR__ . "/../helpers/Session.php";

class UserController
{
    private $userModel;

    public function __construct($db)
    {
        $this->userModel = new User($db);
    }

    public function index()
    {
        $search = trim($_GET["search"] ?? "");
        $role = trim($_GET["role"] ?? "");
        $sort = trim($_GET["sort"] ?? "id_desc");
        $page = isset($_GET["page"]) ? (int) $_GET["page"] : 1;
        $limit = 5;

        if ($page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * $limit;

        $totalUsers = $this->userModel->countAll($search, $role);
        $users = $this->userModel->getAll($search, $role, $sort, $limit, $offset);
        $adminCount = $this->userModel->countByRole("admin");
        $staffCount = $this->userModel->countByRole("user");

        Response::success("Users fetched successfully", [
            "users" => $users,
            "summary" => [
                "total_users" => $totalUsers,
                "admin_users" => $adminCount,
                "staff_users" => $staffCount
            ],
            "pagination" => [
                "current_page" => $page,
                "per_page" => $limit,
                "total_rows" => $totalUsers,
                "total_pages" => ceil($totalUsers / $limit)
            ]
        ]);
    }

    public function store()
    {
        Session::start();

        if (Session::get("role") !== "admin") {
            Response::error("Only admin can add users.", 403);
        }

        $full_name = trim($_POST["full_name"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $password = trim($_POST["password"] ?? "");
        $confirm_password = trim($_POST["confirm_password"] ?? "");
        $role = trim($_POST["role"] ?? "");

        if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
            Response::error("All fields are required.", 422);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error("Please enter a valid email address.", 422);
        }

        if (strlen($password) < 6) {
            Response::error("Password must be at least 6 characters long.", 422);
        }

        if ($password !== $confirm_password) {
            Response::error("Password and Confirm Password do not match.", 422);
        }

        if (!in_array($role, ["admin", "user"])) {
            Response::error("Invalid role selected.", 422);
        }

        if ($this->userModel->emailExists($email)) {
            Response::error("This email is already registered.", 409);
        }

        $profile_picture = null;

        if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] === 0) {
            $uploadResult = ImageUpload::upload($_FILES["profile_picture"]);

            if (!$uploadResult["status"]) {
                Response::error($uploadResult["message"], 422);
            }

            $profile_picture = $uploadResult["path"];
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $created = $this->userModel->create(
            $full_name,
            $email,
            $hashedPassword,
            $role,
            $profile_picture
        );

        if ($created) {
            Response::success("User added successfully!");
        } else {
            Response::error("Failed to add user. Please try again.", 500);
        }
    }

    public function update()
    {
        Session::start();

        if (Session::get("role") !== "admin") {
            Response::error("Only admin can update users.", 403);
        }

        $id = (int) ($_POST["edit_user_id"] ?? 0);
        $full_name = trim($_POST["edit_full_name"] ?? "");
        $email = trim($_POST["edit_email"] ?? "");
        $role = trim($_POST["edit_role"] ?? "");
        $new_password = trim($_POST["edit_password"] ?? "");
        $confirm_new_password = trim($_POST["edit_confirm_password"] ?? "");
        $remove_current_image = isset($_POST["remove_current_image"]) ? 1 : 0;

        if ($id <= 0) {
            Response::error("Invalid user ID.", 422);
        }

        if (empty($full_name) || empty($email) || empty($role)) {
            Response::error("Full name, email, and role are required.", 422);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error("Please enter a valid email address.", 422);
        }

        if (!in_array($role, ["admin", "user"])) {
            Response::error("Invalid role selected.", 422);
        }

        if ($this->userModel->emailExists($email, $id)) {
            Response::error("Another user already uses this email address.", 409);
        }

        $oldUser = $this->userModel->getById($id);

        if (!$oldUser) {
            Response::error("User not found.", 404);
        }

        $newImagePath = $oldUser["profile_picture"];

        if ($remove_current_image && !empty($oldUser["profile_picture"])) {
            $oldPath = __DIR__ . "/../../" . ltrim(str_replace("../", "", $oldUser["profile_picture"]), "/");
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
            $newImagePath = null;
        }

        if (isset($_FILES["edit_profile_picture"]) && $_FILES["edit_profile_picture"]["error"] === 0) {
            $uploadResult = ImageUpload::upload($_FILES["edit_profile_picture"]);

            if (!$uploadResult["status"]) {
                Response::error($uploadResult["message"], 422);
            }

            if (!empty($oldUser["profile_picture"])) {
                $oldPath = __DIR__ . "/../../" . ltrim(str_replace("../", "", $oldUser["profile_picture"]), "/");
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $newImagePath = $uploadResult["path"];
        }

        $hashedPassword = null;

        if (!empty($new_password) || !empty($confirm_new_password)) {
            if (strlen($new_password) < 6) {
                Response::error("New password must be at least 6 characters long.", 422);
            }

            if ($new_password !== $confirm_new_password) {
                Response::error("New password and confirm password do not match.", 422);
            }

            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
        }

        $updated = $this->userModel->update(
            $id,
            $full_name,
            $email,
            $role,
            $newImagePath,
            $hashedPassword
        );

        if ($updated) {
            Response::success("User updated successfully!");
        } else {
            Response::error("Failed to update user.", 500);
        }
    }

    public function destroy()
    {
        Session::start();

        if (Session::get("role") !== "admin") {
            Response::error("Only admin can delete users.", 403);
        }

        $id = (int) ($_POST["delete_user_id"] ?? 0);

        if ($id <= 0) {
            Response::error("Invalid user ID.", 422);
        }

        if ($id == Session::get("user_id")) {
            Response::error("You cannot delete your own logged-in account.", 422);
        }

        $user = $this->userModel->getById($id);

        if (!$user) {
            Response::error("User not found.", 404);
        }

        $deleted = $this->userModel->delete($id);

        if ($deleted) {
            if (!empty($user["profile_picture"])) {
                $imagePath = __DIR__ . "/../../" . ltrim(str_replace("../", "", $user["profile_picture"]), "/");
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            Response::success("User deleted successfully!");
        } else {
            Response::error("Failed to delete user.", 500);
        }
    }
}