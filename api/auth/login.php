<?php

require_once "../../app/config/db.php";
require_once "../../app/controllers/AuthController.php";

$database = new Database();
$conn = $database->connect();

$authController = new AuthController($conn);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $authController->login();
} else {
    require_once "../../app/helpers/Response.php";
    Response::error("Method not allowed.", 405);
}