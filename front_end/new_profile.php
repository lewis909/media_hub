<?php

/*

NEW.PHP

Allows user to create a new entry in the database

*/



// creates the new record form

// since this form is used multiple times in this file, I have made it a function that is easily reusable

function renderForm($name, $target_path, $target_profile, $video_file_naming_convention, $image_file_naming_convention, $package_naming_convention, $package_type, $error)

{

    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Media Hub</title>
        <link rel="stylesheet" type="text/css" href="app/media_hub.css">
        <script>

            <?php

            include ("app/admin_check.php");

            ?>


        </script>
    </head>


    <body>

    <?php

    // if there are any errors, display them

    if ($error != '')

    {

        echo '<div style="padding:4px; border:1px solid red; color:red;">'.$error.'</div>';

    }

    ?>

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


    </div>

    </body>

    <div id="content">

    <form action="" method="post">

        <div>

            <h4>Create New Profile</h4>

            <p>Profile Name:</p>

            <p><input type="text" name="name" value=""/></p>

            <p>Target Path:</p>

            <p><input type="text" name="target_path" value=""/>

            <p>Target Profile:</p>

            <p><textarea type="text" cols="100" rows="10" name="target_profile" value=""></textarea></p>

            <p>Video File Naming Convention:</p>

            <p><textarea type="text" cols="100" rows="10" name="video_file_naming_convention" value=""></textarea></p>

            <p>Image File Naming Convention:</p>

            <p><textarea type="text" cols="100" rows="10" name="image_file_naming_convention" value=""></textarea></p>

            <p>Package Naming Convention:</p>

            <p><textarea type="text" cols="100" rows="10" name="package_naming_convention" value=""></textarea></p>

            <p>Package Type:</p>

            <p>

                <select name="package_type">
                <option value="tar">tar</option>
                <option value="dir">dir</option>
                <option value="flat">flat</option>
            </select>

            </p>

            <input type="submit" name="submit" value="Save" class="button button3"/> <a id="delete" href="profiles.php" class="button button2">Cancel</a>

        </div>

    </form>

    </div>

    </body>

    </html>

    <?php

}




// connect to the database

require_once('app/mysqli_connect_mediahub.php');



// check if the form has been submitted. If it has, start to process the form and save it to the database

if (isset($_POST['submit']))

{

// get form data, making sure it is valid

    $name = trim($_POST['name']);

    $target_path = trim($_POST['target_path']);

    $package_type = trim($_POST['package_type']);

    $video_file_naming_convention = trim($_POST['video_file_naming_convention']);

    $image_file_naming_convention = trim($_POST['image_file_naming_convention']);

    $package_naming_convention = trim($_POST['package_naming_convention']);

    $target_profile = trim($_POST['target_profile']);

    $package_type = trim($_POST['package_type']);


// check to make sure both fields are entered

    if ($name == '' || $target_path == '')

    {

// generate error message

        $error = 'ERROR: Please fill in all required fields!';



// if either field is blank, display the form again

        renderForm($name, $target_path, $target_profile, $video_file_naming_convention, $image_file_naming_convention, $package_naming_convention, $package_type, $error);

    }

    else

    {

// save the data to the database

        $query = "INSERT INTO profiles (name, target_path, target_profile, video_file_naming_convention, image_file_naming_convention, package_naming_convention, package_type) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($dbc, $query);

        mysqli_stmt_bind_param($stmt, "sssssss", $name, $target_path, $target_profile, $video_file_naming_convention, $image_file_naming_convention, $package_naming_convention, $package_type);

        mysqli_stmt_execute($stmt);



// once saved, redirect back to the view page

        header("Location: profiles.php");

    }

}

else

// if the form hasn't been submitted, display the form

{

    renderForm('','','','','','','','');

}

?>