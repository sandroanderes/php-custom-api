<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// get database connection
include_once '../config/database.php';

// instantiate license object
include_once '../objects/license.php';

$database = new Database();
$db = $database->getConnection();

$license = new License($db);

// get current date
$date = new DateTime();
$date->add(new DateInterval('P30D'));
/* echo $date->format('Y-m-d H:i:s') . "\n"; */
$valid_until = $date->format('Y-m-d H:i:s');

// get posted data
$data = json_decode(file_get_contents("php://input"));

// make sure data is not empty
if (
    !empty($data->udid) &&
    !empty($data->pid) &&
    !empty($valid_until)
) {
    // set license property values
    $license->udid = $data->udid;
    $license->product_id = $data->pid;
    $license->valid_until = $valid_until;

    // check if udid is already registered
    $license->checkUDID();
    if ($license->udid_check == null) {
        // create the license
        if ($license->generateLicense()) {

            // set response code - 201 created
            http_response_code(201);

            // tell the user
            echo json_encode(array("message" => "License with " . $license->udid . " was created."));
        }

        // if unable to create the license, tell the user
        else {

            // set response code - 503 service unavailable
            http_response_code(503);

            // tell the user
            echo json_encode(array("message" => "Unable to generate license."));
        }
    } else {

        // set response code - 503 service unavailable
        http_response_code(503);

        // tell the user
        echo json_encode(array("message" => "Unable to generate license because UDID is already registered."));

    }
}

// tell the user data is incomplete
else {

    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode(array("message" => "Unable to generate license. Data is incomplete."));
}
