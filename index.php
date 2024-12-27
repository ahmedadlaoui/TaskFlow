<?php

include 'dbcon.php';
include 'task.php';
include 'user.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createtask'])) {
    $task = new Task(null, $_POST['task-type'], trim($_POST['task-title']), trim($_POST['task-description']), 'Pending');
    $task->addtask(trim($_POST['task-title']), trim($_POST['task-description']), $_POST['task-type'],  $_POST['assigned-user'], 'Pending');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['full-name'])) {
    $newuser = new user($_POST['full-name']);
    $newuser->adduser($_POST['full-name']);
}
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['savestatus'])){
    $task_to_update = $_POST['task_ID'];
    $updated_status = $_POST['newstatus'];

    $task_instance = new Task(null, null, null, null);
    $task_instance->changestatus($task_to_update, $updated_status);
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
        <div class="bg-white shadow-md rounded-lg p-6 w-full max-w-md relative">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Add Member</h2>
            <form action="index.php" method="POST" class="space-y-4">
                <div>
                    <label for="first-name" class="block text-sm font-medium text-gray-700">Full name</label>
                    <input type="text" id="first-name" name="full-name" placeholder="Enter full name"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                </div>

                <button type="submit"
                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-blue-500 ">
                    Add
                </button>
            </form>
            <button id="closeModal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                ✖
            </button>
        </div>
    </div>

    <div class="w-full max-w-7xl mx-auto p-4">
        <div class="bg-white rounded-lg shadow-lg">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">

                <h2 class="text-2xl font-semibold text-gray-800">Tasks table</h2>
            </div>

            <div class="flex items-center justify-between p-4 border-b border-gray-200">

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

            </div>

            <div class="overflow-x-auto p-4">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Task</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Type of Task</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Assigned To</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Status</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $db_instance = database_connection::getinstance();
                            $connection = $db_instance->getconnection();
                        } catch (PDOException $e) {
                            echo "Database not connected: " . $e->getMessage();
                        }
                        $tasks = $db_instance->fetchAlltasks();

                        foreach ($tasks as $task) :
                        ?>
                            <tr class="border-b border-gray-200">

                                <td class="px-4 py-3 text-sm text-gray-700"><?php echo htmlspecialchars($task['title']); ?></td>

                                <td class="px-4 py-3 text-sm text-gray-700"><?php echo htmlspecialchars($task['type']); ?></td>

                                <td class="px-4 py-3 text-sm text-gray-700"><?php echo htmlspecialchars($task['assigned_to']); ?></td>

                                <td class="px-4 py-3 text-sm text-gray-700">
                                    <form action="index.php" method="POST">
                                        <input type="hidden" name="task_ID" value="<?php echo $task['task_ID']; ?>">
                                        <div class="flex items-center space-x-4">
                                            <select name="newstatus" class="block py-1 px-2 border border-gray-300 rounded-md text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="Pending" <?php echo $task['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="In Progress" <?php echo $task['status'] === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                                <option value="Completed" <?php echo $task['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                            </select>
                                            <button name="savestatus" type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                Save
                                            </button>
                                        </div>

                                    </form>
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700">
                                    <a href="taskdetails.php?task_ID=<?php echo $task['task_ID']; ?>" target="_blank" class="text-blue-500 hover:underline">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
                                <?php
                                try {
                                    $db_instance = database_connection::getinstance();
                                    $connection = $db_instance->getconnection();
                                } catch (PDOException $e) {
                                    echo "Database not connected: " . $e->getMessage();
                                }
                                $users = $db_instance->fetchAllUsers();
                                foreach ($users as $user) {
                                    echo '<option value="' . $user['full_name'] . '">' . $user['full_name'] . '</option>';
                                }
                                ?>

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