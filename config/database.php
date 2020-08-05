<?php
require_once('config.php');

class Database
{
    // db connection config vars
    private $dbHost = DBHOST;
    private $dbName = DBNAME;
    private $dbUsername = DBUSER;
    private $dbPassword = DBPWD;
    public $conn;

    // get the database connection
    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->dbHost . ";dbname=" . $this->dbName, $this->dbUsername, $this->dbPassword);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
