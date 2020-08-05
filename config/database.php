<?php
include('config.php');

class Database
{
    // specify your own database credentials
    private $host = DBHOST;
    private $db_name = DBNAME;
    private $username = DBUSER;
    private $password = DBPWD;
    public $conn;

    // get the database connection
    public function getConnection()
    {

        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
