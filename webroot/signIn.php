<?php

    //Start the session
    session_start();

    //This page uses the DB
    include '../config/database.php';

    //We are using the access controller
    include '../controllers/accessController.php';

    //Login
    $access->login();
?>
