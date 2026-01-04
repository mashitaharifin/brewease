<?php
if(!defined('DB_SERVER')){
    require_once "../initialize.php";
}

class DBConnection{

    private $host = DB_SERVER;
    private $username = DB_USERNAME;
    private $password = DB_PASSWORD;
    private $database = DB_NAME;
    
    protected $conn;  
    
    public function __construct() {
        if (!isset($this->conn)) {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
            
            if ($this->conn->connect_error) {
                echo 'Cannot connect to database server: ' . $this->conn->connect_error;
                exit;
            }            
        }    
    }

    // Getter for mysqli connection
    public function getConnection() {
        return $this->conn;
    }

    public function __destruct() {
        if ($this->conn instanceof mysqli) {
            try {
                if (@$this->conn->ping()) {
                    $this->conn->close();
                }
            } catch (Throwable $e) {
                // suppress errors
            }
        }
    }
}
?>
