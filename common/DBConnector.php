<?php 
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
class DBConnector {
    private static $instance = null;
    private $conn;
    private $host     = '127.0.0.1';
    private $database = 'pensuh';
    private $username = 'root';
    private $password = '';
    private function __construct() { 
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
            
        } catch (mysqli_sql_exception $e) {
            throw new Exception($this->conn->getMessage() . ": " . $this->conn->getCode()); 
        }finally{
            //$this->conn->close();
        }       
    }

    /**
     * Establishing database connection
     * @return database connection instance
     */
    public static function getInstance() {
        if(!self::$instance){
           self::$instance = new DBConnector();
        }
        return self::$instance;
    }
    public function connect(){
        if(self::$instance){
            return $this->conn;
        }else {
            DBConnector::getInstance();
            return $this->connect();
        }
    }
}

