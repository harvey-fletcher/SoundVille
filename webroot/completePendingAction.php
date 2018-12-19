<?php

    //This page will complete a pending user action based on the
    //specified identifier.

    //Has a token been specifed
    if( !isset($_GET['identifier']) ){
        header( 'Location: index.php?code=403' );
    }

    //This page uses the database connection
    include '../config/database.php';

    //Check to see if there are any rows in the DB with that identifier
    $query = $db->prepare( "SELECT * FROM pending_user_updates WHERE unique_identifier = :identifier AND spent = '0'" );
    $query->bindParam( ":identifier", $_GET['identifier'] );
    $query->execute();
    $rows = $query->fetchAll( PDO::FETCH_ASSOC );
    if( sizeof( $rows ) != 1 ){
        header( 'Location: index.php?code=403' );
    }

    //Take a single row
    $action = $rows[0];

    //What to do
    if( $action['do_action'] == 'email' ){
        $query = $db->prepare( "UPDATE users SET email=:email WHERE id=:user_id" );
        $query->bindParam( ":email", $action['new_value']);
        $query->bindParam( ":user_id", $action['user_id']);
    } else if( $action['do_action'] == 'activate' ){
        $query = $db->prepare( "UPDATE users SET activated=1 WHERE id=:user_id");
        $query->bindParam( ":user_id", $action['user_id']);
    } else if( $action['do_action'] == 'resetpass' ){
        $query = $db->prepare( "UPDATE users SET password=:password WHERE id=:user_id");
        $query->bindParam( ":password", $action['new_value'] );
        $query->bindParam( ":user_id", $action['user_id'] );
    } else {
        header( 'Location: index.php?code=403' );
    }

    //Execute the query
    $query->execute();

    //Success?
    if( $query->rowCount() ){
        //We need to update the pending action to be spent
        $query = $db->prepare( "UPDATE pending_user_updates SET spent=1 WHERE id = :id");
        $query->bindParam(":id", $action['id']);
        $query->execute();

        echo "The update completed sucessfully. You can now close this page.";
    } else {
        echo "There was an error, please try again";
    }

?>
