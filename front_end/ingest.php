<?php

include ("app/check.php");

?>



<!DOCTYPE html>
<!--suppress ALL -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Media Hub</title>
    <link rel="stylesheet" type="text/css" href="app/media_hub.css">
</head>
<body>


<div id="header" class="center">

    <h1>Media Hub</h1>

</div>

<div id="nav" class="center">
    <ul>
        <li><a class="active" href="index.php">Job Queue</a></li>
        <li><a href="ingest.php">Ingest Asset</a></li>
        <li><a href="Submit_Job.php">Submit Job</a></li>
        <?php echo $nav_bar ?>
    </ul>
    <p>Hello, <em><?php echo $login_user;?>!</em><a href="logout.php">Logout?</a></p>
</div>
<div id="content" class="center">
    <h4>Add Asset Metadata</h4>

    <form action="http://localhost:1234/media_hub/assetadded.php" method="post">

        <p >Material ID:<br/></p>
        <p><input  type="text" name="material_id" size="30" value="" /></p>

        <p>Series Title:<br/></p>
        <p><input type="text" name="series_title" size="30" value="" /></p>

        <p>Season Title:<br/></p>
        <p><input type="text" name="season_title" size="30" value="" /></p>

        <p>Season Number:<br/></p>
        <p><input type="text" name="season_number" size="30" value="" /></p>

        <p>Episode Title:<br/></p>
        <p><input type="text" name="episode_title" size="30" value="" /></p>

        <p>Episode Number:<br/></p>
        <p><input type="text" name="episode_number" size="30" value="" /></p>

        <p>Ratings:<br/></p>
        <p><input type="text" name="ratings" size="30" value="" /></p>

        <p>Synopsis:<br/></p>
        <p><textarea rows="7" cols="70" type="text" name="synopsis" value=""></textarea></p>


        <p><input type="submit" name="submit" value="Send" /></p>

    </form>

</div>

</body>
</html>
