<?php
include 'config.php';

// Adding a new task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_task'])) {
    $title = $_POST['task_title'];
    echo $title;
    $descriptions = $_POST['task_description'];
    $due_date = $_POST['due_date'];

    $sql = "INSERT INTO tasks (title, description, due_date) VALUES ('$title', '$descriptions', '$due_date')";
    $conn->query($sql);
}

// Assigning a task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign_task'])) {
    $student_id = $_POST['student'];
    $task_id = $_POST['task'];
    $assigned_date = date("Y-m-d");

    $sql = "INSERT INTO student_tasks (student_id, task_id, assigned_date) VALUES ('$student_id', '$task_id', '$assigned_date')";
    $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Admin Dashboard</title>
</head>
<body>
<div class="container mt-5">
    <h2>Add Task</h2>
    <form method="POST" >
        <div class="form-group">
            <label for="task_title">Task Title:</label>
            <input type="text" class="form-control" id="task_title" name="task_title" required>
        </div>
        <div class="form-group">
            <label for="task_description">Description:</label>
            <textarea class="form-control" id="task_description" name="task_description" required></textarea>
        </div>
        <div class="form-group">
            <label for="due_date">Due Date:</label>
            <input type="date" class="form-control" id="due_date" name="due_date" required>
        </div>
        <button type="submit" name="add_task" class="btn btn-primary">Add Task</button>
    </form>

    <h2 class="mt-5">Assign Task</h2>
    <form method="POST" >
        <div class="form-group">
            <label for="student">Student:</label>
            <select name="student" class="form-control" required>
                <?php
                $result = $conn->query("SELECT id, name FROM students");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="task">Task:</label>
            <select name="task" class="form-control" required>
                <?php
                $result = $conn->query("SELECT id, title FROM tasks");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['title']}</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" name="assign_task" class="btn btn-primary">Assign Task</button>
    </form>
</div>
</body>
</html>

