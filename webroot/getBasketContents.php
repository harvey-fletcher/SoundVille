<?php

    //We need the databaser
    include '../config/database.php';

    //Prepare the query that we will use to get the user's basket
    $basketQuery = $db->prepare( "SELECT b.product_id, b.quantity, p.product_name, p.product_price, p.product_description, p.product_max_per_purchase, pi.image_url FROM baskets b JOIN products p ON b.product_id=p.id JOIN product_images pi ON p.id=pi.product_id  WHERE user_id=:user_id" );

    //Bind parameters
    $basketQuery->bindParam( ":user_id", $_SESSION['id'] );

    //Execute the query
    $basketQuery->execute();

    //Get the items from the basket
    $basketItems = $basketQuery->fetchAll( PDO::FETCH_ASSOC );

    //This is an array for the sorted basket
    $sortedBasket = array();

    //Group the items in the basket into product groups
    foreach( $basketItems as $basket_item_id=>$item ){
        $sortedBasket[ $item['product_id'] ][] = $item;
    }

    //Erase the basket items, making it a blank array
    $basketItems = array();

    //Count each of the product types, adding up the quantity
    foreach( $sortedBasket as $product_id=>$products ){
        foreach( $products as $key=>$product ){
            if( isset( $basketItems[ $product['product_id'] ] ) ){
                $newQuantity = (int)$basketItems[ $product['product_id'] ]['quantity'] + $product['quantity'];
                $basketItems[ $product['product_id'] ]['quantity'] = $newQuantity;
            } else {
                $basketItems[ $product['product_id'] ] = $product;
            }
        }
    }

    //Arrays have to start at 0
    $basketItems = array_values( $basketItems );

?>
