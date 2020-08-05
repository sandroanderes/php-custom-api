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

//get current date
$date = new DateTime();
$today = $date->format('Y-m-d H:i:s');

// set udid and product_id property of record to read
$license->udid = isset($_GET['udid']) ? $_GET['udid'] : die();
$license->product_id = isset($_GET['pid']) ? $_GET['pid'] : die();

// read the details of license
$license->checkLicense();

//check if the udid exists and the license is not yet expired
if ($license->udid != null and $license->valid_until >= $today) {

    //update last_check
    if (!$license->updateLastCheck()) {
        $license->last_check = "last_check couldn't be updated";
    }

    // create array
    $license_arr = array(
        "udid" => $license->udid,
        "valid_until" => $license->valid_until,
        "today" => $today,
        "product_id" => $license->product_id,
        "product_name" => $license->product_name,
        "last_check" => $license->last_check
    );

    // set response code - 200 OK
    http_response_code(200);

    // make it json format
    /* echo json_encode($license_arr); */

    // tell the user license is valid
    echo json_encode(array("valid" => "true"));
} else {
    // set response code - 404 Not found
    http_response_code(404);

    // tell the user license is not valid
    echo json_encode(array("valid" => "false"));
}
