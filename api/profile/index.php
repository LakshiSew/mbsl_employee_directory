<?php

require_once "../../app/config/db.php";
require_once "../../app/controllers/ProfileController.php";
require_once "../../app/helpers/Session.php";
require_once "../../app/helpers/Response.php";

Session::start();

if (!Session::has("user_id")) {
    Response::error("Unauthorized access.", 401);
}

$database = new Database();
$conn = $database->connect();

$profileController = new ProfileController($conn);

$method = $_SERVER["REQUEST_METHOD"];
$action = $_POST["_method"] ?? "";

if ($method === "GET") {
    $profileController->show();
} elseif ($method === "POST" && $action === "PUT") {
    $profileController->update();
} else {
    Response::error("Method not allowed.", 405);
}