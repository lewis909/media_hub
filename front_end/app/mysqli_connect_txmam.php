<?php
DEFINE('TX_USER', 'lewis_transcode');
DEFINE('TX_PASSWORD', 'tool4602');
DEFINE('TX_HOST', 'localhost');
DEFINE('TX_NAME', 'txmam');

$tx_dbc = @ mysqli_connect(TX_HOST, TX_USER, TX_PASSWORD, TX_NAME)
    OR die('Could not connect to MYSQL: ' .
           mysqli_connect_error());
