<?php

require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../helpers/Response.php";
require_once __DIR__ . "/../helpers/Session.php";
require_once __DIR__ . "/../helpers/ImageUpload.php";

class AuthController
{
    private $conn;
    private $userModel;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->userModel = new User($db);
    }

    public function login()
    {
        Session::start();

        $email = trim($_POST["email"] ?? "");
        $password = trim($_POST["password"] ?? "");

        if (empty($email) || empty($password)) {
            Response::error("Please fill in all fields.", 422);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error("Please enter a valid email address.", 422);
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            Response::error("No account found with this email.", 404);
        }

        if (!password_verify($password, $user["password"])) {
            Response::error("Incorrect password.", 401);
        }

        Session::set("user_id", $user["id"]);
        Session::set("full_name", $user["full_name"]);
        Session::set("email", $user["email"]);
        Session::set("role", $user["role"]);
        Session::set("profile_picture", $user["profile_picture"]);

        Response::success("Login successful!", [
            "redirect" => "dashboard.php",
            "user" => [
                "id" => $user["id"],
                "full_name" => $user["full_name"],
                "email" => $user["email"],
                "role" => $user["role"],
                "profile_picture" => $user["profile_picture"]
            ]
        ]);
    }

    public function register()
    {
        $full_name = trim($_POST["full_name"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $password = trim($_POST["password"] ?? "");
        $confirm_password = trim($_POST["confirm_password"] ?? "");
        $role = trim($_POST["role"] ?? "user");

        if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
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

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $created = $this->userModel->create(
            $full_name,
            $email,
            $hashed_password,
            $role,
            $profile_picture
        );

        if ($created) {
            Response::success("Registration successful! Please login.", [
                "redirect" => "login.php"
            ], 201);
        } else {
            Response::error("Something went wrong. Please try again.", 500);
        }
    }

    public function logout()
    {
        Session::destroy();

        Response::success("Logged out successfully", [
            "redirect" => "login.php"
        ]);
    }
}