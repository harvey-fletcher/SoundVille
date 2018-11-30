<?php
    //Uses session
    session_start();

    //Import the dependencies file
    include '../config/dependencies.php';
    $dependencies = new Dependencies();

    //Uses the database
    include '../config/database.php';

    //Check user is signed in
    include 'sessionAccess.php';

    //Since the confirmation email contains the basket, we need to load the basket now
    include 'getBasketContents.php';

    if( $_SESSION['basketSize'] > 0 ){
        //Generate an order reference
        $orderReference = substr( hash( 'sha1', json_encode( $basketItems ) . date('Y-m-d H:i:s') . $_SESSION['email'] ), 0, 20);

        //We need to insert the order to the database
        $newOrder = $db->prepare( "INSERT INTO orders (order_reference, user_id) VALUES ( :order_reference, :user_id)" );
        $newOrder->bindParam( ":order_reference", $orderReference );
        $newOrder->bindParam( ":user_id", $_SESSION['id'] );
        $newOrder->execute();
        $orderNumber = $db->lastInsertId();

        //Now, we need to insert all the products from this order into the database so it is logged
        foreach( $basketItems as $key=>$item ){
            $newOrder = $db->prepare( "INSERT INTO order_products ( order_id, product_id, quantity ) VALUES ( :order_id, :product_id, :quantity )" );
            $newOrder->bindParam( ":order_id", $orderNumber );
            $newOrder->bindParam( ":product_id", $item['product_id'] );
            $newOrder->bindParam( ":quantity", $item['quantity'] );
            $newOrder->execute();
        }

        //Build the confirmation email
        include 'purchaseConfirmationEmail.php';

        //Update the user's session so it has nothing in the basket
        $_SESSION['basketSize'] = 0;

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

        //There has not been an error.
        $error = false;
    } else {
        $error = true;
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
        <div class="mainBodyContainer" align="center">
            <?php if(!$error){ ?>
                <h1 class="noMargin">
                    Thank You!
                </h2>
                <h4>
                    Your order has been created, and a confirmation email sent to `<?= $_SESSION['email']; ?>`<br /><br />
                    Please keep it safe, and bring a printed copy with you to the event.
                </h4>
            <?php } else { ?>
                <h1 class="noMargin">An unexpected error has occurred.</h1>
            <?php } ?>
        </div>
    </body>
</html>
