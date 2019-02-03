<?php

    class checkoutController
    {
        function processOrder(){
            //This uses the DB
            global $db;

            //Need to see the products in the basket
            include '../controllers/basketController.php';
            $basketController = new basketController();
            $basket = $basketController->getContents();

            if( $_SESSION['basketSize'] > 0 ){
                //Check an order reference was specified (we use it to confirm payment was made)
                if( !isset( $_POST['orderReference'] ) ){
                    return array( "status" => 400, "message" => "You have not specified a valid order reference." );
                }

                //On stripe, confirm that a payment was made. If not, error.
                $paymentInfo = $this->confirmPayment( $_POST['orderReference'] );
                if( !$paymentInfo ){
                    return array( "status" => 403, "message" => "The order_id you have specified is not valid" );
                }

                //We need to insert the order to the database
                $newOrder = $db->prepare( "INSERT INTO orders (order_reference, user_id, secret_code) VALUES ( :order_reference, :user_id, :secretCode)" );
                $newOrder->bindParam( ":order_reference", $_POST['orderReference'] );
                $newOrder->bindParam( ":user_id", $_SESSION['id'] );
                $newOrder->bindParam( ":secretCode", $_POST['secretCode'] );
                $newOrder->execute();
                $orderNumber = $db->lastInsertId();

                //Now, we need to insert all the products from this order into the database so it is logged
                foreach( $basket['basket_items'] as $key=>$item ){
                    $newOrder = $db->prepare( "INSERT INTO order_products ( order_id, product_id, quantity ) VALUES ( :order_id, :product_id, :quantity )" );
                    $newOrder->bindParam( ":order_id", $orderNumber );
                    $newOrder->bindParam( ":product_id", $item['product_id'] );
                    $newOrder->bindParam( ":quantity", $item['quantity'] );
                    $newOrder->execute();

                    //Subtract from the products that are available
                    $stockAdjustQuery = $db->prepare("UPDATE products SET product_stock_level = product_stock_level - :quantity WHERE id = :product_id");
                    $stockAdjustQuery->bindParam( ":quantity", $item['quantity'] );
                    $stockAdjustQuery->bindParam( ":product_id", $item['product_id'] );
                    $stockAdjustQuery->execute();
                }

                //Build the confirmation email
                $emailBody = $this->buildConfirmationEmail( $basket, $_POST['orderReference'], $orderNumber );
                if( !$emailBody ){
                    return array( "message" => "Error building confirmation email." );
                }

                //Delete all items that are in the user's basket on the server
                $emptyBasketQuery = $db->prepare("DELETE FROM baskets WHERE user_id=:user_id");
                $emptyBasketQuery->bindParam( ":user_id", $_SESSION['id'] );
                $emptyBasketQuery->execute();

                //Set the mail parameters
                include '../serverSide/emailScript.php';
                $email = new email();
                $email->send( $_SESSION['email'], "do-not-reply", "Linkenfest 2019: Purchase confirmation", $emailBody );

                //Update the user's session so it has nothing in the basket
                $_SESSION['basketSize'] = 0;

                //Success
                return array( "message" => "Success! Your reference number is " . $_POST['orderReference'] . " and a confirmation email has been sent to you." );
            } else {
                return array( "message" => "Cannot checkout. Your basket is empty.");
            }
        }

        function confirmPayment( $reference = NULL ){
            //Uses service keys
            include '../config/service_keys.php';

            //Reference can't be null
            if($reference == NULL){
                return false;
            }

            //Initiate a curl request
            $ch = curl_init();

            //Set options on the curl
            curl_setopt( $ch, CURLOPT_URL, "https://api.stripe.com/v1/charges/" . $reference);
            curl_setopt( $ch, CURLOPT_USERPWD, $serviceKeys[ $serviceMode ]['stripe'] . ":" . "");
            curl_setopt( $ch, CURLOPT_VERBOSE, 0);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

            //Execute the curl
            $result = curl_exec( $ch );

            if( curl_errno( $ch ) ){
                return false;
            }

            curl_close( $ch );

            return $result;
        }

        function buildConfirmationEmail( $basket = NULL, $orderReference = NULL, $orderNumber = 0 ){
            if( $basket == NULL ){
                return false;
            }

            //Start the order summary table
            $orderSummary = "<table border='0' style='display: inline-block'>"
                  . "<tr><td>Quantity</td><td>Item</td><td>Sub-total</td>";

            //Build the order summary
            foreach( $basket['basket_items'] as $key=>$item ){
                $row = "<tr>"
                     .     "<td>". $item['quantity'] ." x</td>"
                     .     "<td>". $item['product_name'] ."&nbsp; &nbsp;</td>"
                     .     "<td>&pound;". $item['item_total'] . "</td>"
                     . "</tr>";

                //Add this product row to the order summary
                $orderSummary .= $row;
            }

            //There is a blank row, followed by a complete order total
            $orderSummary .= "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>"
                           . "<tr><td>&nbsp;</td><td>Processing Fee:</td><td>&pound;" . $basket['processing_fee']  . "</td>"
                           . "<tr><td>&nbsp;</td><td>Total:</td><td>&pound;" . $basket['order_total']['decimal']  . "</td>";

            //Close off the order summary
            $orderSummary .= "</table>";

            //Assemble the full email
            $emailBody = "<div style='width: 750'>"
                       .     "<div style='float: left; width: 150px; height: 150px;'>"
                       .         "<img src='https://files.linkenfest.co.uk/logo_png.png' style='width: 150px; height: 150px;' />"
                       .     "</div>"
                       .     "<div style='float: left; height: 150;' align='right'>"
                       .         "<h1 style='margin: 0; font-size: 130px;'>Linkenfest</h1>"
                       .     "</div>"
                       . "</div>"
                       . "<div style='width: 750; margin-top: 25px; display: inline-block;'>"
                       .     "<h4 style='margin: 0;'>"
                       .         "Hello,<br />Thank you for booking your tickets to Linkenfest 2019! We're happy to have you with us.<br />"
                       .         "It is extremely important that you print this email off and keep it safe. You <b>MUST</b> bring it with you<br />"
                       .         "to the festival, or you will not be granted entry.<br />"
                       .         "<br /><br />"
                       .         "Your order number is <b><u>" . $orderNumber . "</u></b>.<br />"
                       .         "Your reference is: <b><u>" . $orderReference . "</u></b>.<br />"
                       .         "<br /><br />"
                       .         "Here is a summary of your order:<br /><br />"
                       .         $orderSummary
                       .         "<br /><br />"
                       .         "Gates open 2pm on Friday 19th July 2019. If you are a day-ticket holder, you will not be permitted to re-enter<br />"
                       .         "the site once you have left. All guests must have vacated the premises by 19:00 on Sunday 22nd July 2019<br /><br />"
                       .         "<br /><br />"
                       .         "Event address:<br />"
                       .         "&nbsp;&nbsp;&nbsp;&nbsp;Linkenholt Adventure Centre<br />"
                       .         "&nbsp;&nbsp;&nbsp;&nbsp;Linkenholt<br />"
                       .         "&nbsp;&nbsp;&nbsp;&nbsp;Wiltshire<br />"
                       .         "&nbsp;&nbsp;&nbsp;&nbsp;SP11 0EA<br />"
                       .         "<br /><br />"
                       .         "<img src='http://barcodes4.me/barcode/c128a/" . str_replace( 'ch_', '', substr( $orderReference, 0, -15  ) . '.png' ) . "?width=300&height=80&IsTextDrawn=1' />"
                       .         "<br /><br />"
                       .     "</h4>"
                       . "</div>";

            return $emailBody;
        }

        function payment(){
            //Uses the database
            global $db;

            //Uses service keys
            include '../config/service_keys.php';

            //We need to know how much the basket costs
            include '../controllers/basketController.php';
            $basketController = new basketController();
            $basket = $basketController->getContents();

            //Build the reference number
            $orderReference = substr( hash( 'sha1', json_encode( $basket ) . date('Y-m-d H:i') . $_SESSION['email'] ), 0, 20);

            $headers = array();
            $headers[] = "Content-Type: application/x-www-form-urlencoded";

            $ch = curl_init();

            curl_setopt( $ch, CURLOPT_URL, "https://api.stripe.com/v1/charges" );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt( $ch, CURLOPT_POSTFIELDS, "amount=" . $basket['order_total']['plain'] . "&currency=gbp&description=" . $_SESSION['email'] . "&source=" . $_POST['stripeToken']);
            curl_setopt( $ch, CURLOPT_POST, 1);
            curl_setopt( $ch, CURLOPT_USERPWD, $serviceKeys[ $serviceMode ]['stripe'] . ":" . "");

            $result = json_decode( curl_exec($ch), true );

            curl_close ($ch);

            return array( "status" => $result['status'], "order_id" => $result['id'], "message" => "Payment completed successfully", "data" => json_encode( $result ) ) ;
        }

        function verifySecretCode(){
            //Uses db
            global $db;

            //Build the query to get any current secret codes
            $secretCodeQuery = $db->prepare( "SELECT * FROM secret_codes WHERE ( valid_from <= NOW() AND valid_to >= NOW()) OR always_valid = '1'" );
            $secretCodeQuery->execute();
            $secretCodes = $secretCodeQuery->fetchAll( PDO::FETCH_ASSOC );

            //The secret code
            $userCode = strtoupper( $_POST['secretCode'] );

            //All the codes that are valid
            $secretCodes = array_column( $secretCodes, 'code' );

            //Is the user specified code valid?
            if( in_array( $userCode, $secretCodes ) ){
                $codeValid = true;
                $message   = "That is a valid secret code!";
            } else {
                $codeValid = false;
                $message   = "Oops, sorry, this is a private event and you must have a secret code to buy tickets";
            }

            return array( "status" => 200, "codeValid" => $codeValid, "message" => $message );
        }
    }
