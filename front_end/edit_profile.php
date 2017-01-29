<?php

/*

EDIT.PHP

Allows user to edit specific entry in database

*/



// creates the edit record form

// since this form is used multiple times in this file, I have made it a function that is easily reusable

function renderForm($id, $name, $target_path, $target_profile, $video_file_naming_convention, $image_file_naming_convention, $package_naming_convention, $package_type, $error)

{

    ?>

    <!DOCTYPE html>

    <html>

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
        <p>Hello, <em><?php  echo $login_user;?>!</em><a href="logout.php">Logout?</a></p>
    </div>
    <div id="content">

        <form action="" method="post">

        <input type="hidden" name="id" value="<?php echo $id; ?>"/>

        <div>

            <p><strong>Profile ID:</strong> <?php echo $id; ?></p>

            <p>Profile Name:</p>

            <p><input type="text" name="name" value="<?php echo $name; ?>"/></p>

            <p>Target Path:</p>

            <p><input type="text" name="target_path" value="<?php echo $target_path; ?>"/>

            <p>Target Profile:</p>

            <p><textarea type="text" cols="100" rows="10" name="target_profile" value=""><?php echo $target_profile; ?></textarea></p>

            <p>Video File Naming Convention:</p>

            <p><textarea type="text" cols="100" rows="10" name="video_file_naming_convention" value=""><?php echo $video_file_naming_convention; ?></textarea></p>

            <p>Image File Naming Convention:</p>

            <p><textarea type="text" cols="100" rows="10" name="image_file_naming_convention" value=""><?php echo $image_file_naming_convention; ?></textarea></p>

            <p>Package Naming Convention:</p>

            <p><textarea type="text" cols="100" rows="10" name="package_naming_convention" value=""><?php echo $package_naming_convention; ?></textarea></p>

            <p>Package Type:</p>

            <p><select name="package_type">
                    <?php

                    if($package_type == 'tar'){

                        echo '<option value="tar">' . $package_type . '</option>';
                        echo '<option value="dir">dir</option>';
                        echo '<option value="flat">flat</option>';

                    }if($package_type == 'dir') {

                        echo '<option value="dir">' . $package_type . '</option>';
                        echo '<option value="tar">tar</option>';
                        echo '<option value="flat">flat</option>';

                    }if($package_type == 'flat') {

                        echo '<option value="flat">' . $package_type . '</option>';
                        echo '<option value="tar">tar</option>';
                        echo '<option value="dir">dir</option>';

                    }

                    ?>
            </select></p>

            <input type="submit" name="submit" value="Save" class="button button3"/> <a id="delete" href="profiles.php" class="button button2">Cancel</a>

        </div>

    </form>

    </body>

    </html>

    <?php

}


// connect to the database

require_once('app/mysqli_connect_mediahub.php');



// check if the form has been submitted. If it has, process the form and save it to the database

if (isset($_POST['submit']))

{

// confirm that the 'id' value is a valid integer before getting the form data

    if (is_numeric($_POST['id']))

    {

// get form data, making sure it is valid

        $id = trim($_POST['id']);

        $name = trim($_POST['name']);

        $target_path = $_POST['target_path'];

        $target_profile = $_POST['target_profile'];

        $package_type = trim($_POST['package_type']);

        $video_file_naming_convention = trim($_POST['video_file_naming_convention']);

        $image_file_naming_convention = trim($_POST['image_file_naming_convention']);

        $package_naming_convention = trim($_POST['package_naming_convention']);



// check that name/target_path fields are both filled in

        if ($name == '' || $target_path == '')

        {

// generate error message

            $error = 'ERROR: Please fill in all required fields!';



//error, display form

            renderForm($id, $name, $target_path, $target_profile, $video_file_naming_convention, $image_file_naming_convention, $package_naming_convention, $package_type, $error);

        }

        else

        {

// save the data to the database

            mysqli_query($dbc, "UPDATE profiles SET name='$name', target_path='$target_path', target_profile='$target_profile', video_file_naming_convention='$video_file_naming_convention', image_file_naming_convention='$image_file_naming_convention', package_naming_convention='$package_naming_convention', package_type ='$package_type'WHERE id='$id'")

            or die(mysqli_error($dbc));



// once saved, redirect back to the view page

            header("Location: profiles.php");

        }

    }

    else

    {

// if the 'id' isn't valid, display an error

        echo 'Error!';

    }

}

else

// if the form hasn't been submitted, get the data from the db and display the form

{



// get the 'id' value from the URL (if it exists), making sure that it is valid (checking that it is numeric/larger than 0)

    if (isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0)

    {

// query db

        $id = $_GET['id'];

        $result = mysqli_query($dbc, "SELECT * FROM profiles WHERE id=$id")

        or die(mysqli_error($dbc));

        $row = mysqli_fetch_array($result);



// check that the 'id' matches up with a row in the database

        if($row)

        {



// get data from db

            $name = $row['name'];

            $target_path = $row['target_path'];

            $target_profile = $row['target_profile'];

            $video_file_naming_convention = $row['video_file_naming_convention'];

            $image_file_naming_convention = $row['image_file_naming_convention'];

            $package_naming_convention = $row['package_naming_convention'];

            $package_type = $row['package_type'];



// show form

            renderForm($id, $name, $target_path, $target_profile, $video_file_naming_convention, $image_file_naming_convention, $package_naming_convention, $package_type, '');

        }

        else

// if no match, display result

        {

            echo "No results!";

        }

    }

    else

// if the 'id' in the URL isn't valid, or if there is no 'id' value, display an error

    {

        echo 'Error!';

    }

}

?>