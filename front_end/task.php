<?php

include ("app/check.php");

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" http-equiv="refresh" content="5; URL="index.php">
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
        <?php echo $nav_bar ?>
    </ul>
    <p>Hello, <em><?php echo $login_user;?>!</em><a href="logout.php">Logout?</a> </p>
</div>



<div id="section">

    <h4>Task Status</h4>

    <p></p>

    <p> <?php

        require_once('app/mysqli_connect_mediahub.php');

        $query = "SELECT task_id , material_id, workflow, status FROM task ORDER BY task_id DESC ";

        $response = @mysqli_query($dbc,$query);
        if($response){

            echo '<table width="200" cellpadding="5" cellspacing="10">
                    <tr>
                        <th>Task ID</th>
                        <th>Workflow</th>
                        <th>Material ID</th>
                        <th>Status</th>
                    </tr>';

            while($row = mysqli_fetch_array($response)){

                $progress = null;
                $content = null;

                echo '<tr>';
                    echo '<td>'.$row['task_id'].'</td>';
                    echo '<td>'.$row['workflow'].'</td>';
                    echo '<td>'.$row['material_id'].'</td>';
                    echo '<td>'.$row['status'].'</td>';
                echo '</tr>';

            }

            echo '</table>';

        }else {
            echo "couldn't connect to DB";
            echo mysqli_errno($dbc);
        }
        mysqli_close($dbc);

        ?>
    </p>

</div>

</body>
</html>