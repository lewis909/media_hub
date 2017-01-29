<?php

/*

EDIT.PHP

Allows user to edit specific entry in database

*/



// creates the edit record form

// since this form is used multiple times in this file, I have made it a function that is easily reusable

function renderForm($id, $username, $password, $error)

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
        <p>Hello, <em><?php echo $login_user;?>!</em><a href="logout.php">Logout?</a></p>
    </div>
    <div id="content">

        <form action="" method="post">

            <input type="hidden" name="id" value="<?php echo $id; ?>"/>

            <div>

                <p><strong>User ID:</strong> <?php echo $id; ?></p>

                <p>Username:</p>

                <p><input type="text" name="username" value="<?php echo $username; ?>"/></p>

                <p>Password:</p>

                <p><input type="text" name="password" value="<?php echo $password; ?>"/></p>

                    <input type="submit" name="submit" value="Save" class="button button3"/> <a id="delete" href="users.php" class="button button2">Cancel</a>

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

        $username = trim($_POST['username']);

        $password = trim($_POST['password']);



// check that username/target_path fields are both filled in

        if ($username == '' || $password == '')

        {

// generate error message

            $error = 'ERROR: Please fill in all required fields!';



//error, display form

            renderForm($id, $username, $password, $error);

        }

        else

        {

// save the data to the database

            mysqli_query($dbc, "UPDATE users SET username='$username', password='$password' WHERE id='$id'")

            or die(mysqli_error($dbc));



// once saved, redirect back to the view page

            header("Location: users.php");

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



// get the 'id' value from the URL (if it exists), making sure that it is valid (checing that it is numeric/larger than 0)

    if (isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0)

    {

// query db

        $id = $_GET['id'];

        $result = mysqli_query($dbc, "SELECT * FROM users WHERE id=$id")

        or die(mysqli_error($dbc));

        $row = mysqli_fetch_array($result);



// check that the 'id' matches up with a row in the databse

        if($row)

        {



// get data from db

            $username = $row['username'];

            $password = $row['password'];




// show form

            renderForm($id, $username, $password, '');

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