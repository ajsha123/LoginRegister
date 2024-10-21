<?php
include 'config.php';
session_start();

// Restrict access to admin users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Handle flash messages
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Handle soft deletion of users
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("UPDATE `user_form` SET deleted_at = NOW() WHERE id = $id") or die($conn->error);
    $_SESSION['message'] = "User deleted successfully!";
    header("Location: admin.php");
    exit();
}

// Handle restoring of users
if (isset($_GET['restore'])) {
    $id = $_GET['restore'];
    $conn->query("UPDATE `user_form` SET deleted_at = NULL WHERE id = $id") or die($conn->error);
    $_SESSION['message'] = "User restored successfully!";
    header("Location: admin.php");
    exit();
}

// Handle adding and editing users
if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];

    if (isset($_POST['id']) && $_POST['id'] != '') {
        // Update existing user
        $id = $_POST['id'];
        $conn->query("UPDATE `user_form` SET name='$name', email='$email', password='$password', role='$role' WHERE id=$id") or die($conn->error);
        $_SESSION['message'] = "User updated successfully!";
    } else {
        // Add new user
        $conn->query("INSERT INTO `user_form` (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')") or die($conn->error);
        $_SESSION['message'] = "User added successfully!";
    }

    header("Location: admin.php");
    exit();
}

// Fetch all users who are not deleted
$result = $conn->query("SELECT * FROM `user_form` WHERE deleted_at IS NULL") or die($conn->error);

// Fetch all deleted users for restore option
$deletedUsers = $conn->query("SELECT * FROM `user_form` WHERE deleted_at IS NOT NULL") or die($conn->error);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .sidebar {
            min-width: 250px;
            height: 100vh;
            position: fixed;
            background-color: #198754;
            color: white;
            padding-top: 20px;
            transition: all 0.3s;
        }
        .sidebar img {
            border-radius: 50%;
        }
        .sidebar ul {
            list-style: none;
            padding-left: 0;
        }
        .sidebar ul li {
            padding: 10px;
        }
        .sidebar ul li a {
            text-decoration: none;
            color: white;
            font-weight: bold;
            transition: color 0.3s;
        }
        .sidebar ul li a:hover {
            color: #d1e7dd;
        }
        .content {
            margin-left: 270px;
            padding: 30px;
            transition: all 0.3s;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        }
        table {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav ms-auto">
                <a class="nav-item nav-link" href="home.php">Home</a>
                <a class="nav-item nav-link" href="#">Contacts</a>
                <a class="nav-item nav-link" href="#">About</a>
                <a class="nav-item nav-link " href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</nav>

<div class="sidebar">
    <div class="text-center mb-4">
        <img src="images/luffy.png" alt="Profile Image" width="100">
        <h4 class="mt-2"><?php echo $_SESSION['name']?></h4>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="#">Home</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="task.php">Task</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Users</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Orders</a>
        </li>
    </ul>

    <a href="logout.php">Logout</a>

</div>

<div class="content">
    <h2 class="mb-4">Welcome back, Admin! 😊</h2>
</head>
<body>
    <div class="container mt-5">
        <h2>Admin Dashboard</h2>
        <a href="logout.php" class="btn btn-danger mb-3">Logout</a>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= $message; ?></div>
        <?php endif; ?>

        <h3>All Users</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc() ): 
                    if($row['id'] == $_SESSION['user_id'])
                    {
                        $name = $row['name'];
                        // echo $name;
                        continue;
                    }
                    ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= $row['name']; ?></td>
                        <td><?= $row['email']; ?></td>
                        <td><?= $row['role']; ?></td>
                        <td>
                            <a href="admin.php?edit=<?= $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="admin.php?delete=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete();">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h3><?php echo isset($_GET['edit']) ? 'Edit User' : 'Add User'; ?></h3>
        <?php
        $id = '';
        $name = '';
        $email = '';
        $role = '';
        if (isset($_GET['edit'])) {
            $id = $_GET['edit'];
            $result = $conn->query("SELECT * FROM `user_form` WHERE id = $id") or die($conn->error);
           
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $name = $user['name'];
                $email = $user['email'];
                $role = $user['role'];
            }
        }
        ?>
        <form action="admin.php" method="POST">
            <input type="hidden" name="id" value="<?= $id; ?>">
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="<?= $name; ?>" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= $email; ?>" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-control" required>
                    <option value="admin" <?= ($role == 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="student" <?= ($role == 'student') ? 'selected' : ''; ?>>Student</option>
                </select>
            </div>
            <button type="submit" name="save" class="btn btn-success">Save</button>
        </form>

        <h3>Deleted Users</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $deletedUsers->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= $row['name']; ?></td>
                        <td><?= $row['email']; ?></td>
                        <td><?= $row['role']; ?></td>
                        <td>
                            <a href="admin.php?restore=<?= $row['id']; ?>" class="btn btn-warning btn-sm" onclick="return confirmRestore();">Restore</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
