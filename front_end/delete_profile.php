<?php


/*

DELETE.PHP

Deletes a specific entry from the 'players' table

*/



// connect to the database

require_once('app/mysqli_connect_mediahub.php');



// check if the 'id' variable is set in URL, and check that it is valid

if (isset($_GET['id']) && is_numeric($_GET['id']))

{

// get id value

    $id = $_GET['id'];



// delete the entry

    $result = mysqli_query($dbc, "DELETE FROM profiles WHERE id=$id")

    or die(mysqli_error());



// redirect back to the view page

    header("Location: profiles.php");

}

else

// if id isn't set, or isn't valid, redirect back to view page

{

    header("Location: profiles.php");

}



?>