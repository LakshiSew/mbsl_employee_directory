<?php

require_once "../../app/config/db.php";
require_once "../../app/controllers/DashboardController.php";
require_once "../../app/helpers/Session.php";
require_once "../../app/helpers/Response.php";

Session::start();

if (!Session::has("user_id")) {
    Response::error("Unauthorized access", 401);
}

$database = new Database();
$conn = $database->connect();

$dashboardController = new DashboardController($conn);

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $dashboardController->getStats();
} else {
    Response::error("Method not allowed", 405);
}