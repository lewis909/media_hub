<?php
session_start();
include("app/mysqli_connect_mediahub.php"); //Establishing connection with our database

$error = ""; //Variable for storing our errors.
if(isset($_POST["submit"]))
{
    if(empty($_POST["username"]) || empty($_POST["password"]))
    {
        $error = "Both fields are required.";
    }else
    {
// Define $username and $password
        $username=trim($_POST['username']);
        $password=trim($_POST['password']);

// To protect from MySQL injection
        $username = trim($username);
        $password = trim($password);
        $username = mysqli_real_escape_string($dbc, $username);
        $password = mysqli_real_escape_string($dbc, $password);
        //$password = md5($password);

//Check username and password from database
        $sql="SELECT id FROM media_hub.users WHERE username='$username' and password='$password'";
        $result=mysqli_query($dbc,$sql);
        $row=mysqli_fetch_array($result);

//If username and password exist in our database then create a session.
//Otherwise echo error.

        if(mysqli_num_rows($result) == 1)
        {
            $_SESSION['username'] = $username; // Initializing Session
            header("location: task.php"); // Redirecting To Other Page
        }else
        {
            $error = "Incorrect username or password.";
        }

    }
}

?>