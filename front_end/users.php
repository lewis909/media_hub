<?php

include ("app/admin_check.php");

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
    <ul>
        <li><a class="active" href="index.php">Job Queue</a></li>
        <li><a href="ingest.html">Ingest Asset</a></li>
        <li><a href="submit_job.php">Submit Job</a></li>
        <li><a href="profiles.php">Admin</a></li>
    </ul>
</div>
<div id="admin_nav">
    <ul>
        <li><a href="profiles.php">Profiles</a></li>
        <li><a href="users.php">Users</a></li>
        <li><a href="repo.php">Repo</a></li>
    </ul>
    <p>Hello, <em><?php echo $login_user;?>!</em><a href="logout.php">Logout?</a></p>
</div>
<div id="content">

    <h4>Users</h4>

    <?php

    require_once('app/mysqli_connect_mediahub.php');

    $profiles_query = "SELECT * FROM users";

    $response = mysqli_query($dbc,$profiles_query);

    echo "<table border='1' cellpadding='10'>";

    echo "<tr> <th>User Name</th> <th>Edit</th> <th>Delete</th> ";

    while($row = mysqli_fetch_array($response)){

        echo "<tr>";

        echo '<td>' . $row['username'] . '</td>';

        echo '<td id="edit"><a  href="edit_user.php?id=' . $row['id'] . '" class="button button1">Edit</a></td>';

        echo '<td id="delete"><a  href="delete_user.php?id=' . $row['id'] . '"class="button button2">Delete</a></td>';

        echo "</tr>";

    }

    echo "</table>";

    ?>

    <p id="create"><a href="new_user.php" class="button button3">Create New User</a></p>


</div>

</body>
</html>