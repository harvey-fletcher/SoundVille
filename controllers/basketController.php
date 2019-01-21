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
                       p.promotion,
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
                    "sub_total"      => number_format( $orderTotal, 2, '.', '' ),
                    "order_total"    => array(
                            "decimal" => number_format( ( $orderTotal + $processingFee ), 2, '.', '' ),
                            "plain"   => number_format( ( $orderTotal + $processingFee ), 2, '', '' ),
                        ),
                    "processing_fee" => number_format( $processingFee, 2, '.', '' ),
                    "basket_items"   => $basketItems
                );
        }

        function amend(){
            //We need access to the DB
            global $db;

            //Check that required fields specified
            if( !isset($_POST['new_quantity']) || !isset($_POST['amend_id']) ){
                return array( "status" => 400, "message" => "You are missing a required field. new_quantity or amend_id." );
            }

            //A check needs to be done to ensure the maximum amount is not exceeded
            $productSelectQuery = $db->prepare("SELECT * FROM products WHERE id=:id");
            $productSelectQuery->bindParam( ":id", $_POST['amend_id'] );
            $productSelectQuery->execute();
            $products = $productSelectQuery->fetchAll( PDO::FETCH_ASSOC );

            //Check the matching product actually exists
            if( sizeof( $products ) != 1 ){
                return array(
                        "status"  => 400,
                        "message" => "No product matches the selection."
                    );
            } else {
                //Has the maximum number been exceeded?
                if( $_POST['new_quantity'] > $products[0]['product_max_per_purchase'] ){
                    return array(
                        "status"  => 400,
                        "message" => "You already have the maximum quantity of this item in your basket.\nPlease checkout first, then try again."
                    );
                }
            }

            //Delete all existing rows
            $this->remove();

            //Add a new single row
            $addBasket = $this->add( array('id' => $_POST['amend_id'], 'productQuantity' => $_POST['new_quantity']) );

            if( $addBasket['status'] == 200 ){
                return array(
                        "status"  => 200,
                        "message" => "Basket successfully updated!"
                    );
            } else {
                return $addBasket;
            }
        }

        function add( $parameters = NULL ){
            //Needs the DB
            global $db;

            //Check that all required fields are specified
            if( $parameters == NULL ){
                if( !isset( $_POST['id'] ) || !isset( $_POST['productQuantity'] ) ){
                    return array(
                            "status"  => 400,
                            "message" => "You are missing id or productQuantity field"
                        );
                }

                //Set parameters
                $parameters = $_POST;
            }

            //Check that the user does not already have the maximum number of this product in the bag
            $productSelectQuery = $db->prepare("SELECT * FROM products WHERE id=:id");
            $productSelectQuery->bindParam( ":id", $parameters['id'] );
            $productSelectQuery->execute();
            $productsMatchingRequest = $productSelectQuery->fetchAll( PDO::FETCH_ASSOC );

            //Check the matching product actually exists
            if( sizeof( $productsMatchingRequest ) != 1 ){
                return array(
                        "status"  => 400,
                        "message" => "No product matches the selection."
                    );
            } else {
                //Get the product details from the selected product
                $product = $productsMatchingRequest[0];

                //Ensure we have enough of that product in stock to handle the request
                if( ( $product['product_stock_level'] - $parameters['productQuantity'] ) < 0 ){
                    return array(
                            "status"  => 400,
                            "message" => "There is not enough of this product in stock.\nPlease decrease your selection and try again."
                        );
                }

                //Check the user's basket to find other quantities of this item
                $basketInspectQuery = $db->prepare( "SELECT SUM( quantity ) as 'item_quantity_in_basket' FROM baskets WHERE user_id=:user_id AND product_id=:product_id" );
                $basketInspectQuery->bindParam( ":user_id", $_SESSION['id'] );
                $basketInspectQuery->bindParam( ":product_id", $parameters['id'] );
                $basketInspectQuery->execute();

                //Get all the rows from the basket
                $basketItems = $basketInspectQuery->fetchAll( PDO::FETCH_ASSOC )[0]['item_quantity_in_basket'];

                if( $basketItems == NULL ){
                    $basketItems = 0;
                }

                //Has the maximum number been exceeded?
                if( $basketItems >= $product['product_max_per_purchase'] ){
                    return array(
                            "status"  => 400,
                            "message" => "There is not enough of this product in stock.\nPlease decrease your selection and try again."
                        );
                }

                //Check the user's basket to find other quantities of this item
                $basketInspectQuery = $db->prepare( "SELECT SUM( quantity ) as 'item_quantity_in_basket' FROM baskets WHERE user_id=:user_id AND product_id=:product_id" );
                $basketInspectQuery->bindParam( ":user_id", $_SESSION['id'] );
                $basketInspectQuery->bindParam( ":product_id", $parameters['id'] );
                $basketInspectQuery->execute();

                //Get all the rows from the basket
                $basketItems = $basketInspectQuery->fetchAll( PDO::FETCH_ASSOC )[0]['item_quantity_in_basket'];

                if( $basketItems == NULL ){
                    $basketItems = 0;
                }

                //Has the maximum number been exceeded?
                if( $basketItems >= $product['product_max_per_purchase'] ){
                    return array(
                        "status"  => 400,
                        "message" => "You already have the maximum quantity of this item in your basket.\nPlease checkout first, then try again."
                    );
                }

                //Will the maximum number of this product be exceeded?
                if( $basketItems + $parameters['productQuantity'] > $product['product_max_per_purchase'] ){
                    return array(
                        "status"  => 400,
                        "message" => "You have selected too many of this product. Please select fewer and try again."
                    );
                }
            }

            //add the new record to the DB so it is tracked as being in the basket, even after sign out
            $updateBasketQuery = $db->prepare("INSERT INTO baskets (`user_id`,`product_id`, `quantity`) VALUES (:user_id, :product_id, :quantity)");
            $updateBasketQuery->bindParam( ":user_id", $_SESSION['id'] );
            $updateBasketQuery->bindParam( ":product_id", $parameters['id'] );
            $updateBasketQuery->bindParam( ":quantity", $parameters['productQuantity'] );
            $updateBasketQuery->execute();

            //Update the size of the users basket
            $this->updateBasketSize();

            //Return success code and message
            return array(
                    "status"  => 200,
                    "message" => "The selection has been added to your basket."
                );
        }

         function remove(){
            //Needs DB access
            global $db;

            //Are all required fields specified
            if( !isset( $_POST['amend_id'] ) ){
                return array(
                        "status"  => 400,
                        "message" => "Please specify amend_id",
                    );
            }

            //Delete from users basket where the product ID matches this one
            $deleteQuery = $db->prepare( "DELETE FROM baskets WHERE product_id=:product_id AND user_id=:user_id" );
            $deleteQuery->bindParam( "product_id", $_POST['amend_id'] );
            $deleteQuery->bindParam( "user_id", $_SESSION['id'] );
            $deleteQuery->execute();

            //Update the size of the user's basket
            $this->updateBasketSize();

            if( $deleteQuery->rowCount() != 0 ){
                return array(
                        "status"  => 200,
                        "message" => "Product removed.",
                    );
            } else {
                return array(
                        "status"  => 200,
                        "message" => "The operation completed successfully but no changes were required.",
                    );
            }
        }

        function updateBasketSize(){
            //Uses the database
            global $db;

            //Update the session to have the new count of items in the basket
            $updateSizeQuery = $db->prepare("SELECT SUM(quantity) AS basket_size FROM baskets WHERE user_id=:user_id");
            $updateSizeQuery->bindParam(":user_id", $_SESSION['id']);
            $updateSizeQuery->execute();
            $_SESSION['basketSize'] = $updateSizeQuery->fetchAll( PDO::FETCH_ASSOC )[0]['basket_size'];

            if( $_SESSION['basketSize'] == NULL ){
                $_SESSION['basketSize'] = 0;
            }

            //Success message
            return array(
                    "status"  => 200,
                    "message" => $_SESSION['basketSize']
                );
        }
    }
