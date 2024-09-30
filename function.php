<?php
session_start();

class Connection{
    public $host = "localhost";
    public $user = "root";
    public $password = "";
    public $db_name = "user_db";
    public $conn;

    public function __construct(){
        $this->conn = mysqli_connect($this->host, $this->user, $this->password, $this->db_name);

        // Check connection
        if (mysqli_connect_errno()) {
            die("Failed to connect to MySQL: " . mysqli_connect_error());
        }
    }
}

class Register extends Connection{
    public function registration($name, $email, $pass, $cpass, $image){
        // Check if email already exists
        $stmt = $this->conn->prepare("SELECT * FROM `user_form` WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            return 10; // Email already taken
        } else {
            if($pass == $cpass){
                // Hash password
                $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

                // Insert new user
                $stmt = $this->conn->prepare("INSERT INTO user_form (name, email, password, image) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $name, $email, $hashed_pass, $image);
                $stmt->execute();

                if($stmt->affected_rows > 0){
                    return 1; // Registration successful
                } else {
                    return 0; // Failed to insert user
                }
            } else {
                return 100; // Passwords do not match
            }
        }
    }
}

class Login extends Connection{
    public $id;

    public function login($email, $pass){
        // Check if email exists
        $stmt = $this->conn->prepare("SELECT * FROM user_form WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if($result->num_rows > 0){
            // Verify password
            if(password_verify($pass, $row["password"])){
                $this->id = $row["id"];
                $_SESSION['user_id'] = $this->id; // Store user ID in session
                return 1; // Login successful
            } else {
                return 10; // Wrong password
            }
        } else {
            return 100; // User not registered
        }
    }
}
?>
