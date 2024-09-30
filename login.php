<?php

// include 'config.php';
// session_start();

// if(isset($_POST['submit'])){

//    $email = mysqli_real_escape_string($conn, $_POST['email']);
//    $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

//    $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email' AND password = '$pass'") or die('query failed');

//    if(mysqli_num_rows($select) > 0){
//       $row = mysqli_fetch_assoc($select);
//       $_SESSION['user_id'] = $row['id'];
//       header('location:home.php');
//    }else{
//       $message[] = 'incorrect email or password!';
//    }

// }

include 'config.php';
session_start();

// Define the User class to handle login functionality
class User {
   private $conn;

   // Constructor to initialize the database connection
   public function __construct($dbConn) {
       $this->conn = $dbConn;
   }

   // Function to handle user login
   public function login($email, $password) {
       $email = mysqli_real_escape_string($this->conn, $email);
       $passwordHash = md5($password);
       echo "Hashed Password: " . $passwordHash . "<br>";
      //  $passwordHash = mysqli_real_escape_string($this->conn, md5($password)); // Encrypt the password

       // Query the database to find the user with the provided email and password
       $query = "SELECT * FROM `user_form` WHERE email = '$email' AND password = '$passwordHash'";
       $result = mysqli_query($this->conn, $query);

       if (mysqli_num_rows($result) > 0) {
           $row = mysqli_fetch_assoc($result);
           $_SESSION['user_id'] = $row['id']; // Set the session with the user ID
           header('location:home.php'); // Redirect to the home page after login
       } else {
           return 'Incorrect email or password!';
       }
   }
}

// Handle form submission
if (isset($_POST['submit'])) {
   // Create a new User object
   $user = new User($conn);

   // Call the login method with the form data
   $message = $user->login($_POST['email'], $_POST['password']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>login</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
   <link rel="stylesheet" href="style.css">

</head>
<body>
   
<div class="form-container">

   <form action="" method="post" enctype="multipart/form-data">
      <h3>login now</h3>
      <?php
      if(isset($message)){
         
            echo '<div class="message">'.$message.'</div>';
         }
      
      ?>
      <input type="email" name="email" placeholder="enter email" class="box" required>
      <input type="password" name="password" placeholder="enter password" class="box" required>
      <input type="submit" name="submit" value="login now" class="btn">
      <p>don't have an account? <a href="register.php">regiser now</a></p>
   </form>

</div>

</body>
</html>