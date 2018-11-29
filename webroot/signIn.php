<?php

    //Start the session
    session_start();

    //This page uses the DB
    include '../config/database.php';

    //If we are already signed in, direct the user away from this page.
    if( !isset( $_POST['email'] ) || !isset( $_POST['password'] ) ){
        header( 'Location: ' . $_GET['referrer'] . '?code=400' );
    } else {
        //Create the query to identify user rows
        $loginQuery = $db->prepare("SELECT * FROM users WHERE email=:email");

        //bind the parameters to the query
        $loginQuery->bindParam( ":email", $_POST['email'] );

        //Execute the result
        $loginQuery->execute();

        //Get the results
        $results = $loginQuery->fetchAll( PDO::FETCH_ASSOC );

        //How many rows are there
        if( $loginQuery->rowCount() == 1 ){
            if( password_verify( $_POST['password'], $results[0]['password'] ) ){
                //Login success, set the session
                $_SESSION = $results[0];

                //Get the number of items in that user's basket
                $basketSizeQuery = $db->prepare("SELECT COUNT(*) AS 'size' FROM baskets WHERE user_id=:user_id");
                $basketSizeQuery->bindParam( ":user_id", $_SESSION['id'] );
                $basketSizeQuery->execute();

                //Add the size of the basket to the session
                $_SESSION['basketSize'] = $basketSizeQuery->fetch( PDO::FETCH_ASSOC )['size'];

                //Login success, redirect with success message
                http_response_code( 303 );
                header( 'Location: ' . $_GET['referrer'] . '?code=303' );
            } else {
                http_response_code( 403 );
                header('Location: ' . $_GET['referrer'] . '?code=403');
            }
        } else {
            //Login failed, redirect with error message
            http_response_code( 403 );
            header('Location: ' . $_GET['referrer'] . '?code=403');
        }
    }
?>
