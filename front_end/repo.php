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
        <li><a href="ingest.php">Ingest Asset</a></li>
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

    <h4>Asset Repo</h4>

    <?php

    require_once('app/mysqli_connect_mediahub.php');

    $profiles_query = "SELECT * FROM asset_metadata";

    $response = mysqli_query($dbc,$profiles_query);

    echo "<table border='1' cellpadding='10'>";

    echo "<tr> <th>Name</th> <th>Edit</th> <th>Delete</th> ";

    while($row = mysqli_fetch_array($response)){

        echo "<tr>";

        echo '<td>' . $row['material_id'] . '</td>';

        echo '<td id="edit"><a  href="edit_asset.php?id=' . $row['id'] . '" class="button button1">Edit</a></td>';

        echo '<td id="delete"><a  href="delete_asset.php?id=' . $row['id'] . '"class="button button2">Delete</a></td>';

        echo "</tr>";

    }

    echo "</table>";

    ?>

</div>

</body>
    </html><?php
/**
 * Created by PhpStorm.
 * User: 3A01
 * Date: 06/10/2016
 * Time: 11:29
 */