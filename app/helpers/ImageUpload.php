<?php

class ImageUpload
{
    public static function upload($file, $folder = "users", $prefix = "file")
    {
        if (!isset($file) || $file["error"] !== 0) {
            return [
                "status" => false,
                "message" => "No image uploaded."
            ];
        }

        $allowedExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
        $fileName = $file["name"];
        $tmpName = $file["tmp_name"];
        $fileSize = $file["size"];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowedExtensions)) {
            return [
                "status" => false,
                "message" => "Only JPG, JPEG, PNG, GIF, and WEBP images are allowed."
            ];
        }

        if ($fileSize > 2 * 1024 * 1024) {
            return [
                "status" => false,
                "message" => "Image size must be less than 2MB."
            ];
        }

        $absoluteUploadDir = __DIR__ . "/../../uploads/" . $folder . "/";

        if (!is_dir($absoluteUploadDir)) {
            mkdir($absoluteUploadDir, 0777, true);
        }

        $newFileName = $prefix . "_" . time() . "_" . rand(1000, 9999) . "." . $fileExt;
        $destination = $absoluteUploadDir . $newFileName;

        if (move_uploaded_file($tmpName, $destination)) {
            return [
                "status" => true,
                "path" => "../uploads/" . $folder . "/" . $newFileName
            ];
        }

        return [
            "status" => false,
            "message" => "Failed to upload image."
        ];
    }
}