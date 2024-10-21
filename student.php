<?php
include 'config.php';
session_start();
$student_id = $_SESSION['user_id'];

// Redirect to login page if user is not logged in or does not have the correct role
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();

    
}

// Get the logged-in student's ID
$student_id = $_SESSION['user_id'];

// Fetch tasks assigned to this student
$sql = "SELECT tasks.title, tasks.description, tasks.due_date 
        FROM student_tasks 
        JOIN tasks ON student_tasks.task_id = tasks.id 
        WHERE student_tasks.student_id = $student_id";
// $result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Page</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Student Portal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav ms-auto">
                    <a class="nav-item nav-link active" href="#">Home</a>
                    <a class="nav-item nav-link" href="#">Contacts</a>
                    <a class="nav-item nav-link" href="logout.php">Logout</a>
                    <a class="nav-item nav-link" href="#">About</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Wrapper -->
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar bg-success text-white p-3" style="min-width: 250px;">
            <div class="text-center mb-4">
                <img src="images/monkey.jpg" alt="Profile Image" class="rounded-circle mb-3" width="80">
                <h5>Student</h5>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">Contact</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">Fee</a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="content p-4 w-100">
            <h2>Welcome back, Student! 😊</h2>
            <p>Time: <!-- You can add dynamic time here, e.g., using JavaScript or PHP --></p>
            <a class="btn btn-danger mt-3" href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-skAcpIdNQKV+e+12Xh2LF6j6jBIeIG5j6qq6t5l5Y5Uh0Boqne+1WzCflMJ1aI4F"
        crossorigin="anonymous"></script>


       < div class="container mt-5">
    <h2>My Tasks</h2>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Task Title</th>
            <th>Description</th>
            <th>Due Date</th>
            <th>Assigned Date</th>
        </tr>
        </thead>
        <tbody>
        <?php
        // $sql = "SELECT tasks.title, tasks.description, tasks.due_date, student_tasks.assigned_date 
        //         FROM student_tasks 
        //         JOIN tasks ON student_tasks.task_id = tasks.id 
        //         WHERE student_tasks.student_id = '$student_id'";

        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['title']}</td>
                    <td>{$row['description']}</td>
                    <td>{$row['due_date']}</td>
                    <td>{$row['assigned_date']}</td>
                  </tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
</body>

</html>
