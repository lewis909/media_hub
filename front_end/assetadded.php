<?php

include ("app/check.php");

?>

<html>
<head>
    <title>Add Aseet</title>
    <link rel="stylesheet" type="text/css" href="app/media_hub.css">
</head>
<body>
<div>
<?php
 
if(isset($_POST['submit'])){
    
    $data_missing = array();
    
    if(empty($_POST['material_id'])){
 
        // Adds name to array
        $data_missing[] = 'Material ID';
 
    } else {
 
        // Trim white space from the name and store the name
        $m_name = trim($_POST['material_id']);
 
    }
 
    if(empty($_POST['series_title'])){
 
        // Adds name to array
        $data_missing[] = 'Seris Title';
 
    } else{
 
        // Trim white space from the name and store the name
        $s_name = trim($_POST['series_title']);
 
    }

    if(empty($_POST['season_title'])){

        // Adds name to array
        $data_missing[] = 'Season Title';

    } else{

        // Trim white space from the name and store the name
        $t_name = trim($_POST['season_title']);

    }
    if(empty($_POST['season_number'])){

        // Adds name to array
        $data_missing[] = 'season Number';

    } else{

        // Trim white space from the name and store the name
        $n_name = trim($_POST['season_number']);

    }

    if(empty($_POST['episode_title'])){

        // Adds name to array
        $data_missing[] = 'Episode Title';

    } else{

        // Trim white space from the name and store the name
        $e_name = trim($_POST['episode_title']);

    }

    if(empty($_POST['episode_number'])){

        // Adds name to array
        $data_missing[] = 'Episode Number';

    } else{

        // Trim white space from the name and store the name
        $p_name = trim($_POST['episode_number']);

    }

    if(empty($_POST['synopsis'])){

        // Adds name to array
        $data_missing[] = 'Synopsis';

    } else{

        // Trim white space from the name and store the name
        $y_name = trim($_POST['synopsis']);

    }

    if(empty($_POST['ratings'])){

        // Adds name to array
        $data_missing[] = 'Ratings';

    } else{

        // Trim white space from the name and store the name
        $r_name = trim($_POST['ratings']);

    }


    
    if(empty($data_missing)){
        
        require_once('app/mysqli_connect_mediahub.php');
        
        $query = "INSERT INTO asset_metadata (material_id, series_title, season_title, season_number, episode_title, episode_number, synopsis, ratings) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($dbc, $query);
        
        mysqli_stmt_bind_param($stmt, "ssssssss", $m_name, $s_name, $t_name, $n_name, $e_name, $p_name, $y_name, $r_name);
        
        mysqli_stmt_execute($stmt);
        
        $affected_rows = mysqli_stmt_affected_rows($stmt);
        
        if($affected_rows == 1){
            
            echo 'New Asset Entered';
            
            mysqli_stmt_close($stmt);
            
            mysqli_close($dbc);
            
        } else {
            
            echo 'Error Occurred<br />';
            echo mysqli_error();
            
            mysqli_stmt_close($stmt);
            
            mysqli_close($dbc);
            
        }
        
    } else {
        
        echo 'You need to enter the following data<br />';
        
        foreach($data_missing as $missing){
            
            echo "$missing<br />";
            
        }
        
    }
    
}
 
?>
</div>

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