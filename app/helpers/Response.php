<?php

class Response
{
    public static function success($message = "Success", $data = [], $statusCode = 200)
    {
        http_response_code($statusCode);
        header("Content-Type: application/json");

        echo json_encode([
            "status" => true,
            "message" => $message,
            "data" => $data
        ]);
        exit();
    }

    public static function error($message = "Something went wrong", $statusCode = 400, $data = [])
    {
        http_response_code($statusCode);
        header("Content-Type: application/json");

        echo json_encode([
            "status" => false,
            "message" => $message,
            "data" => $data
        ]);
        exit();
    }
}