<?php

function create_xml($m_name,$w_name,$lu_name,$s_name,$e_name,$dbc,$con){

    //require_once 'mysqli_connect_mediahub.php';

    $file_check_query = "SELECT * FROM file_repo INNER JOIN asset_metadata ON file_repo.filename = asset_metadata.material_id WHERE filename = '$m_name'";

    $check = mysqli_query($dbc, $file_check_query);

    $file_check = mysqli_fetch_array($check);

    if ($file_check) {

        echo 'file present<br/>';

        if (empty($data_missing)) {

            //require_once('app/mysqli_connect_mediahub.php');

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
            //require_once("app/media_hub_insert.php");

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
            $xml->formatOutput = true;

            $manifest = $xml->createElement("manifest");
            $xml->appendChild($manifest);


            while ($row = mysqli_fetch_assoc($xmlresults)) {

                // Aspect Ratio selection Logic

                $conform_profile = null;

                if ($row['definition'] == 'sd' && $row['aspect_ratio'] == '12f16') {

                    $conform_profile = mysqli_fetch_assoc($sd16);

                } elseif ($row['definition'] == 'sd' && $row['aspect_ratio'] == '16f16') {

                    $conform_profile = mysqli_fetch_assoc($sd16);

                } elseif ($row['definition'] == 'sd' && $row['aspect_ratio'] == '12f12') {

                    $conform_profile = mysqli_fetch_assoc($sd43);

                } elseif ($row['definition'] == 'sd' && $row['aspect_ratio'] == '12p16') {

                    $conform_profile = mysqli_fetch_assoc($sd16);

                } elseif ($row['definition'] == 'hd' && $row['aspect_ratio'] == '16f16') {

                    $conform_profile = mysqli_fetch_assoc($hd);
                }

                //Create core xml doc.

                $task = $manifest->setAttributeNode(new DOMAttr('task_id', $row['task_id']));

                $asset_metadata = $xml->createElement("asset_metadata");
                $manifest->appendChild($asset_metadata);

                $mat_id = $xml->createElement("material_id", $row['material_id']);
                $asset_metadata->appendChild($mat_id);

                $s_title = $xml->createElement("series_title", $row['series_title']);
                $asset_metadata->appendChild($s_title);

                $sea_title = $xml->createElement("season_title", $row['season_title']);
                $asset_metadata->appendChild($sea_title);

                $sea_num = $xml->createElement("season_number", $row['season_number']);
                $asset_metadata->appendChild($sea_num);

                $ep_title = $xml->createElement("episode_title", $row['episode_title']);
                $asset_metadata->appendChild($ep_title);

                $ep_num = $xml->createElement("episode_number", $row['episode_number']);
                $asset_metadata->appendChild($ep_num);

                $s_name = $xml->createElement("start_date", $s_name);
                $asset_metadata->appendChild($s_name);

                $e_name = $xml->createElement("end_date", $e_name);
                $asset_metadata->appendChild($e_name);

                $ratings = $xml->createElement("ratings", $row['ratings']);
                $asset_metadata->appendChild($ratings);

                $synopsis = $xml->createElement("synopsis", $row['synopsis']);
                $asset_metadata->appendChild($synopsis);

                $file_info = $xml->createElement("file_info");
                $manifest->appendChild($file_info);

                $src_fileName = $xml->createElement("source_filename", $row['filename']);
                $file_info->appendChild($src_fileName);

                $src_fileName = $xml->createElement("number_of_segments", $row['number_of_segments']);
                $file_info->appendChild($src_fileName);

                if ($row['number_of_segments'] == 1) {

                    $seg_1 = $xml->createElement("segment_1");
                    $sg1_s_at = $seg_1->setAttributeNode(new DOMAttr('seg_1_in', $row['seg_1_in']));
                    $sg1_s_at = $seg_1->setAttributeNode(new DOMAttr('seg_1_out', $row['seg_1_out']));
                    $sg1_d_at = $seg_1->setAttributeNode(new DOMAttr('seg_1_dur', $row['seg_1_dur']));
                    $file_info->appendChild($seg_1);

                } elseif ($row['number_of_segments'] == 2) {

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

                } elseif ($row['number_of_segments'] == 3) {

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

                } elseif ($row['number_of_segments'] == 4) {

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

                $c_profile = $xml->createElement("conform_profile", $conform_profile['conform_profile']);
                $c_at = $c_profile->setAttributeNode(new DOMAttr('definition', $row['definition']));
                $a_at = $c_profile->setAttributeNode(new DOMAttr('aspect_ratio', $row['aspect_ratio']));
                $file_info->appendChild($c_profile);

                $t_profile = $xml->createElement("transcode_profile", $row['target_profile']);
                $p_at = $t_profile->setAttributeNode(new DOMAttr('profile_name', $w_name));
                $pk_at = $t_profile->setAttributeNode(new DOMAttr('package_type', $row['package_type']));
                $file_info->appendChild($t_profile);

                $trg_path = $xml->createElement("target_path", $row['target_path']);
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
            $xml->save("F:/transcoder/staging/prep/$m_name" . "_" . "$w_name" . "_" . "$date" . ".xml");

        } else {

            echo 'You need to enter the following data<br />';

            foreach ($data_missing as $missing) {

                echo "$missing<br />";

            }

        }


    }
}