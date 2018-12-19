<?php

    //This page will complete a pending user action based on the
    //specified identifier.
    $success = doUpdate();

    function doUpdate(){
        //Has a token been specifed
        if( !isset($_GET['identifier']) ){
            return false;
        }

        //This page uses the database connection
        include '../config/database.php';

        //Check to see if there are any rows in the DB with that identifier
        $query = $db->prepare( "SELECT * FROM pending_user_updates WHERE unique_identifier = :identifier AND spent = '0'" );
        $query->bindParam( ":identifier", $_GET['identifier'] );
        $query->execute();
        $rows = $query->fetchAll( PDO::FETCH_ASSOC );
        if( sizeof( $rows ) != 1 ){
            return false;
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
            return false;
        }

        //Execute the query
        $query->execute();

        //Success?
        if( $query->rowCount() ){
            //We need to update the pending action to be spent
            $query = $db->prepare( "UPDATE pending_user_updates SET spent=1 WHERE id = :id");
            $query->bindParam(":id", $action['id']);
            $query->execute();

            return true;
        } else {
            return false;
        }
    }
?>
<html>
    <head>
        <link rel="stylesheet" href="main.css" type="text/css"/>
        <title>Linkenfest 2019</title>
    </head>
    <body>
        <img src="https://files.linkenfest.co.uk/logo_png.png" class="main-logo"/>
        <div class="signInWidget">
            <?php include 'signInWidget.php'; ?>
        </div>
        <div class="links" align="right">
            <?php include 'menu.php'; ?>
        </div>
        <div class="mainBodyContainer">
            <?php if( $success ){ ?>
                <h1 class="success noMargin">Success!</h1>
                <p class="noMargin">
                    The operation was completed successfully.
                </p>
            <?php } else { ?>
                <h1 class="warning noMargin">Error.</h1>
                <p class="noMargin">
                    Something went wrong. Please try again.
                </p>
            <?php } ?>
        </div>
    </body>
</html>
