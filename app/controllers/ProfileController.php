<?php

require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../helpers/Response.php";
require_once __DIR__ . "/../helpers/Session.php";
require_once __DIR__ . "/../helpers/ImageUpload.php";

class ProfileController
{
    private $userModel;

    public function __construct($db)
    {
        $this->userModel = new User($db);
    }

    public function show()
    {
        Session::start();

        $userId = Session::get("user_id");

        if (!$userId) {
            Response::error("Unauthorized access.", 401);
        }

        $user = $this->userModel->getProfileById($userId);

        if (!$user) {
            Response::error("User not found.", 404);
        }

        Response::success("Profile fetched successfully.", [
            "user" => $user
        ]);
    }

    public function update()
    {
        Session::start();

        $userId = Session::get("user_id");

        if (!$userId) {
            Response::error("Unauthorized access.", 401);
        }

        $full_name = trim($_POST["full_name"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $password = trim($_POST["password"] ?? "");
        $confirm_password = trim($_POST["confirm_password"] ?? "");
        $remove_current_image = isset($_POST["remove_current_image"]) ? 1 : 0;

        if (empty($full_name) || empty($email)) {
            Response::error("Full name and email are required.", 422);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error("Please enter a valid email address.", 422);
        }

        if ($this->userModel->emailExists($email, $userId)) {
            Response::error("Another user already uses this email address.", 409);
        }

        $currentUser = $this->userModel->getProfileById($userId);

        if (!$currentUser) {
            Response::error("User not found.", 404);
        }

        $newImagePath = $currentUser["profile_picture"];

        if ($remove_current_image && !empty($currentUser["profile_picture"])) {
            $oldPath = __DIR__ . "/../../" . ltrim(str_replace("../", "", $currentUser["profile_picture"]), "/");
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
            $newImagePath = null;
        }

        if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] === 0) {
            $uploadResult = ImageUpload::upload($_FILES["profile_picture"], "users", "user");

            if (!$uploadResult["status"]) {
                Response::error($uploadResult["message"], 422);
            }

            if (!empty($currentUser["profile_picture"])) {
                $oldPath = __DIR__ . "/../../" . ltrim(str_replace("../", "", $currentUser["profile_picture"]), "/");
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $newImagePath = $uploadResult["path"];
        }

        $hashedPassword = null;

        if (!empty($password) || !empty($confirm_password)) {
            if (strlen($password) < 6) {
                Response::error("Password must be at least 6 characters long.", 422);
            }

            if ($password !== $confirm_password) {
                Response::error("Password and confirm password do not match.", 422);
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        }

        $updated = $this->userModel->updateProfile(
            $userId,
            $full_name,
            $email,
            $newImagePath,
            $hashedPassword
        );

        if ($updated) {
            Session::set("full_name", $full_name);
            Session::set("email", $email);
            Session::set("profile_picture", $newImagePath);

            Response::success("Profile updated successfully!");
        } else {
            Response::error("Failed to update profile.", 500);
        }
    }
}