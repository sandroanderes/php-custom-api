<?php

// include database and object files
require_once('../config/database.php');
require_once('../controller/LicenseController.php');

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// all of the endpoints start with /license
// everything else results in a 404 Not Found
if ($uri[2] !== 'license') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

// the product is optional but must be a string:
$param1 = null;
if (isset($uri[3])) {
    $param1 = (string) $uri[3];
}

// the license UDID is optional but must be a number:
$param2 = null;
if (isset($uri[4])) {
    $param2 = (string) $uri[4];
}

$requestMethod = $_SERVER["REQUEST_METHOD"];


// get database connection
$database = new Database();
$dbConnection = $database->getConnection();

// pass the request method and user ID to the PersonController:
$controller = new LicenseController($dbConnection, $requestMethod, $param1, $param2);
$controller->processRequest();
