<?php
require_once ('mysqli_connect_mediahub.php');
session_start();

$admin_user_check=$_SESSION['username'];

if($admin_user_check == 'admin'){

    $admin_user_check=$_SESSION['username'];

    $sql = mysqli_query($dbc,"SELECT username FROM media_hub.users WHERE username='$admin_user_check' ");

    $row=mysqli_fetch_array($sql);

    $login_user= 'admin';

    if(!isset($admin_user_check))
    {
        header("Location: index.php");
    }

}else{

    header("Location: index.php");

}

