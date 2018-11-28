<?php

    //Access session
    session_start();

    //Clear everything
    $_SESSION = array();
    unset( $_SESSION );

    //Redirect
    header( 'Location: ' . $_GET['referrer'] );

?>
