<?php

include 'dbcon.php';
include 'task.php';
include 'user.php';

// Handle POST requests for task creation, user addition, or status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createtask'])) {
    // Add the new task to the database
    $task = new Task(null, $_POST['task-type'], trim($_POST['task-title']), trim($_POST['task-description']), 'Pending');
    $task->addtask(trim($_POST['task-title']), trim($_POST['task-description']), $_POST['task-type'], $_POST['assigned-user'], 'Pending');
    
    // Redirect to prevent form resubmission (you can use AJAX later to handle it without a full page reload)
    // header('Content-Type: application/json');
    // header('Location: index.php');
    exit();
}

// Handle GET requests for fetching tasks as JSON (for AJAX-like behavior)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch_tasks'])) {

    try {
        $db_instance = database_connection::getinstance();
        $connection = $db_instance->getconnection();
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
        exit();
    }

    // Fetch all tasks from the database
  
    $tasks = $db_instance->fetchAlltasks();
      header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'tasks' => $tasks
    ]);
    exit();  // Ensure the script stops here if we're returning JSON
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
        <button id="closeModal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600"> ✖ </button>
    </div>
</div>

<div class="w-full max-w-7xl mx-auto p-4">
    <div class="bg-white rounded-lg shadow-lg">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-2xl font-semibold text-gray-800">Tasks table</h2>
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
                <tbody id="task-table-body">
                    <!-- Tasks will be dynamically inserted here by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="w-full max-w-3xl mx-auto p-4">
    <div class="bg-white rounded-lg shadow-lg">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-2xl font-semibold text-gray-800">Task creation</h2>
        </div>

        <div class="p-4">
            <form id="create-task-form" action="index.php" method="POST">
                <div class="space-y-4">
                    <div class="flex flex-col">
                        <label for="task-title" class="text-sm font-medium text-gray-700">Title</label>
                        <input type="text" id="task-title" name="task-title" class="mt-1 p-3 border border-gray-300 rounded-md text-sm w-full" placeholder="Enter task title" required>
                    </div>

                    <div class="flex flex-col">
                        <label for="task-description" class="text-sm font-medium text-gray-700">Description</label>
                        <textarea id="task-description" name="task-description" rows="4" class="mt-1 p-3 border border-gray-300 rounded-md text-sm w-full" placeholder="Enter task description" required></textarea>
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
                        <label for="assigned-user" class="text-sm font-medium text-gray-700">Assign to</label>
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
    // Fetch tasks when the page loads
    function fetchTasks() {
        fetch('index.php?fetch_tasks=true')  // Send GET request with query string to fetch tasks as JSON
            .then(response => response.json())  // Parse the JSON response
            .then(data => {
                if (data.status === 'success') {
                    const tasks = data.tasks;  // Get the tasks array

                    const tbody = document.querySelector('#task-table-body');
                    tbody.innerHTML = '';  // Clear existing rows

                    // Loop through the tasks and insert them into the table
                    tasks.forEach(task => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="px-4 py-3 text-sm text-gray-700">${task.title}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">${task.type}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">${task.assigned_to}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">${task.status}</td>
                            <td class="px-4 py-3 text-sm text-gray-700"><a href="taskdetails.php?task_ID=${task.task_ID}" target="_blank" class="text-blue-500 hover:underline">View Details</a></td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            })
            .catch(error => console.error('Error fetching tasks:', error));
    }

    // Event listener to handle form submission
    const form = document.getElementById('create-task-form');
    form.addEventListener('submit', function (event) {
        event.preventDefault();  // Prevent the default form submission

        // Serialize form data
        const formData = new FormData(form);

        fetch('index.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                fetchTasks();  // Refresh the task list after adding the new task
                form.reset();  // Reset the form fields
            } else {
                alert('Error adding task!');
            }
        })
        .catch(error => {
            console.error('Error submitting task form:', error);
        });
    });

    // Fetch tasks when the page loads
    window.onload = fetchTasks;
</script>

</body>
</html>
