<?php

    //We need the database
    include '../config/database.php';

    //If there is no email or password in the session, deny access
    if( !isset( $_SESSION['email'] ) || !isset( $_SESSION['password'] ) ){
        header( 'Location: index.php?code=403' );
    } else {
        //Check that the user account exists
        //Create the query to identify user rows
        $loginQuery = $db->prepare("SELECT * FROM users WHERE email=:email");

        //bind the parameters to the query
        $loginQuery->bindParam( ":email", $_SESSION['email'] );

        //Execute the result
        $loginQuery->execute();

        //Get the results
        $results = $loginQuery->fetchAll( PDO::FETCH_ASSOC );

        //How many rows are there
        if( $loginQuery->rowCount() == 1 ){
            if( $_SESSION['password'] != $results[0]['password'] ){
                header('Location: index.php?code=403');
            }
        } else {
            //Login failed, redirect with error message
            header('Location: index.php?code=403');
        }
    }

?>
