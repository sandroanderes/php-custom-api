<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

// include database and object files
include_once '../config/database.php';
include_once '../objects/license.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare license object
$license = new License($db);

// set udid property of record to read
$license->udid = isset($_GET['udid']) ? $_GET['udid'] : die();

// read the details of license to be edited
$license->readOne();

if ($license->udid != null) {
    // create array
    $license_arr = array(
        "id" =>  $license->id,
        "udid" => $license->udid,
        "created" => $license->created,
        "last_check" => $license->last_check,
        "valid_until" => $license->valid_until,
        "product_id" => $license->product_id,
        "product_name" => $license->product_name
    );

    // set response code - 200 OK
    http_response_code(200);

    // make it json format
    echo json_encode($license_arr);
} else {
    // set response code - 404 Not found
    http_response_code(404);

    // tell the user license does not exist
    echo json_encode(array("message" => "License does not exist."));
}
