<?php include 'config.php';
class Database {
    // handle of the db connexion
    public $objDbc;
    private static $instance;
    public function __construct($dbname = '') {
        
        // building data source name from config
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME;
        // set data base handler with try catch
        try {
            $this->objDbc = new PDO($dsn, DB_USER, DB_PASS ,array(
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                    ));
        }
        catch (PDOException $e) {
            die("<h1 style='color:red;'>MySQL - Oops Some Thing Went Wrong </h1>");
        }
        // return instance of connection
        self::getInstance();
    }
    public function getInstance() {
        if (isset(self::$instance)) {
            $object         = __CLASS__;
            self::$instance = new $object;
            // echo "false";
            return self::$instance;
        }
    }
    public function __destruct() {
        unset($this->objDbc);
    }
}
?>

