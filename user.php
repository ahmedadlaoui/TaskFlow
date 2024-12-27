<?php 
class User
{
    private $full_name;
    private $tasks = [];

    public function __construct($full_name)
    {
        $this->full_name = $full_name;
    }
    public static function adduser($full_name)
    {
        try {
            $db_instance = database_connection::getinstance();
            $connection = $db_instance->getconnection();
        } catch (PDOException $e) {
            echo "Database not connected: " . $e->getMessage();
        }
        try {
            $stmt = $connection->prepare("INSERT INTO users (full_name) VALUES (:full_name)");
            $stmt->bindParam(':full_name', $full_name);
            $stmt->execute();
            header('location : index.php');
            exit;
        } catch (PDOException $e) {
            echo "Error adding user: " . $e->getMessage();
        }
    }
}
?>