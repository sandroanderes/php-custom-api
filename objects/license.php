<?php
class License
{
    // database connection and table name
    private $conn;
    private $table_name = "test_licenses";

    // object properties
    public $id;
    public $udid;
    public $udid_check;
    public $created;
    public $last_check;
    public $valid_until;
    public $product_id;
    public $product_name;

    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // read licenses
    function read()
    {

        // select all query
        $query = "SELECT
                l.id, l.udid, l.created, l.last_check, l.valid_until, l.product_id, p.name as product_name
            FROM
                " . $this->table_name . " l
                LEFT JOIN
                products p
                    ON l.product_id = p.id

            ORDER BY
                l.id ASC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        return $stmt;
    }

    // create product
    function create()
    {

        // query to insert record
        $query = "INSERT INTO
                " . $this->table_name . "
            SET
                name=:name, price=:price, description=:description, category_id=:category_id, created=:created";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->created = htmlspecialchars(strip_tags($this->created));

        // bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":created", $this->created);

        // execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // create license
    function generateLicense()
    {

        // query to insert record
        $query = "INSERT INTO
                " . $this->table_name . "
            SET
                udid=:udid, product_id=:product_id, valid_until=:valid_until";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->udid = htmlspecialchars(strip_tags($this->udid));
        $this->product_id = htmlspecialchars(strip_tags($this->product_id));
        $this->valid_until = htmlspecialchars(strip_tags($this->valid_until));

        // bind values
        $stmt->bindParam(":udid", $this->udid);
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":valid_until", $this->valid_until);

        // execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // used when filling up the update product form
    function readOne()
    {

        // query to read single record
        $query = "SELECT
                l.id, l.udid, l.created, l.last_check, l.valid_until, l.product_id, p.name as product_name
            FROM
                " . $this->table_name . " l
                LEFT JOIN
                products p
                    ON l.product_id = p.id
            WHERE
                l.udid = ?
            LIMIT
                0,1";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // bind id of product to be updated
        $stmt->bindParam(1, $this->udid);

        // execute query
        $stmt->execute();

        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set values to object properties
        $this->id = $row['id'];
        $this->udid = $row['udid'];
        $this->created = $row['created'];
        $this->last_check = $row['last_check'];
        $this->valid_until = $row['valid_until'];
        $this->product_id = $row['product_id'];
        $this->product_name = $row['product_name'];
    }

    // update the product
    function update()
    {

        // update query
        $query = "UPDATE
                " . $this->table_name . "
            SET
                name = :name,
                price = :price,
                description = :description,
                category_id = :category_id
            WHERE
                id = :id";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind new values
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':id', $this->id);

        // execute the query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // delete the product
    function delete()
    {

        // delete query
        $query = "DELETE FROM " . $this->table_name . " WHERE udid = ?";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->udid = htmlspecialchars(strip_tags($this->udid));

        // bind id of record to delete
        $stmt->bindParam(1, $this->udid);

        // execute query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // search products
    function search($keywords)
    {

        // select all query
        $query = "SELECT
                c.name as category_name, p.id, p.name, p.description, p.price, p.category_id, p.created
            FROM
                " . $this->table_name . " p
                LEFT JOIN
                    categories c
                        ON p.category_id = c.id
            WHERE
                p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?
            ORDER BY
                p.created DESC";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $keywords = htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";

        // bind
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);

        // execute query
        $stmt->execute();

        return $stmt;
    }

    // used when filling up the update product form
    function checkLicense()
    {

        // query to read single record
        $query = "SELECT
                l.udid, l.valid_until, l.product_id, p.name as product_name
            FROM
                " . $this->table_name . " l
                LEFT JOIN
                products p
                    ON l.product_id = p.id
            WHERE
                l.udid = ? AND l.product_id = ?
            LIMIT
                0,1";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // bind udid and product_id
        $stmt->bindParam(1, $this->udid);
        $stmt->bindParam(2, $this->product_id);

        // execute query
        $stmt->execute();

        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set values to object properties
        $this->udid = $row['udid'];
        $this->valid_until = $row['valid_until'];
        $this->product_id = $row['product_id'];
        $this->product_name = $row['product_name'];
    }

    // used when filling up the update product form
    function checkUDID()
    {
        // query to read single record
        $query = "SELECT
                l.udid
            FROM
                " . $this->table_name . " l
            WHERE
                l.udid = ?
            LIMIT
                0,1";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // bind udid and product_id
        $stmt->bindParam(1, $this->udid);

        // execute query
        $stmt->execute();

        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set values to object properties
        $this->udid_check = $row['udid'];
    }

    function updateLastCheck()
    {
        // update last_check query
        $query = "UPDATE
                 " . $this->table_name . "
             SET
                 last_check = :last_check
             WHERE
                 udid = :udid";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        $date = new DateTime();
        $today = $date->format('Y-m-d H:i:s');

        // sanitize
        $this->udid = htmlspecialchars(strip_tags($this->udid));
        $today = htmlspecialchars(strip_tags($today));

        // bind new values
        $stmt->bindParam(':udid', $this->udid);
        $stmt->bindParam(':last_check', $today);

        // execute the query
        if ($stmt->execute()) {
        
            // set values to object properties
            $this->last_check = $today;
            return true;

        }

        return false;
  
    }
}
