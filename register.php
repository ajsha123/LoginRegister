<?php

// include 'config.php';

// if(isset($_POST['submit'])){

//    $name = mysqli_real_escape_string($conn, $_POST['name']);
//    $email = mysqli_real_escape_string($conn, $_POST['email']);
//    $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
//    $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));
//    $image = $_FILES['image']['name'];
//    $image_size = $_FILES['image']['size'];
//    $image_tmp_name = $_FILES['image']['tmp_name'];
//    $image_folder = 'uploaded_img/'.$image;

//    $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email' AND password = '$pass'") or die('query failed');

//    if(mysqli_num_rows($select) > 0){
//       $message[] = 'user already exist'; 
//    }else{
//       if($pass != $cpass){
//          $message[] = 'confirm password not matched!';
//       }elseif($image_size > 2000000){
//          $message[] = 'image size is too large!';
//       }else{
//          $insert = mysqli_query($conn, "INSERT INTO `user_form`(name, email, password, image) VALUES('$name', '$email', '$pass', '$image')") or die('query failed');

//          if($insert){
//             move_uploaded_file($image_tmp_name, $image_folder);
//             $message[] = 'registered successfully!';
//             header('location:login.php');
//          }else{
//             $message[] = 'registeration failed!';
//          }
//       }
//    }

// }




include 'config.php';

//Define a user class to encapsuualte registatrion logic
class user {
   private $conn;

   //constructor to initialize the database connection

   public function __construct($dbconn){
      $this->conn = $dbconn;

   }
   //function to handle user registration
   public function register($name, $email, $password, $cpassword,$imageFile){
      var_dump( $name, $email, $password, $cpassword,$imageFile);
      $name = mysqli_real_escape_string($this->conn, $name);
      $email = mysqli_real_escape_string($this-> conn,$email);
      $password = mysqli_real_escape_string($this->conn ,md5($password));
      $cpassword = mysqli_real_escape_string($this->conn ,md5($cpassword));
      $image = $imageFile['name'];
        $imageSize = $imageFile['size'];
        $imageTmpName = $imageFile['tmp_name'];
        $imageFolder = 'uploaded_img/' . $image;

         // Check if the user already exists
         $selectQuery = "SELECT * FROM `user_form` WHERE email = '$email' AND password = '$password'";
         $select = mysqli_query($this->conn, $selectQuery);
 
         if (mysqli_num_rows($select) > 0) {
             return 'User already exists!';
         } else {
             // Validate passwords
             if ($password != $cpassword) {
                 return 'Confirm password does not match!';
             }
 
             // Validate image size
             if ($imageSize > 2000000) {
                 return 'Image size is too large!';
             }
 
             // Insert user data into the database
             $insertQuery = "INSERT INTO `user_form` (name, email, password, role) VALUES ('$name', '$email', '$password', 'user')";
             $insert = mysqli_query($this->conn, $insertQuery);
 
             if ($insert) {
                 // Move the uploaded image to the folder
                 move_uploaded_file($imageTmpName, $imageFolder);
                 header('location:login.php'); // Redirect to the login page after successful registration
                 return 'Registered successfully!';
             } else {
                 return 'Registration failed!';
             }
         }

   }
   
}

// Handle form submission
if (isset($_POST['submit'])) {
   // create a new user object
   $user = new USer($conn);

   //call the register method with form data
   $message = $user->register(
      $_POST['name'],
      $_POST['email'],
      $_POST['password'],
      $_POST['cpassword'],
     $_FILES['image'],
   );
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
   <link rel="stylesheet" href="style.css">


</head>
<body>
   
<div class="form-container">

   <form action="" method="post" enctype="multipart/form-data">
      <h3>register now</h3>
      <?php
      // if(isset($message)){
      //    // foreach($message as $msg){
      //       echo '<div class="message">'.$message.'</div>';
      //    // }
      // }
      ?>
      <input type="text" name="name" placeholder="enter username" class="box" required>
      <input type="email" name="email" placeholder="enter email" class="box" required>
      <input type="password" name="password" placeholder="enter password" class="box" required>
      <input type="password" name="cpassword" placeholder="confirm password" class="box" required>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
      <input type="submit" name="submit" value="register now" class="btn">
      <p>already have an account? <a href="login.php">login now</a></p>
   </form>

</div>

</body>
</html>