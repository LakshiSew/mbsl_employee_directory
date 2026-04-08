<?php

require_once "../../app/helpers/Session.php";
require_once "../../app/helpers/Response.php";

Session::start();
Session::destroy();

Response::success("Logged out successfully", [
    "redirect" => "../public/login.php"
]);