<?php
include('app/login.php'); // Include Login Script
if ((isset($_SESSION['username']) != ''))
{
    header('Location: task.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Media Hub</title>
    <link rel="stylesheet" type="text/css" href="app/media_hub.css">
</head>

<body>


<div id="header">

    <h1>Media Hub</h1>

</div>

<div id="nav">
</div>
<div id="content">

    <h2>Login</h2>

    <form  method="post" action="">

        <label>Username:</label>
        <input id="name" name="username" placeholder="username" type="text">

        <label>Password :</label>
        <input id="password" name="password" placeholder="password" type="password">

        <input name="submit" type="submit" value=" Login ">

    </form>

    <div class="error"><?php echo $error ?></div>


</div>

</body>
</html>