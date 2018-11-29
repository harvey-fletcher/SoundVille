<?php

    header('Content-Type: application/json');

    //Uses the session
    session_start( );

    //Uses the database
    include '../config/database.php';

    //Check the user is the same as the user we authed
    $authQuery = $db->prepare("SELECT password FROM users WHERE id=:id");
    $authQuery->bindParam(":id", $_SESSION['id']);
    $authQuery->execute();
    $authUser = $authQuery->fetch( PDO::FETCH_ASSOC );

    if( $_SESSION['password'] != $authUser['password'] ){
        out( 403, "Access is denied. You do not have permission to access the basket. Please sign in again." );
    } else {
        //Check that the user does not already have the maximum number of this product in the bag
        $productSelectQuery = $db->prepare("SELECT * FROM products WHERE id=:id");
        $productSelectQuery->bindParam( ":id", $_POST['id'] );
        $productSelectQuery->execute();
        $productsMatchingRequest = $productSelectQuery->fetchAll( PDO::FETCH_ASSOC );

        //Check the matching product actually exists
        if( sizeof( $productsMatchingRequest ) != 1 ){
            out( 400, "No product matches the selection." );
        } else {
            //Get the product details from the selected product
            $product = $productsMatchingRequest[0];

            //Check the user's basket to find other quantities of this item
            $basketInspectQuery = $db->prepare( "SELECT SUM( quantity ) as 'item_quantity_in_basket' FROM baskets WHERE user_id=:user_id AND product_id=:product_id" );
            $basketInspectQuery->bindParam( ":user_id", $_SESSION['id'] );
            $basketInspectQuery->bindParam( ":product_id", $_POST['id'] );
            $basketInspectQuery->execute();

            //Get all the rows from the basket
            $basketItems = $basketInspectQuery->fetchAll( PDO::FETCH_ASSOC )[0]['item_quantity_in_basket'];

            //Has the maximum number been exceeded?
            if( $basketItems >= $product['product_max_per_purchase'] && $basketItems != NULL){
                out( 400, "You already have the maximum quantity of this item in your basket.\nPlease checkout first, then try again." );
            }

            //Will the maximum number of this product be exceeded?
            if( $basketItems + $_POST['productQuantity'] > $product['product_max_per_purchase'] && $basketItems != NULL){
                out( 400, "You have selected too many of this product. Please select fewer and try again." );
           }
        }

        //add the new record to the DB so it is tracked as being in the basket, even after sign out
        $updateBasketQuery = $db->prepare("INSERT INTO baskets (`user_id`,`product_id`, `quantity`) VALUES (:user_id, :product_id, :quantity)");
        $updateBasketQuery->bindParam( ":user_id", $_SESSION['id'] );
        $updateBasketQuery->bindParam( ":product_id", $_POST['id'] );
        $updateBasketQuery->bindParam( ":quantity", $_POST['productQuantity'] );
        $updateBasketQuery->execute();

        //Update the session to have the new count of items in the basket
        $_SESSION['basketSize']++;

        //Return success code and message
        out( 200, "The selection has been added to your basket." );
    }

    function out( $code, $message ){
        //This will be sent back to the client browser in JSON format
        $details = array(
                "status"  => $code,
                "message" => $message
            );

        //JSON encode the message
        $details = json_encode( $details );

        //Return the message.
        echo $details;

        //Done
        die();
    }

?>
