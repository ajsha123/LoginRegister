<?php

// include 'config.php';
// session_start();
// $user_id = $_SESSION['user_id'];


// if(isset($_POST['update_profile'])){

//    $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
//    $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);

//    mysqli_query($conn, "UPDATE `user_form` SET name = '$update_name', email = '$update_email' WHERE id = '$user_id'") or die('query failed');

//    $old_pass = $_POST['old_pass'];
//    $update_pass = mysqli_real_escape_string($conn, md5($_POST['update_pass']));
//    $new_pass = mysqli_real_escape_string($conn, md5($_POST['new_pass']));
//    $confirm_pass = mysqli_real_escape_string($conn, md5($_POST['confirm_pass']));

//    if(!empty($update_pass) || !empty($new_pass) || !empty($confirm_pass)){
//       if($update_pass != $old_pass){
//          $message[] = 'old password not matched!';
//       }elseif($new_pass != $confirm_pass){
//          $message[] = 'confirm password not matched!';
//       }else{
//          mysqli_query($conn, "UPDATE `user_form` SET password = '$confirm_pass' WHERE id = '$user_id'") or die('query failed');
//          $message[] = 'password updated successfully!';
//       }
//    }

//    $update_image = $_FILES['update_image']['name'];
//    $update_image_size = $_FILES['update_image']['size'];
//    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
//    $update_image_folder = 'uploaded_img/'.$update_image;

//    if(!empty($update_image)){
//       if($update_image_size > 2000000){
//          $message[] = 'image is too large';
//       }else{
//          $image_update_query = mysqli_query($conn, "UPDATE `user_form` SET image = '$update_image' WHERE id = '$user_id'") or die('query failed');
//          if($image_update_query){
//             move_uploaded_file($update_image_tmp_name, $update_image_folder);
//          }
//          $message[] = 'image updated succssfully!';
//       }
//    }

// }




include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];
echo $user_id;
class User {
    private $conn;
    private $user_id;

    // Constructor to initialize the database connection and user ID
    public function __construct($dbConn, $user_id) {
        $this->conn = $dbConn;
        $this->user_id = $user_id;
    }

    // Fetch the user's data from the database
    public function getUserData() {
        $query = "SELECT * FROM `user_form` WHERE id = '$this->user_id'";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_array($result);
    }

    // Update name and email
    public function updateProfile($name, $email) {
        $query = "UPDATE `user_form` SET name = '$name', email = '$email' WHERE id = '$this->user_id'";
        mysqli_query($this->conn, $query) or die('Query failed');
    }

    // Update password
    public function updatePassword($old_pass, $new_pass, $confirm_pass) {
        if (!empty($new_pass) && !empty($confirm_pass)) {
            // Verify the old password
            $hashed_input = md5($old_pass);
            if ($hashed_input != $this->getOldPassword()) {
                return 'Old password does not match!';
            } elseif ($new_pass != $confirm_pass) {
                return 'Confirm password does not match!';
            } else {
                $encrypted_new_pass = md5($confirm_pass);
                $query = "UPDATE `user_form` SET password = '$encrypted_new_pass' WHERE id = '$this->user_id'";
                mysqli_query($this->conn, $query) or die('Query failed');
                return 'Password updated successfully!';
            }
        }
        return null;
    }

    // Fetch the old password from the database
    public function getOldPassword() {
        $user_data = $this->getUserData();
        return $user_data['password'];
    }

    // Update image
    public function updateImage($image_name, $image_tmp_name, $image_size, $image_folder) {
        if (!empty($image_name)) {
            if ($image_size > 2000000) {
                return 'Image size is too large!';
            } else {
                $sanitized_image_name = basename($image_name);
                $query = "UPDATE `user_form` SET image = '$sanitized_image_name' WHERE id = '$this->user_id'";
                $image_update_query = mysqli_query($this->conn, $query);
                if ($image_update_query) {
                    move_uploaded_file($image_tmp_name, $image_folder);
                    return 'Image updated successfully!';
                }
            }
        }
        return null;
    }
}

// Handle form submission for profile update
if (isset($_POST['update_profile'])) {
    $user = new User($conn, $user_id);
   echo $user_id;
    // Update name and email
    $name = mysqli_real_escape_string($conn, $_POST['update_name']);
    $email = mysqli_real_escape_string($conn, $_POST['update_email']);
    $user->updateProfile($name, $email);

    // Update password
    $old_pass = $_POST['old_pass']; // No hashing needed here, we already store a hashed password
    $new_pass = mysqli_real_escape_string($conn, $_POST['new_pass']);
    $confirm_pass = mysqli_real_escape_string($conn, $_POST['confirm_pass']);
    $password_message = $user->updatePassword($old_pass, $new_pass, $confirm_pass);

    // Update image
    $image = $_FILES['update_image']['name'];
    $image_size = $_FILES['update_image']['size'];
    $image_tmp_name = $_FILES['update_image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;
    $image_message = $user->updateImage($image, $image_tmp_name, $image_size, $image_folder);

    // Display messages
    if ($password_message) {
        $message[] = $password_message;
    }
    if ($image_message) {
        $message[] = $image_message;
    }
}

// Fetch user data for display in the form
$user = new User($conn, $user_id);
$user_data = $user->getUserData();

?>




<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>update profile</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
   <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="update-profile">

   <?php
      $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$user_id'") or die('query failed');
      if(mysqli_num_rows($select) > 0){
         $fetch = mysqli_fetch_assoc($select);
      }
   ?>

   <form action="" method="post" enctype="multipart/form-data">
      <?php
         if($fetch['image'] == ''){
            echo '<img src="images/default-avatar.png">';
         }else{
            echo '<img src="uploaded_img/'.$fetch['image'].'">';
         }
         if(isset($message)){
            foreach($message as $message){
               echo '<div class="message">'.$message.'</div>';
            }
         }
      ?>
      <div class="flex">
         <div class="inputBox">
            <span>username :</span>
            <input type="text" name="update_name" value="<?php echo $fetch['name']; ?>" class="box">
            <span>your email :</span>
            <input type="email" name="update_email" value="<?php echo $fetch['email']; ?>" class="box">
            <span>update your pic :</span>
            <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png" class="box">
         </div>
         <div class="inputBox">
            <span>old password :</span>
            <input type="password" name="old_pass" placeholder="enter previous password" class="box">
            <span>new password :</span>
            <input type="password" name="new_pass" placeholder="enter new password" class="box">
            <span>confirm password :</span>
            <input type="password" name="confirm_pass" placeholder="confirm new password" class="box">
         </div>
      </div>
      <input type="submit" value="update profile" name="update_profile" class="btn">
      <a href="home.php" class="delete-btn">go back</a>
   </form>

</div>

</body>
</html>