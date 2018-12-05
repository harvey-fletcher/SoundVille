<?php

    //We need the databaser
    include '../config/database.php';

    //Check the user is signed in
    //only do this if the access class hasn't already been called on this page.
    if( !$access ){
        include '../controllers/accessController.php';
        $access->checkAuth();
    }

    //Prepare the query that we will use to get the user's basket
    $basketQuery = $db->prepare( "SELECT b.product_id, b.quantity, p.product_name, p.product_price, p.product_description, p.product_max_per_purchase, pi.image_url FROM baskets b JOIN products p ON b.product_id=p.id JOIN product_images pi ON p.product_image_id=pi.id  WHERE user_id=:user_id" );

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

    //Check that all the products in the basket will be in stock at the checkout
    foreach( $basketItems as $productID=>$product ){
        //Get the stock level for that product
        $stockCheckQuery = $db->prepare("SELECT product_stock_level FROM products WHERE id=:productID");
        $stockCheckQuery->bindParam(":productID", $productID);
        $stockCheckQuery->execute();
        $stockLevel = $stockCheckQuery->fetchAll( PDO::FETCH_ASSOC )[0]['product_stock_level'];

        if( ($stockLevel - $product['quantity']) < 0 ){
            $basketItems[ $productID ]['in_stock'] = false;
        } else {
            $basketItems[ $productID ]['in_stock'] = true;
        }
    }

    //Arrays have to start at 0
    $basketItems = array_values( $basketItems );

?>
