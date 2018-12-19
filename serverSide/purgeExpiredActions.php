<?php

    /**
        This script will purge any pending actions in the database
        that have been there for more than 48 hours.

        It does not care if the action has been completed or not,
        it deletes regardless.
    **/

    //Requires the database
    include '../config/database.php';

    //Prepare the deletion query
    $deletionQuery = $db->prepare( "DELETE FROM pending_user_updates WHERE created <= NOW() - INTERVAL 1 DAY" );
    $deletionQuery->execute();

    //How many rows were affected?
    echo $deletionQuery->rowCount() . " rows deleted.";
