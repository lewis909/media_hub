<?php

include ("app/check.php");

?>


<html>
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
<div>
<?php

include ("app/create_xml.php");

if(isset($_POST['submit'])) {

    $m_name = null;

    $data_missing = array();

    if (empty($_POST['login_user'])) {

        // Adds name to array
        $data_missing[] = 'Login user';

    } else {

        // Trim white space from the name and store the name
        $lu_name = trim($_POST['login_user']);

    }

    if (empty($_POST['material_id'])) {

        // Adds name to array
        $data_missing[] = 'Material ID';

    } else {

        // Trim white space from the name and store the name
        $m_name = trim($_POST['material_id']);

    }

    if (empty($_POST['workflow'])) {

        // Adds name to array
        $data_missing[] = 'workflow';

    } else {

        // Trim white space from the name and store the name
        $w_name = trim($_POST['workflow']);

    }

    if (empty($_POST['start_date'])) {

        // Adds name to array
        $data_missing[] = 'License Start Date';

    } else {

        // Trim white space from the name and store the name
        $s_name = trim($_POST['start_date']);

    }

    if (empty($_POST['end_date'])) {

        // Adds name to array
        $data_missing[] = 'License End Date';

    } else {

        // Trim white space from the name and store the name
        $e_name = trim($_POST['end_date']);

    }


    //checks to make sure that file has been ingested and reference is in DB

    require_once('app/mysqli_connect_mediahub.php');
    require_once ("app/media_hub_insert.php");

    $file_check_query = "SELECT * FROM file_repo INNER JOIN asset_metadata ON file_repo.filename = asset_metadata.material_id WHERE filename = '$m_name'";

    $check = mysqli_query($dbc, $file_check_query);

    $file_check = mysqli_fetch_array($check);

    // If file is in Media Hub DB create task XML
    if($file_check){


        create_xml($m_name,$w_name,$lu_name,$s_name,$e_name,$dbc,$con);

        //if asset is not in Media Hub DB check the TX and Schedule DB
    }elseif($file_check == false){

        // Check TX DB for material ID

        require_once('app/mysqli_connect_txmam.php');

        $tx_file_check_query = "SELECT material_id FROM txmam.assets WHERE material_id= '$m_name'";
        $tx_check = mysqli_query($dbc, $tx_file_check_query);
        $tx_file_check = mysqli_fetch_array($tx_check);
        $txquery = "SELECT * FROM assets WHERE material_id = '$m_name'";
        $txresults = mysqli_query($tx_dbc, $txquery);

        // If mat id is in the TX database execute block
        if($tx_file_check){

            while ($txrow = mysqli_fetch_assoc($txresults)){

                $tx_mat_id = $txrow['material_id'];
                $tx_total_segs = $txrow['total_segments'];
                $tx_seg_1_start = $txrow['seg_1_start'];
                $tx_seg_1_dur = $txrow['seg_1_dur'];
                $tx_seg_2_start = $txrow['seg_2_start'];
                $tx_seg_2_dur = $txrow['seg_2_dur'];
                $tx_seg_3_start = $txrow['seg_3_start'];
                $tx_seg_3_dur = $txrow['seg_3_dur'];
                $tx_seg_4_start = $txrow['seg_4_start'];
                $tx_seg_4_dur = $txrow['seg_4_dur'];
                $tx_definition = $txrow['definition'];
                $tx_aspect_ratio = $txrow['aspect_ratio'];

                mysqli_close($tx_dbc);
                unset($tx_dbc);
                echo 'Metadata added from TX DB to Media Hub DB:<br/>';
                echo "$tx_mat_id<br/>";

                require_once ("app/media_hub_insert.php");

                $insert_tx_to_mediahub = "INSERT INTO media_hub.file_repo(filename, definition, aspect_ratio, number_of_segments,seg_1_in,seg_1_dur,seg_2_in,seg_2_dur,seg_3_in,seg_3_dur,seg_4_in,seg_4_dur) VALUE (?,?,?,?,?,?,?,?,?,?,?,?)";

                $tx_stmt = mysqli_prepare($dbc, $insert_tx_to_mediahub);
                mysqli_stmt_bind_param($tx_stmt, "sssissssssss", $tx_mat_id, $tx_definition, $tx_aspect_ratio ,$tx_total_segs ,$tx_seg_1_start,$tx_seg_1_dur,$tx_seg_2_start,$tx_seg_2_dur,$tx_seg_3_start,$tx_seg_3_dur,$tx_seg_4_start,$tx_seg_4_dur);

                mysqli_stmt_execute($tx_stmt);

            }

        }else{

            echo 'asset not in the TX DB: ';
            echo "$m_name<br>";

        }

        // Check Schedule DB for material ID
        require_once ('app/mysqli_connect_schedule.php');

        $sc_file_check_query = "SELECT material_id FROM schedule.asset_metadata WHERE material_id='$m_name'";
        $sc_check = mysqli_query($dbc, $sc_file_check_query );
        $sc_file_check = mysqli_fetch_array($sc_check);
        $scquery = "SELECT * FROM schedule.asset_metadata WHERE material_id = '$m_name'";
        $scresults = mysqli_query($sc_dbc, $scquery);

        // If mat id is in the Schedule database execute block
        if($sc_file_check){

            while($scrow = mysqli_fetch_assoc($scresults)){

                $sc_mat_id = $scrow['material_id'];
                $sc_series_title = $scrow['series_title'];
                $sc_season_title = $scrow['season_title'];
                $sc_season_number = $scrow['season_number'];
                $sc_episode_title = $scrow['episode_title'];
                $sc_episode_number = $scrow['episode_number'];
                $sc_synopsis = $scrow['synopsis'];
                $sc_ratings = $scrow['ratings'];

                mysqli_close($sc_dbc);
                unset($sc_dbc);

                require_once ("app/media_hub_insert.php");

                $insert_sc_to_mediahub = "INSERT INTO media_hub.asset_metadata(material_id, series_title, season_title, season_number, episode_title, episode_number, ratings, synopsis) VALUE (?,?,?,?,?,?,?,?)";
                $sc_stmt = mysqli_prepare($dbc, $insert_sc_to_mediahub);
                mysqli_stmt_bind_param($sc_stmt, "sssisiss", $sc_mat_id, $sc_series_title, $sc_season_title, $sc_season_number, $sc_episode_title, $sc_episode_number,$sc_ratings, $sc_synopsis);

                mysqli_stmt_execute($sc_stmt);

                echo 'Metadata added from Schdule DB to Media Hub DB:<br/>';
                echo "$sc_mat_id<br/>";

            }

        }else{

            echo 'asset not in the Schedule DB: ';
            echo "$m_name<br>";

        }
        // Checks that TX & Schedule data has been imported into Media HUb, If it has the task XML will be created.
        if($tx_file_check && $sc_file_check == true){

            create_xml($m_name,$w_name,$lu_name,$s_name,$e_name,$dbc,$con);

        }else{

        }

        if ($data_missing){

            echo 'You need to enter the following data<br />';

            foreach ($data_missing as $missing) {

                echo "$missing<br />";

            }

        }else{

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



<div id="content">

    <p><b>Submit Job</b></p>

    <form action="http://localhost:1234/media_hub/transcode.php" method="post">

        <b>Select Asset to transcode</b>

        <input type="hidden" name="login_user" value="<?php echo $login_user; ?>"/>

        <p>Material ID:
            <input type="text" name="material_id" size="30" value="" id="keyword"/>
        <div id="results"></div>
        </p>
        <!-- Profile drop down menu -->
        <p>Profile:
            <?php

            // Selects profiles from DB to display in drop down menu.
            require '/app/start.php';

            $profileQuery = "
                SELECT
                    name
                FROM profiles
                ";

            $profiles = $db->query($profileQuery);

            ?>
            <!-- Results returned from $profileQuery, ready to be selected in the drop down. -->
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