<?php

// -------------------------------------
// SENDMAIL.PHP
// permit to send automatically email
// -------------------------------------

//header('Content-type: application/json');

// -------------------------------------
// REQUIRE
// -------------------------------------

require_once "assets/jsonresponse.php";
require_once "assets/filevalidation.php";

// -------------------------------------
// CONSTANTS
// -------------------------------------

const NAME_COLUMN = 0;
const TEL_COLUMN = 1;
const EMAIL_COLUMN = 2;

// ------------------------------------
// PHP SCRIPT LOOP
// -----------------------------------

check_uploaded_file($_FILES['csv']);
$file_uri = upload_file_to_server($_FILES['csv'], $_POST['fair']);
$file = open_file($file_uri);
$data = read_uploaded_file($file);
main_loop($data, $_POST['name'], $_POST['email'], $_POST['subject']);
launch_response("All emails are sent with success!");

// -------------------------------------
// FILE UPLOAD/READ FUNCTIONS
// -------------------------------------

// Check if uploaded file is a valid one
function check_uploaded_file($file) {
    if(!isset($file))
        launch_error("CSV file was not submitted.");
    
    if ($file["error"] > 0)
        launch_error("CSV file was not correctly submitted - Error: " . $_FILES["file"]["error"]);
    
    if (!check_file_size($file))
        launch_error("Your file is bigger than 1.5 MB.");

    if (!check_file_extension($file))
        launch_error("Your file extension is not .csv!");    
}

// Upload a file to server
function upload_file_to_server($file, $filename) {
    $target_dir = "upload/csv/";
    $target_file = $filename . "." . file_get_extension($file);
    $target_uri = $target_dir . $target_file;
    
    if(check_file_not_exists($target_uri) && upload_file($file, $target_uri))
        return $target_uri;
    else
        launch_error("Problem uploading your file to server!");
}

// Open CSV file and return it
function open_file($file_uri) {
    if (!$file = fopen($file_uri , 'r'))
        launch_error("Unable to open uploaded file.");

    return $file;
}

// Read csv file and return a matrix with read data
function read_uploaded_file($file) {
    $line = array();
    $line[0] = fgets($file, 4096);

    $num_of_field = strlen($line[0]) - strlen(str_replace(";", "", $line[0]));

    $csv_data = array();
    $csv_data[0] = array();
    $csv_data[0] = explode( ";", $line[0], ($num_of_field+1));

    $i = 1;
    
    while ($line[$i] = fgets($file, 4096)) {

        $csv_data[$i] = array();
        $csv_data[$i] = explode( ";", $line[$i], ($num_of_field+1) );

        $i++;
    }

    return $csv_data;
}

// -------------------------------------
// EMAIL COMPOSE/SEND FUNCTIONS
// -------------------------------------

// Iterate over data and send emails
function main_loop($data, $my_name, $my_mail, $subject) {
    $message = get_raw_message();

    unset($data[0]); // it contains column name

    foreach($data as $customer) {
        $content = substitute_fields($message, $customer[NAME_COLUMN]);

        $sender = array();
        $sender['name'] = $my_name;
        $sender['email'] = $my_mail;

        $recipent = array();
        $recipent['name'] = $customer[NAME_COLUMN];
        $recipent['email'] = $customer[EMAIL_COLUMN];

        send_email($sender, $recipent, $subject, $content);
    }
}

// Get raw message from POST request
function get_raw_message() {
    $raw_message = $_POST['editordata'];
    return $raw_message;
}

// Substitute shortcode in raw message with real data and return it
function substitute_fields($message, $company) {
    return str_replace("{company_name}", $company, $message);
}

// Send an email
function send_email($sender, $recipent, $subject, $content) {
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=iso-8859-1';
    $headers[] = 'From:' . $sender['email'];

    if(!mail($recipent['email'], $subject, $content, implode("\r\n", $headers)))
        launch_error("Was not possible to send an email to " . $recipent['email'] . ". Retry later.");

}

?>