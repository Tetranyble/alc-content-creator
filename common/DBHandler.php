<?php

require_once 'DBConnector.php';
trait DBHandler {
    private $conn;
    private $result;
    function __construct() {

        $this->conn = DBConnector::getInstance()->connect();
    }

    /**
     * Delete a single record
     * @param $query
     */
    public function delete($query){
        //$sql = "DELETE FROM ".$tabble_name." WHERE $column = ".$id;
        try {
            if($this->conn->query($query)){
                return true;
            }else{return false;}
        } catch (mysqli_sql_exception $th) {
            throw $th;
        }
    }
     
    /**
     *@method getAllRecords retrieves all record from table
     *@param $query The actual query params
     *@return $this->results The Data retrieved from the database
     */
    public function records($query) {
        try {
            $r = $this->conn->query($query);
            $results = array();
            while ($row = $r->fetch_assoc()) {
                $results[] = $row;
                }
            } catch (mysqli_sql_exception $e) {
            throw $e;
        }
        return $this->result = $results;
    }

    /**
     * @return $result
     * Creating new record
     * @param $obj array
     * @param $column_names array
     * @param $table_name string table name
     */
    public function insert($obj, $column_names, $table_name) {
        $c = array();
        foreach ($obj as $key => $value) {
            $c[$key] = $this->conn->real_escape_string($value);
        }
        //return var_dump($c);
        $keys = array_keys($c);
        $columns = '';
        $values = '';
        foreach($column_names as $desired_key){ // Check the obj received. If blank insert blank into the array.
           if(!in_array($desired_key, $keys)) {
                $$desired_key = '';
            }else{
                $$desired_key = $c[$desired_key];
            }
            $columns = $columns.$desired_key.',';
            $values = $values."'".$$desired_key."',";
        }
        
        try{
            $query = "INSERT INTO ".$table_name."(".trim($columns,',').") VALUES(".trim($values,',').")";
            if($this->conn->query($query)){
                $this->result = $this->conn->insert_id;
                $this->conn->free();
            }
        }catch(mysqli_sql_exception $e){
            
            $this->result = $e->getMessage();
        }finally{
            return $this->result;
            //$this->conn->close();
        }
    }
    public function update($obj, $column_names, $table_name, $record_id,$column_id) {
        try{
        $c = array();
        foreach ($obj as $key => $value) {
            $c[$key] = $this->conn->real_escape_string($value);
        }
        $keys = array_keys($c);
        $subquery='';
        foreach($column_names as $desired_key){ // Check the obj received. If blank insert blank into the array.
           if(!in_array($desired_key, $keys)) {
               $$desired_key = '';
           }else{
               $$desired_key = $c[$desired_key];
           }
            $subquery.=$desired_key.' = '."'".$$desired_key."', ";
        }
        $query="UPDATE $table_name SET ".rtrim(trim(substr($subquery,0,-1)), ',')." WHERE $column_id='$record_id'";
            return $this->result = $this->conn->query($query);
        }catch(mysqli_sql_exception $e){
            throw $e;
        }
    }
    /**
     * Returns a string with backslashes before characters that need to be escaped.
     * As required by MySQL and suitable for multi-byte character sets
     * Characters encoded are NUL (ASCII 0), \n, \r, \, ', ", and ctrl-Z.
     * In addition, the special control characters % and _ are also escaped,
     * suitable for all statements, but especially suitable for `LIKE`.
     *
     * @param string $string String to add slashes to
     * @return $string with `\` prepended to reserved characters
     *
     * @author Trevor Herselman
     */
    public function escape_charset($string) {
        if (function_exists('mb_ereg_replace')) {
            return mb_ereg_replace('[\x00\x0A\x0D\x1A\x22\x25\x27\x5C\x5F]', '\\\0', $string);
        } else {
            return preg_replace('~[\x00\x0A\x0D\x1A\x22\x25\x27\x5C\x5F]~u', '\\\$0', $string);
        }
    }
    public function escapeobj($data){
        $c = array();
        foreach ($data as $key => $value) {
            return $c[$key] = $this->escape($value);
        }

    }
    public function escape($value){
        return $this->conn->real_escape_string($value);
    }
    /**
     * Fetching single record
     */
    public function getOneRecord($query) {
        try {
            if($r = $this->conn->query($query.' LIMIT 1')){
                return $result = $r->fetch_assoc();
                //$this->conn->free();
            }

        } catch (mysli_sql_exception $e) {
            //log the error and continue normal operations
            throw $e;
        }
    }
}
?>
