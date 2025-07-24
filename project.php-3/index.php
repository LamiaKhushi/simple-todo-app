<?php
const TASKS_FILE = 'tasks.json';

// Load tasks from file
function loadTasks(): array {
    if (!file_exists(TASKS_FILE)) {
        return [];
    }

    $data = file_get_contents(TASKS_FILE);
    return $data ? json_decode($data, true) : [];
}

// Save tasks to file
function saveTasks(array $tasks): void {
    file_put_contents(TASKS_FILE, json_encode($tasks, JSON_PRETTY_PRINT));
}

// Handle POST actions
$tasks = loadTasks();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add Task
    if (isset($_POST['task']) && !empty(trim($_POST['task']))) {
        $tasks[] = [
            'task' => htmlspecialchars(trim($_POST['task'])),
            'done' => false
        ];
        saveTasks($tasks);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // Mark Done/Undone
    if (isset($_POST['toggle'])) {
        $index = (int) $_POST['toggle'];
        if (isset($tasks[$index])) {
            $tasks[$index]['done'] = !$tasks[$index]['done'];
            saveTasks($tasks);
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // Delete Task
    if (isset($_POST['delete'])) {
        $index = (int) $_POST['delete'];
        if (isset($tasks[$index])) {
            array_splice($tasks, $index, 1);
            saveTasks($tasks);
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Simple To-Do App</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 30px auto;
        }
        .done {
            text-decoration: line-through;
            color: gray;
        }
        form.inline {
            display: inline;
        }
    </style>
</head>
<body>
    <h1>My To-Do List</h1>

    <!-- Add Task Form -->
    <form method="POST">
        <input type="text" name="task" placeholder="Add new task" required>
        <button type="submit">Add</button>
    </form>

    <ul>
        <?php foreach ($tasks as $index => $task): ?>
            <li>
                <form method="POST" class="inline">
                    <button name="toggle" value="<?= $index ?>" style="background:none; border:none; cursor:pointer;">
                        <span class="<?= $task['done'] ? 'done' : '' ?>">
                            <?= $task['task'] ?>
                        </span>
                    </button>
                </form>
                <form method="POST" class="inline" style="margin-left: 10px;">
                    <button name="delete" value="<?= $index ?>" onclick="return confirm('Are you sure?');">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
