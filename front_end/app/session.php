<?php
session_start();

require_once 'mysqli_connect_mediahub.php';

$user_check = $_SESSION['login_user'];

$login_query = "SELECT username FROM media_hub.users WHERE username = '$user_check'";

$check = mysqli_query($dbc,$login_query);

$row = mysqli_fetch_assoc($check);

$login_session = $row['username'];

if(!isset($login_session)){

    mysqli_close($dbc);
    header('location: ../index.php');

}


