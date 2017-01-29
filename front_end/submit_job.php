<?php

include ("app/check.php");

?>

<!DOCTYPE html>
<!--suppress ALL -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Media Hub</title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="app/material_id.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <script>
            $(function() {
                $( "#start_datepicker" ).datepicker({

                    dateFormat: 'dd-mm-yy'

                });
            });
        </script>
        <script>
            $(function() {
                $( "#end_datepicker" ).datepicker({

                    dateFormat: 'dd-mm-yy'

                });
            });
        </script>
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
        <li><a href="Submit_Job.php">Submit Job</a></li>
        <?php echo $nav_bar ?>
    </ul>
    <p>Hello, <em><?php echo $login_user;?>!</em><a href="logout.php">Logout?</a></p>
</div>



<div id="content">

    <p><b>Submit Job</b></p>

    <form action="http://localhost:1234/media_hub/transcode.php" method="post">

        <b>Select Asset to transcode</b>

        <input type="hidden" name="login_user" value="<?php echo $login_user; ?>"/>

        <p>Material ID:
            <input type="text" name="material_id" size="30" value="" id="keyword"/>
        <div id="results"></div>
        </p>

        <p>Profile:
            <?php

            require '/app/start.php';

            $profileQuery = "
                SELECT
                    name
                FROM profiles
                ";

            $profiles = $db->query($profileQuery);

            ?>
            <select name="workflow">
                        <option value="">Select a transcode Profile</option>
                        <?php foreach($profiles->fetchAll() as $profile): ?>

                            <option value="<?php echo $profile['name']?>"><?php echo $profile['name']?></option>

                        <?php endforeach; ?>
            </select>
            

        </p>

        <p>License Start Date: <input type="text" id="start_datepicker" name="start_date"></p>

        <p>License End Date: <input type="text" id="end_datepicker" name="end_date"></p>

        <p>
            <input type="submit" name="submit" value="Send" />
        </p>

    </form>

</div>

</body>
</html>