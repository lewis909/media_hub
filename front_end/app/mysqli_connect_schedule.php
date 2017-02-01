<?php
DEFINE('SC_USER', 'username');
DEFINE('SC_PASSWORD', 'password');
DEFINE('SC_HOST', 'localhost');
DEFINE('SC_NAME', 'databasename');

$sc_dbc = @ mysqli_connect(SC_HOST, SC_USER, SC_PASSWORD, SC_NAME)
    OR die('Could not connect to MYSQL: ' .
           mysqli_connect_error());
