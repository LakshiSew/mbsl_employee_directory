<?php

require_once "../../app/config/db.php";
require_once "../../app/controllers/EmployeeController.php";
require_once "../../app/helpers/Session.php";
require_once "../../app/helpers/Response.php";

Session::start();

if (!Session::has("user_id")) {
    Response::error("Unauthorized access.", 401);
}

$database = new Database();
$conn = $database->connect();

$employeeController = new EmployeeController($conn);

$method = $_SERVER["REQUEST_METHOD"];
$action = $_POST["_method"] ?? "";

if ($method === "GET") {
    $employeeController->index();
} elseif ($method === "POST" && $action === "PUT") {
    $employeeController->update();
} elseif ($method === "POST" && $action === "DELETE") {
    $employeeController->destroy();
} elseif ($method === "POST") {
    $employeeController->store();
} else {
    Response::error("Method not allowed.", 405);
}