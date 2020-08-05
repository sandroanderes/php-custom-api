<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/database.php';
include_once '../objects/license.php';

// instantiate database and license object
$database = new Database();
$db = $database->getConnection();

// initialize object
$license = new License($db);

// query licenses
$stmt = $license->read();
$num = $stmt->rowCount();

// check if more than 0 record found
if ($num > 0) {

    // licenses array
    $licenses_arr = array();
    $licenses_arr["records"] = array();

    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);

        $license_item = array(
            "id" => $id,
            "udid" => $udid,
            "created" => $created,
            "last_check" => $last_check,
            "valid_until" => $valid_until,
            "product_id" => $product_id,
            "product_name" => $product_name
    );

        array_push($licenses_arr["records"], $license_item);
    }

    // set response code - 200 OK
    http_response_code(200);

    // show licenses data in json format
    echo json_encode($licenses_arr);
}
  
else{
  
    // set response code - 404 Not found
    http_response_code(404);
  
    // tell the user no licenses found
    echo json_encode(
        array("message" => "No licenses found.")
    );
}