<?php
DEFINE('DB_USER', 'lewis_transcode');
DEFINE('DB_PASSWORD', 'tool4602');
DEFINE('DB_HOST', 'localhost');
DEFINE('DB_NAME', 'media_hub');

$dbc = @ mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
    OR die('Could not connect to MYSQL: ' .
           mysqli_connect_error());
       
?>