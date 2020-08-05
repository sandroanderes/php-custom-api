<?php
require_once('../gateways/TestLicenseGateway.php');

class LicenseController
{

    private $db;
    private $requestMethod;
    private $productName;
    private $deviceUDID;

    private $personGateway;

    public function __construct($db, $requestMethod, $productName, $deviceUDID)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->productName = $productName;
        $this->deviceUDID = $deviceUDID;

        $this->licenseGateway = new TestLicenseGateway($db);
    }

    // switch case for different request methodes
    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->productName and $this->deviceUDID) {
                    $response = $this->checkLicense($this->productName, $this->deviceUDID);
                } else {
                    $response = $this->getAllLicenses();
                };
                break;
            case 'POST':
                $response = $this->createLicense();
                break;
                /* case 'PUT':
                $response = $this->updateLicense($this->deviceUDID);
                break; */
            case 'DELETE':
                $response = $this->deleteLicense($this->deviceUDID);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    // get all licenses
    private function getAllLicenses()
    {
        $result = $this->licenseGateway->getAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    // check if license is valid
    private function checkLicense($product, $udid)
    {

        //get current date
        $today = date(DateTime::ISO8601);

        $result = $this->licenseGateway->check($product, $udid);
        
        //check if udid is equal null or valid_until is smaller than the present date
        if ($result['udid'] == null or $result['valid_until'] < $today) {
            $license_arr = array(
                "valid" => "false",
            );
            $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        } else {
            //update last_check
            if (!$this->licenseGateway->updateLastCheck($udid)) {
                echo "last_check couldn't be updated";
            }

            $license_arr = array(
                "valid" => "true",
                "valid_until" => date(DATE_ISO8601, strtotime($result['valid_until']))
            );
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
        }
        $response['body'] = json_encode($license_arr);
        return $response;
    }

    // create a new license
    private function createLicense()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        // check if input is valid
        if (!$this->validateLicense($input)) {
            return $this->unprocessableEntityResponse();
        }

        // get current date
        $date = new DateTime();
        $date->add(new DateInterval('P30D'));
        $valid_until = $date->format('Y-m-d H:i:s');

        // put valid_until to array
        $input["valid_until"] = $valid_until;


        if (!$this->licenseGateway->get($input['udid'])) {
            $result = $this->licenseGateway->insert($input);
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $license_arr = array(
                "created" => "true",
            );
            $response['body'] = json_encode($license_arr);
        } else {
            return $this->unprocessableEntityResponse();
        }
        return $response;
    }

    // delete license
    private function deleteLicense($udid)
    {
        $result = $this->licenseGateway->get($udid);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $this->licenseGateway->delete($udid);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $license_arr = array(
            "deleted" => "true",
        );
        $response['body'] = json_encode($license_arr);
        return $response;
    }

    // check if license input is valid
    private function validateLicense($input)
    {
        // check if product is set
        if (!isset($input['product'])) {
            return false;
        }

        // check if udid is set
        if (!isset($input['udid'])) {
            return false;
        }
        // check if ip-adress is set and if the format is correct
        if (isset($input['ip'])) {
            if (filter_var($input['ip'], FILTER_VALIDATE_IP)) {
                /* echo ($input['ip'] . " is a valid IP address"); */
            } else {
                /* echo ($input['ip'] . " is not a valid IP address"); */
                return false;
            }
        } else {
            return false;
        }

        // check if device_information is set
        if (!isset($input['device_information'])) {
            return false;
        }
        return true;
    }

    // error message for unprocessable entity
    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    // error message if not found
    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}
