<?php

class database_connection
{
    private static $instance = NULL;
    private static $connection;
    private  function __construct()
    {
        $servername = "localhost";
        $username = "root";
        $password = "06database@SM23";
        $dbname = "TaskFlow";
        $port = 3306;


        $this->connection = new PDO("mysql:host=$servername;dbname=$dbname;port=$port", $username, $password);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getinstance()
    {
        if (self::$instance === NULL) {
            self::$instance = new database_connection();
            return self::$instance;
        }
        return self::$instance;
    }
    public static function getconnection()
    {
        if (self::$connection === NULL) {
            self::getinstance();
        }
        return self::$connection;
    }
}





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
    public function getDetails()
    {
        return [
            'task_ID' => $this->task_ID,
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
        ];
    }
    public function addtask()
    {
        global $connection;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createtask'])) {
          
            $title = trim($_POST['task-title']);
            $description = trim($_POST['task-description']);
            $type = $_POST['task-type'];
            $assigned_to = $_POST['assigned-user'];
            $status = 'Pending';
    
            try {
                $query = "INSERT INTO tasks (title, description, type, assigned_to, status) VALUES (:title, :description, :type, :assigned_to, :status)";
                $stmt = $connection->prepare($query);
    
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':type', $type);
                $stmt->bindParam(':assigned_to', $assigned_to);
                $stmt->bindParam(':status', $status);
    
                $stmt->execute();
                echo "Task added successfully!";
            } catch (PDOException $e) {
                echo "Error adding task: " . $e->getMessage();
            }
        }
    }
    

    public function assignTo(User $user)
    {
        $this->assigned_to = $user;
        $user->addTask($this);
    }
    public function changestatus($status)
    {
        $this->status = $status;
    }
    public function getassignedTo()
    {
        return $this->assigned_to;
    }
}


class User
{
    private $full_name;
    private $tasks = [];

    public function __construct($full_name)
    {
        $this->full_name = $full_name;
    }
    public function addTask(Task $task)
    {
        $this->tasks[] = $task;
    }
    public function gettasks()
    {
        return $this->tasks;
    }
}

?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Group View</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="font-roboto bg-gray-100">

    <header class="bg-blue-600 text-white p-4">
        <div class="max-w-7xl mx-auto flex items-center justify-arround">
            <h1 class="text-2xl font-bold">TaskFlow</h1>
        </div>
    </header>

    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <!-- Modal -->
        <div class="bg-white shadow-md rounded-lg p-6 w-full max-w-md relative">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Add Member</h2>
            <form action="#" method="POST" class="space-y-4">
                <!-- First Name -->
                <div>
                    <label for="first-name" class="block text-sm font-medium text-gray-700">Full name</label>
                    <input type="text" id="first-name" name="first-name" placeholder="Enter full name"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                </div>
                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Add
                    </button>
                </div>
            </form>

            <!-- Close Button -->
            <button id="closeModal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                ✖
            </button>
        </div>
    </div>

    <div class="w-full max-w-7xl mx-auto p-4">
        <!-- Container for the Task Group View -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <!-- Task Group Title Section -->
                <h2 class="text-2xl font-semibold text-gray-800">Tasks table</h2>
            </div>

            <!-- Filters Section -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">

                <!-- Left Filter Options -->
                <div class="flex space-x-4">
                    <div class="relative">
                        <select class="block w-full py-2 px-3 bg-white border border-gray-300 rounded-md text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>All tasks</option>
                            <option>My tasks</option>
                        </select>
                    </div>
                </div>
                <button id="openModal" class="bg-green-600 text-white text-sm px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Add New Member
                </button>

                <!-- Right Filter Options -->
            </div>

            <div class="overflow-x-auto p-4">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Task</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Type of Task</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Assigned To</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Status</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        <form class="border-b border-gray-200">
                            <td class="px-4 py-3 text-sm text-gray-700">Task 2</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <select class="block w-full py-1 px-2 border border-gray-300 rounded-md text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="basic">Basic</option>
                                    <option value="bug">Bug</option>
                                    <option value="feature" selected>Feature</option>
                                </select>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <select class="block w-full py-1 px-2 border border-gray-300 rounded-md text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="John Doe">John Doe</option>
                                    <option value="Jane Smith">Jane Smith</option>
                                    <option value="Mark Johnson" selected>Mark Johnson</option>
                                </select>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <select class="block w-full py-1 px-2 border border-gray-300 rounded-md text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="completed" selected>Completed</option>
                                    <option value="in-progress">In Progress</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </td>
                            <button name="saveButton" type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-blue-500 mb-8 ">
                                Save
                            </button>

                        </form>
            </div>
        </div>
        </tbody>
        </table>
    </div>
    </div>


    <div class="w-full max-w-3xl mx-auto p-4">
        <div class="bg-white rounded-lg shadow-lg">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-gray-800">Task creation</h2>
            </div>

            <div class="p-4">
                <form action="index.php" method="POST">
                    <div class="space-y-4">
                        <div class="flex flex-col">
                            <label for="task-title" class="text-sm font-medium text-gray-700">Title</label>
                            <input type="text" id="task-title" name="task-title" class="mt-1 p-3 border border-gray-300 rounded-md text-sm w-full" placeholder="Entrez le titre de la tâche" required>
                        </div>

                        <div class="flex flex-col">
                            <label for="task-description" class="text-sm font-medium text-gray-700">Description</label>
                            <textarea id="task-description" name="task-description" rows="4" class="mt-1 p-3 border border-gray-300 rounded-md text-sm w-full" placeholder="Entrez la description de la tâche" required></textarea>
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        <div class="flex flex-col">
                            <label for="task-type" class="text-sm font-medium text-gray-700">Type of task</label>
                            <select id="task-type" name="task-type" class="mt-1 p-3 border border-gray-300 rounded-md text-sm w-full" required>
                                <option value="basic">Basic task</option>
                                <option value="bug">Bug</option>
                                <option value="feature">Feature</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        <div class="flex flex-col">
                            <label for="assigned-user" class="text-sm font-medium text-gray-700">Assigne to</label>
                            <select id="assigned-user" name="assigned-user" class="mt-1 p-3 border border-gray-300 rounded-md text-sm w-full" required>
                                <option value="user1">Me</option>
                                <option value="user2">Jane Smith</option>
                                <option value="user3">Mark Johnson</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" name="createtask" class="bg-blue-600 text-white text-sm px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Create
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const openModalButton = document.getElementById('openModal');
        const closeModalButton = document.getElementById('closeModal');
        const overlay = document.getElementById('overlay');
        openModalButton.addEventListener('click', () => {
            overlay.classList.remove('hidden');
        });
        closeModalButton.addEventListener('click', () => {
            overlay.classList.add('hidden');
        });
        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) {
                overlay.classList.add('hidden');
            }
        });
    </script>
</body>

</html>