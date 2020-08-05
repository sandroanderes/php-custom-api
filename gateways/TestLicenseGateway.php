<?php

class TestLicenseGateway
{

    private $db = null;

    // object properties
    public $id;
    public $udid;
    public $udid_check;
    public $created;
    public $last_check;
    public $valid_until;
    public $product_name;
    public $ip;
    public $device_information;

    // db table name
    private $table_name = "test_licenses";

    public function __construct($db)
    {
        $this->db = $db;
    }

    // select all licenses
    public function getAll()
    {
        // select all query
        $query = "SELECT
                *
            FROM
                " . $this->table_name . "
            ORDER BY
                id ASC";

        try {
            // prepare query statement
            $stmt = $this->db->query($query);

            // get retrieved row
            $result = $stmt->fetchALL(PDO::FETCH_ASSOC);

            //return result
            return $result;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    // select license with specific udid
    public function get($udid)
    {
        $query = "SELECT *
            FROM
                " . $this->table_name . "
            WHERE udid = ?;
        ";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array($udid));
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    // select license with product and product 
    public function check($product, $udid)
    {
        // select udid, valid_until, product query
        $query = "SELECT
                udid, valid_until, product
            FROM
                " . $this->table_name . "
            WHERE
                product = ? AND udid = ?
            LIMIT
                0,1";

        try {
            // prepare query statement
            $stmt = $this->db->prepare($query);

            // execute query
            $stmt->execute(array($product, $udid));

            // get retrieved row
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            //return result
            return $result;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    // insert new license
    public function insert(array $input)
    {
        $query = "INSERT INTO
                " . $this->table_name . "
            SET
            product=:product, udid=:udid, ip=:ip, device_information=:device_information, valid_until=:valid_until";

        try {

            // prepare query
            $stmt = $this->db->prepare($query);

            // sanitize
            $udid = htmlspecialchars(strip_tags($input['udid']));
            $product = htmlspecialchars(strip_tags($input['product']));
            $ip = htmlspecialchars(strip_tags($input['ip']));
            $device_information = htmlspecialchars(strip_tags($input['device_information']));
            $valid_until = htmlspecialchars(strip_tags($input['valid_until']));

            // bind values
            $stmt->bindParam(":product", $product);
            $stmt->bindParam(":udid", $udid);
            $stmt->bindParam(":ip", $ip);
            $stmt->bindParam(":device_information", $device_information);
            $stmt->bindParam(":valid_until", $valid_until);

            // execute query
            if ($stmt->execute()) {
                return $stmt->rowCount();
            } else {
                $arr = $stmt->errorInfo();
                print_r($arr);
            }
            return false;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    // delete spcific license
    public function delete($udid)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE udid = :udid; ";

        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute(array('udid' => $udid));
            return $stmt->rowCount();
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    // update last_check field in db
    function updateLastCheck($udid)
    {
        // update last_check query
        $query = "UPDATE " . $this->table_name . " SET last_check = :last_check WHERE udid = :udid";

        // prepare query statement
        $stmt = $this->db->prepare($query);

        $date = new DateTime();
        $today = $date->format('Y-m-d H:i:s');

        // sanitize
        $udid = htmlspecialchars(strip_tags($udid));
        $today = htmlspecialchars(strip_tags($today));

        // bind new values
        $stmt->bindParam(':udid', $udid);
        $stmt->bindParam(':last_check', $today);

        // execute the query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
