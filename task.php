<?php 
class Task
{
    private $task_ID;
    private $assigned_to;
    private $type;
    private $title;
    private $description;
    private $status;

    public function __construct($task_ID, $type, $title, $description, $status = 'Pending')
    {
        $this->task_ID = $task_ID;
        $this->type = $type;
        $this->title = $title;
        $this->description = $description;
        $this->status = $status;
    }
    public function addtask($title, $description, $type, $assigned_to, $status)
    {
        try {
            $db_instance = database_connection::getinstance();
            $connection = $db_instance->getconnection();
        } catch (PDOException $e) {
            echo "Database not connected: " . $e->getMessage();
        }

        try {

            $stmt = $connection->prepare("INSERT INTO tasks (title, description, type, assigned_to, status) VALUES (:title, :description, :type, :assigned_to, :status)");
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':assigned_to', $assigned_to);
            $stmt->bindParam(':status', $status);
            $stmt->execute();
            header("Location: index.php?1");
            exit();
        } catch (PDOException $e) {
            echo "Error adding task: " . $e->getMessage();
        }
    }


    public function changestatus($task_ID, $status)
    {
        try {
            $db_instance = database_connection::getinstance();
            $connection = $db_instance->getconnection();
        } catch (PDOException $e) {
            echo "Database not connected: " . $e->getMessage();
        }
        try {

            $stmt = $connection->prepare("UPDATE tasks SET status = :newstatus WHERE task_ID = :task_idtm ");
            $stmt->bindParam(':newstatus', $status);
            $stmt->bindParam(':task_idtm', $task_ID);
            $stmt->execute();
            header("Location: index.php?1");
            exit();
        } catch (PDOException $e) {
            echo "failed to update status: " . $e->getMessage();
        }
    }
    public function fetchTaskById($task_ID)
{
    try {
        $db_instance = database_connection::getinstance();
        $connection = $db_instance->getconnection();
    } catch (PDOException $e) {
        echo "Database not connected: " . $e->getMessage();
    }
    try {
        $stmt = $connection->prepare("SELECT * FROM tasks WHERE task_ID = :task_ID");
        $stmt->bindParam(':task_ID', $task_ID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching task: " . $e->getMessage();
        return null;
    }
}
}

?>