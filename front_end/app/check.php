<?php
require_once ('mysqli_connect_mediahub.php');
session_start();
$user_check=$_SESSION['username'];

$sql = mysqli_query($dbc,"SELECT username FROM media_hub.users WHERE username='$user_check' ");

$row=mysqli_fetch_array($sql);

$login_user=$row['username'];

if($user_check == 'admin'){

    $nav_bar = "<li><a href=\"profiles.php\">Admin</a></li>";

}else{

    $nav_bar = null;

}

if(!isset($user_check))
{
    header("Location: index.php");
}