<?php
// -------------------------------------
// FILEVALIDATION.PHP
// contains php script to validate
// file uploaded to the server
// -------------------------------------

// Check file size
function check_file_size($file) {
    if ($file["size"] > 1500000)
        return false;
    return true;
}

// Check if file already exists
function check_file_not_exists($target_file) {
    if (file_exists($target_file))
        return false;
    return true;
}

// Get file extension
function file_get_extension($file) {
    return strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
}

// Allow only certain file formats
function check_file_extension($file) {
    $extension = file_get_extension($file);

    if($extension != "csv")
        return false;

    return true;
}

// Try to upload file
function upload_file($file, $target_file) {
    if (move_uploaded_file($file["tmp_name"], $target_file))
        return true;
    else
        return false;
}

?>