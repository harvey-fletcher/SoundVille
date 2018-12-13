<?php

    class basketController{
        function getContents(){
            //Uses the database
            global $db;

            //We need to use the math controller (calculate processing fee)
            include '../controllers/mathController.php';

            //Prepare the query that we will use to get the user's basket
            $basketQuery = $db->prepare(
                    "SELECT
                       b.product_id,
                       SUM( b.quantity ) as 'quantity',
                       ROUND( SUM( b.quantity * p.product_price ), 2) as 'item_total',
                       ( ( p.product_stock_level - SUM( b.quantity ) ) >= 0 ) as 'in_stock',
                       p.product_name,
                       p.product_price,
                       p.product_description,
                       p.product_max_per_purchase,
                       p.product_stock_level,
                       pi.image_url
                     FROM baskets b
                     JOIN products p
                       ON b.product_id=p.id
                     JOIN product_images pi
                       ON p.product_image_id=pi.id
                     WHERE
                       user_id=:user_id
                     GROUP BY p.id"
                );

            //Bind parameters
            $basketQuery->bindParam( ":user_id", $_SESSION['id'] );

            //Execute the query
            $basketQuery->execute();

            //Get the items from the basket
            $basketItems = $basketQuery->fetchAll( PDO::FETCH_ASSOC );

            //Calculate the total amount of the order
            $orderTotal = array_sum( array_values( array_column( $basketItems, "item_total") ) );

            //Work out the processing fee
            $processingFee = $math->calcProcessingFee( $orderTotal );

            //Return the data
            return array(
                    "order_total"    => $orderTotal,
                    "processing_fee" => $processingFee,
                    "basket_items"   => $basketItems
                );
        }
    }
