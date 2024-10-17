<?php


header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');


$targetDir = "thumbnail/";




$targetFile = $targetDir . basename($_FILES["file"]["name"]);

// Check if the uploads directory exists, if not, create it
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// Check if file was uploaded without errors
if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
        echo "The file ". htmlspecialchars(basename($_FILES["file"]["name"])) . " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
} else {
    echo "Error: " . $_FILES["file"]["error"];
}

?>