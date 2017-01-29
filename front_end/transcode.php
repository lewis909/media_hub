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

    $file_check_query = "SELECT * FROM file_repo INNER JOIN asset_metadata ON file_repo.filename = asset_metadata.material_id WHERE filename = '$m_name'";

    $check = mysqli_query($dbc, $file_check_query);

    $file_check = mysqli_fetch_array($check);




    if($file_check){

        echo 'file present<br/>';

        if (empty($data_missing)) {

            require_once('app/mysqli_connect_mediahub.php');

            $query = "INSERT INTO task (material_id, workflow, status, user) VALUES (?, ?, 'Submitted', ?)";

            $stmt = mysqli_prepare($dbc, $query);

            mysqli_stmt_bind_param($stmt, "sss", $m_name, $w_name, $lu_name);

            mysqli_stmt_execute($stmt);


            $affected_rows = mysqli_stmt_affected_rows($stmt);

            if ($affected_rows == 1) {

                $id = mysqli_insert_id($dbc);

                echo 'Job' . " " . $id . " " . 'submitted';

                mysqli_stmt_close($stmt);

                mysqli_close($dbc);

                unset($dbc);

            } else {

                echo 'Error Occurred<br />';
                echo mysqli_error();

                mysqli_stmt_close($stmt);

                mysqli_close($dbc);

                unset($dbc);

            }

            // If File and metadate are present create core_xml.xml


            $con = mysqli_connect('localhost', 'lewis_transcode', 'tool4602');
            $db_select = mysqli_select_db($con, 'media_hub');


            $xmlquery = "SELECT
                      *
                    FROM asset_metadata
                    INNER JOIN file_repo ON asset_metadata.material_id = file_repo.filename
                    INNER JOIN task ON task.material_id = file_repo.filename
                    INNER JOIN profiles ON task.workflow = profiles.name
                    WHERE asset_metadata.material_id = '$m_name' AND task.task_id = '$id' AND profiles.name = '$w_name'";

            $xmlresults = mysqli_query($con, $xmlquery);

            $conform_query_sd16 = "SELECT conform_profile FROM conform_profiles WHERE name = 'sd_16_9'";
            $conform_query_sd43 = "SELECT conform_profile FROM conform_profiles WHERE name = 'sd_4_3'";
            $conform_query_hd = "SELECT conform_profile FROM conform_profiles WHERE name = 'hd'";

            $sd16 = mysqli_query($con, $conform_query_sd16);
            $sd43 = mysqli_query($con, $conform_query_sd43);
            $hd = mysqli_query($con, $conform_query_hd);

            $xml = new DOMDocument('1.0', 'utf-8');
            $xml->formatOutput=true;

            $manifest = $xml->createElement("manifest");
            $xml->appendChild($manifest);



            while($row = mysqli_fetch_assoc($xmlresults)){

                // Aspect Ratio selection Logic

                $conform_profile = null;

                if($row['definition'] == 'sd' && $row['aspect_ratio'] == '12f16'){

                    $conform_profile = mysqli_fetch_assoc($sd16);

                }elseif ($row['definition'] == 'sd' && $row['aspect_ratio'] == '16f16'){

                    $conform_profile = mysqli_fetch_assoc($sd16);

                }elseif ($row['definition'] == 'sd' && $row['aspect_ratio'] == '12f12'){

                    $conform_profile = mysqli_fetch_assoc($sd43);

                }elseif ($row['definition'] == 'sd' && $row['aspect_ratio'] == '12p16'){

                    $conform_profile = mysqli_fetch_assoc($sd16);

                }elseif ($row['definition'] == 'hd' && $row['aspect_ratio'] == '16f16') {

                    $conform_profile = mysqli_fetch_assoc($hd);
                }

                //Create core xml doc.

                $task = $manifest->setAttributeNode(new DOMAttr('task_id', $row['task_id']));

                $asset_metadata=$xml->createElement("asset_metadata");
                $manifest->appendChild($asset_metadata);

                $mat_id=$xml->createElement("material_id",$row['material_id']);
                $asset_metadata->appendChild($mat_id);

                $s_title=$xml->createElement("series_title",$row['series_title']);
                $asset_metadata->appendChild($s_title);

                $sea_title=$xml->createElement("season_title",$row['season_title']);
                $asset_metadata->appendChild($sea_title);

                $sea_num=$xml->createElement("season_number",$row['season_number']);
                $asset_metadata->appendChild($sea_num);

                $ep_title=$xml->createElement("episode_title",$row['episode_title']);
                $asset_metadata->appendChild($ep_title);

                $ep_num=$xml->createElement("episode_number",$row['episode_number']);
                $asset_metadata->appendChild($ep_num);

                $s_name=$xml->createElement("start_date",$s_name);
                $asset_metadata->appendChild($s_name);

                $e_name=$xml->createElement("end_date",$e_name);
                $asset_metadata->appendChild($e_name);

                $ratings=$xml->createElement("ratings",$row['ratings']);
                $asset_metadata->appendChild($ratings);

                $synopsis=$xml->createElement("synopsis",$row['synopsis']);
                $asset_metadata->appendChild($synopsis);

                $file_info=$xml->createElement("file_info");
                $manifest->appendChild($file_info);

                $src_fileName=$xml->createElement("source_filename",$row['filename']);
                $file_info->appendChild($src_fileName);

                $src_fileName=$xml->createElement("number_of_segments",$row['number_of_segments']);
                $file_info->appendChild($src_fileName);

                if($row['number_of_segments'] == 1) {

                    $seg_1 = $xml->createElement("segment_1");
                    $sg1_s_at = $seg_1->setAttributeNode(new DOMAttr('seg_1_in', $row['seg_1_in']));
                    $sg1_s_at = $seg_1->setAttributeNode(new DOMAttr('seg_1_out', $row['seg_1_out']));
                    $sg1_d_at = $seg_1->setAttributeNode(new DOMAttr('seg_1_dur', $row['seg_1_dur']));
                    $file_info->appendChild($seg_1);

                }elseif ($row['number_of_segments'] == 2){

                    $seg_1 = $xml->createElement("segment_1");
                    $sg1_s_at = $seg_1->setAttributeNode(new DOMAttr('seg_1_in', $row['seg_1_in']));
                    $sg1_s_at = $seg_1->setAttributeNode(new DOMAttr('seg_1_out', $row['seg_1_out']));
                    $sg1_d_at = $seg_1->setAttributeNode(new DOMAttr('seg_1_dur', $row['seg_1_dur']));
                    $file_info->appendChild($seg_1);

                    $seg_2 = $xml->createElement("segment_2");
                    $sg2_s_at = $seg_2->setAttributeNode(new DOMAttr('seg_2_in', $row['seg_2_in']));
                    $sg2_s_at = $seg_2->setAttributeNode(new DOMAttr('seg_2_out', $row['seg_2_out']));
                    $sg2_d_at = $seg_2->setAttributeNode(new DOMAttr('seg_2_dur', $row['seg_2_dur']));
                    $file_info->appendChild($seg_2);

                }elseif ($row['number_of_segments'] == 3){

                    $seg_1 = $xml->createElement("segment_1");
                    $sg1_s_at = $seg_1->setAttributeNode(new DOMAttr('seg_1_in', $row['seg_1_in']));
                    $sg1_s_at = $seg_1->setAttributeNode(new DOMAttr('seg_1_out', $row['seg_1_out']));
                    $sg1_d_at = $seg_1->setAttributeNode(new DOMAttr('seg_1_dur', $row['seg_1_dur']));
                    $file_info->appendChild($seg_1);

                    $seg_2 = $xml->createElement("segment_2");
                    $sg2_s_at = $seg_2->setAttributeNode(new DOMAttr('seg_2_in', $row['seg_2_in']));
                    $sg2_s_at = $seg_2->setAttributeNode(new DOMAttr('seg_2_out', $row['seg_2_out']));
                    $sg2_d_at = $seg_2->setAttributeNode(new DOMAttr('seg_2_dur', $row['seg_2_dur']));
                    $file_info->appendChild($seg_2);

                    $seg_3 = $xml->createElement("segment_3");
                    $sg3_s_at = $seg_3->setAttributeNode(new DOMAttr('seg_3_in', $row['seg_3_in']));
                    $sg3_s_at = $seg_3->setAttributeNode(new DOMAttr('seg_3_out', $row['seg_3_out']));
                    $sg3_d_at = $seg_3->setAttributeNode(new DOMAttr('seg_3_dur', $row['seg_3_dur']));
                    $file_info->appendChild($seg_3);

                }elseif ($row['number_of_segments'] == 4){

                    $seg_1 = $xml->createElement("segment_1");
                    $sg1_s_at = $seg_1->setAttributeNode(new DOMAttr('seg_1_in', $row['seg_1_in']));
                    $sg1_s_at = $seg_1->setAttributeNode(new DOMAttr('seg_1_out', $row['seg_1_out']));
                    $sg1_d_at = $seg_1->setAttributeNode(new DOMAttr('seg_1_dur', $row['seg_1_dur']));
                    $file_info->appendChild($seg_1);

                    $seg_2 = $xml->createElement("segment_2");
                    $sg2_s_at = $seg_2->setAttributeNode(new DOMAttr('seg_2_in', $row['seg_2_in']));
                    $sg2_s_at = $seg_2->setAttributeNode(new DOMAttr('seg_2_out', $row['seg_2_out']));
                    $sg2_d_at = $seg_2->setAttributeNode(new DOMAttr('seg_2_dur', $row['seg_2_dur']));
                    $file_info->appendChild($seg_2);

                    $seg_3 = $xml->createElement("segment_3");
                    $sg3_s_at = $seg_3->setAttributeNode(new DOMAttr('seg_3_in', $row['seg_3_in']));
                    $sg3_s_at = $seg_3->setAttributeNode(new DOMAttr('seg_3_out', $row['seg_3_out']));
                    $sg3_d_at = $seg_3->setAttributeNode(new DOMAttr('seg_3_dur', $row['seg_3_dur']));
                    $file_info->appendChild($seg_3);

                    $seg_4 = $xml->createElement("segment_4");
                    $sg4_s_at = $seg_4->setAttributeNode(new DOMAttr('seg_4_in', $row['seg_4_in']));
                    $sg4_s_at = $seg_4->setAttributeNode(new DOMAttr('seg_4_out', $row['seg_4_out']));
                    $sg4_d_at = $seg_4->setAttributeNode(new DOMAttr('seg_4_dur', $row['seg_4_dur']));
                    $file_info->appendChild($seg_4);

                }

                $c_profile=$xml->createElement("conform_profile", $conform_profile['conform_profile']);
                $c_at = $c_profile->setAttributeNode(new DOMAttr('definition', $row['definition']));
                $a_at = $c_profile->setAttributeNode(new DOMAttr('aspect_ratio', $row['aspect_ratio']));
                $file_info->appendChild($c_profile);

                $t_profile=$xml->createElement("transcode_profile",$row['target_profile']);
                $p_at = $t_profile->setAttributeNode(new DOMAttr('profile_name', $w_name));
                $pk_at = $t_profile->setAttributeNode(new DOMAttr('package_type', $row['package_type']));
                $file_info->appendChild($t_profile);

                $trg_path=$xml->createElement("target_path",$row['target_path']);
                $file_info->appendChild($trg_path);

                $video_file_naming_convention=$xml->createElement("video_file_naming_convention",$row['video_file_naming_convention']);
                $file_info->appendChild($video_file_naming_convention);

                $image_file_naming_convention=$xml->createElement("image_file_naming_convention",$row['image_file_naming_convention']);
                $file_info->appendChild($image_file_naming_convention);

                $package_naming_convention=$xml->createElement("package_naming_convention",$row['package_naming_convention']);
                $file_info->appendChild($package_naming_convention);


                mysqli_close($con);

                unset($con);

            }

            date_default_timezone_set('Europe/London');
            $date = date("dmyHis");
            //echo "<xmp>".$xml->saveXML()."</xmp>";
            $xml->save("F:/transcoder/staging/prep/$m_name" . "_" . "$w_name". "_"."$date".".xml");

        } else {

            echo 'You need to enter the following data<br />';

            foreach ($data_missing as $missing) {

                echo "$missing<br />";

            }

        }


    }else{

        echo 'No complete asset matches that Material ID, please check that the video file and metadata are available.<br/>';

        echo 'You need to enter the following data<br />';

        foreach ($data_missing as $missing) {

            echo "$missing<br />";

        }

        mysqli_close($dbc);

        unset($dbc);
        
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