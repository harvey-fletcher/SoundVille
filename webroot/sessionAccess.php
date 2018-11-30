<?php

    //We need the database
    include '../config/database.php';

    include '../controllers/accessController.php';

    $access->checkAuth();
?>
