<?php
class database_connection
{
    private static $instance = NULL;
    private  $connection;
    private  function __construct()
    {
        $servername = "localhost";
        $username = "root";
        $password = "06database@SM23";
        $dbname = "TaskFlow";
        $port = 3306;

        try {
            $this->connection = new PDO("mysql:host=$servername;dbname=$dbname;port=$port", $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'error connecting to the data base ' . $e->getMessage();
        }
    }

    public static function getinstance()
    {
        if (self::$instance === NULL) {
            self::$instance = new database_connection();
            return self::$instance;
        }
        return self::$instance;
    }
    public  function getconnection()
    {
        return $this->connection;
    }
    public function fetchAllUsers()
    {
        try {
            $stmt = $this->connection->prepare("SELECT * FROM users");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error fetching users: " . $e->getMessage();
            return [];
        }
    }
    public function fetchAlltasks()
    {
        try {
            $stmt = $this->connection->prepare("SELECT * FROM tasks");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error fetching tasks: " . $e->getMessage();
            return [];
        }
    }
}

?>