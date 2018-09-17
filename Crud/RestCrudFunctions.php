<?php include 'dbconn.php';
class RestCrudFunctions extends Database {

    function __construct() {
        parent::__construct();
        $this->objDbc->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->connection = $this->objDbc;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    function select_data($query,$array = null) {
        try {
            $queryData = $this->connection->prepare($query);
            $queryData->execute($array);
            $res = $queryData->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e) {
            echo 'Select connection failed: ' . $e->getMessage();
            die;
        }
        return $res;
    }


    /**
     * Expect one row from a query and return it if found or false if not.
     *
     * @param $query
     * @param null $array
     * @return mixed
     */
    function select_one($query, $array = null) {
        $result = $this->select_data($query, $array);
        if(empty($result)) {
            return false;
        }
        
        return $result[0];
    }
    
    /**
     * Function to insert data into audit log
     * $table   {STRING}    tablename of database
     * $insert  {ARRAY}     columnn & values
     */
    public function insert_data($table, $insert) {
        try {
            $inputs = array();
            $ins = array();
            foreach ($insert as $key => $value) {
                $inputs[] = $key;
                $ins[] = "'" . addslashes($value) . "'";
            }
            $fieldlist = implode(', ', $inputs);
            $qs = implode(', ', $ins);
            $sql = ("INSERT IGNORE INTO $table($fieldlist) values($qs)");
            
            $queryData = $this->connection->prepare($sql);
            $queryData->execute();
            $res = $this->connection->lastInsertId(); 
        }
        catch(PDOException $e) {
            echo 'Insert failed, Error code :' . $e->getMessage();
            die;
        }
        return $res;
    }
    
    function create_id($table, $col) {
        $query = "SELECT id FROM $table WHERE $col=(SELECT MAX($col) FROM $table)";
        $queryData = $this->connection->prepare($query);
        $queryData->execute();
        $res = $queryData->fetchAll(PDO::FETCH_ASSOC);
        $decodeID = base64_decode($res[0]['id']);
        $explode_id = explode('_', $decodeID);
        if ($explode_id[1] != '') {
            $cnt = count($explode_id);
            if ($cnt == 3) {
                $int = (int)$explode_id[2];
            } else {
                $int = (int)$explode_id[1];
            }
            $new_id1 = $int + 1;
            $new_id = $table . "_" . $new_id1;
        } 
        else {
            $new_id = $table . "_1";
        }
        return base64_encode(strtolower($new_id));
    }

    function get_stock_id($phy_id){
        $query = "SELECT id,product_target,target_completed,target_pending,reorder_point,safety_stock FROM provider_stock where provider_id = '$phy_id' order by id desc limit 1";
        $queryData = $this->connection->prepare($query);
        $queryData->execute();
        return $res = $queryData->fetchAll(PDO::FETCH_ASSOC); 
    }

    function update_data($id, $data, $table) {
        try {
            foreach ($data as $key => $value) {
                $valueArray[] = $value;
                $inputs[] = $key;
                $updates[] = "$key =" . "'" . addslashes($value) . "'";
            }
            
            $implodeArray = implode(', ', $updates);
            $sql = ("UPDATE $table SET $implodeArray WHERE id='$id'");

            $queryData = $this->connection->prepare($sql);
            $queryData->execute();
        }
        catch(PDOException $e) {
            echo 'Update - Oops Something went Wrong ' . $e->getMessage();
            die;
        }
    }

    function update_query($condition, $data, $table) {
        try {
            foreach ($data as $key => $value) {
                $valueArray[] = $value;
                $inputs[] = $key;
                $updates[] = "$key =" . "'" . addslashes($value) . "'";
            }
            
            $implodeArray = implode(', ', $updates);
            $sql = ("UPDATE $table SET $implodeArray WHERE $condition");
            
            $queryData = $this->connection->prepare($sql);
            $queryData->execute();
        }
        catch(PDOException $e) {
            echo 'Update - Oops Something went Wrong ' . $e->getMessage();
            die;
        }
    }
    
    function update_query_data($query) {
        try {
            $queryData = $this->connection->prepare($query);
            $queryData->execute();
            
            // $res = $queryData->fetchAll(PDO::FETCH_ASSOC);
            return array("error"=>false,"msg"=>"success");
            
        }
        catch(PDOException $e) {
            echo 'Update query connection failed: ' . $e->getMessage();
            die;
        }
        return $res;
    }
    
    function delete_data($id, $table) {
        try {
            $query = "DELETE FROM $table WHERE id='$id'";
            $queryData = $this->connection->prepare($query);
            $queryData->execute();
            
            // $res = $queryData->fetchAll(PDO::FETCH_ASSOC);
            
            
        }
        catch(PDOException $e) {
            echo 'Delete - Oops Something went Wrong  ' . $e->getMessage();
            die;
        }
        return $res;
    }
    
    function create_log_file($method) {
        $ip_address = $this->getUserIP();
        $log = $method . $ip_address . ' - ' . date("F j, Y, g:i:s a e") . PHP_EOL;
        $filename = DIR_SITE_ROOT . DS . "log" . DS . "log_intranet_" . date("j-n-Y") . ".txt";
        chmod($filename, 0755);
        chown($filename, "root");
        $fh = fopen($filename, 'a') or die("can't open file");;
        fwrite($fh, $log);
        fclose($fh);
    }
    
    function getUserIP() {

        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = $_SERVER['REMOTE_ADDR'];
        
        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } 
        else if (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } 
        else {
            $ip = $remote;
        }
        
        return $ip;
    }
    
    function audit_log($method, $query, $table_name, $response = '', $email = 'Guest User: ') {
        date_default_timezone_set('America/Chicago');
        $insert = array();
        $browser = get_browser(null, true);
        $browser_version = $browser['parent'];
        $browser_platform = $browser['platform'];
        $ip_address = $this->getUserIP();
        $email_addr = $_SESSION['ses_csr_email'] ? $_SESSION['ses_csr_email'] : $email;
        $log = $email_addr . ' - ' . date("F j, Y, g:i:s a e") . ' - ' . $browser_version . ' - ' . $browser_platform . ' - ' . $ip_address . ' - ' . $method . ' - ' . $query . ' - ' . $table_name . ' - ' . $response . PHP_EOL;
        
        $table = "int_audit_log";
        $insert['description']  = $log;
        $insert['created_date'] = date('Y-m-d_H-i-s');
        $this->insert_data($table, $insert);
    }
}
?>