<?php

include 'task.php';
include 'dbcon.php';

$current_task_id = $_GET['task_ID'];
$task_instance = new Task(null, null, null, null);
$task_details = $task_instance->fetchTaskById($current_task_id);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Task Details</h1>
        <div class="bg-gray-100 p-4 rounded-md shadow-md">
            <p><strong>Title:</strong> <?php echo htmlspecialchars($task_details['title']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($task_details['description']); ?></p>
            <p><strong>Type:</strong> <?php echo htmlspecialchars($task_details['type']); ?></p>
            <p><strong>Assigned To:</strong> <?php echo htmlspecialchars($task_details['assigned_to']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($task_details['status']); ?></p>
        </div>
        <a href="index.php" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
            Back to Tasks
        </a>
    </div>
</body>

</html>