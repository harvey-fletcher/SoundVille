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
                include '../config/dependencies.php';
                $dependencies = new Dependencies();

                //Generate an order reference
                $orderReference = substr( hash( 'sha1', json_encode( $basket ) . date('Y-m-d H:i:s') . $_SESSION['email'] ), 0, 20);

                //We need to insert the order to the database
                $newOrder = $db->prepare( "INSERT INTO orders (order_reference, user_id) VALUES ( :order_reference, :user_id)" );
                $newOrder->bindParam( ":order_reference", $orderReference );
                $newOrder->bindParam( ":user_id", $_SESSION['id'] );
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
                    $stockAdjustQuery = $db->prepare("UPDATE products SET product_stock_level = product_stock_level - :quantity");
                    $stockAdjustQuery->bindParam( ":quantity", $item['quantity'] );
                    $stockAdjustQuery->execute();
                }

                //Build the confirmation email
                $emailBody = $this->buildConfirmationEmail( $basket, $orderReference, $orderNumber );
                if( !$emailBody ){
                    return array( "message" => "Error building confirmation email." );
                }

                //Delete all items that are in the user's basket on the server
                $emptyBasketQuery = $db->prepare("DELETE FROM baskets WHERE user_id=:user_id");
                $emptyBasketQuery->bindParam( ":user_id", $_SESSION['id'] );
                $emptyBasketQuery->execute();

                //Set the mail parameters
                $mailer = $dependencies->mailer();
                $mailer->addAddress( $_SESSION['email'] );
                $mailer->Subject = "Linkenfest 2019: Booking Confirmation";
                $mailer->Body = $emailBody;
                $mailer->send();

                //Update the user's session so it has nothing in the basket
                $_SESSION['basketSize'] = 0;

                //Success
                return array( "message" => "Success! Your reference number is " . $orderReference . " and a confirmation email has been sent to you." );
            } else {
                return array( "message" => "Cannot checkout. Your basket is empty.");
            }
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
            $orderSummary .= "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp</td>"
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
                       .         "&nbsp;&nbsp;&nbsp;&nbsp;SP11 0EA"
                       .         "<br /><br />"
                       .         "Questions? Contact us!<br />0751 174 9870<br />https://www.linkenfest.co.uk"
                       .     "</h4>"
                       . "</div>";

            return $emailBody;
        }
    }
