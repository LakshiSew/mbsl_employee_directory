<?php

require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../helpers/Response.php";
require_once __DIR__ . "/../helpers/Session.php";

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
            "redirect" => "../public/dashboard.php",
            "user" => [
                "id" => $user["id"],
                "full_name" => $user["full_name"],
                "email" => $user["email"],
                "role" => $user["role"]
            ]
        ]);
    }

    public function logout()
    {
        Session::destroy();
        Response::success("Logout successful!", [
            "redirect" => "../public/login.php"
        ]);
    }
}