<?php
    session_start();

    //This page needs the database and session access restrictions
    include 'sessionAccess.php';

    //Import the dependencies file
    include '../config/dependencies.php';
    $dependencies = new Dependencies();

    //If a form was submitted on this page, we need to update the user account
    if( isset( $_POST['changeEmailForm'] ) ){
        //An update will happen
        $updateHappened = true;

        $uuid = hash('sha1', $_SESSION['email'] . date('Y-m-d H:i:s') );
        $actionName = "email";

        if( $_POST['newEmail'] == $_POST['newEmailConfirm'] ){

            $query = $db->prepare("INSERT INTO pending_user_updates( unique_identifier, user_id, do_action, update_column, old_value, new_value ) VALUES ( :uuid, :user_id, :do_action, :update_column, :old_val, :new_val )");
            $query->bindParam( ":uuid", $uuid );
            $query->bindParam( ":user_id", $_SESSION['id']);
            $query->bindParam( ":do_action", $actionName );
            $query->bindParam( ":update_column", $actionName );
            $query->bindParam( ":old_val", $_SESSION['email'] );
            $query->bindParam( ":new_val", $_POST['newEmailConfirm'] );

            $query->execute();

            $emailsMatch = true;
        } else {
            $emailsMatch = false;
        }

        //Make a mail
        //Set the mail parameters
        $mailer = $dependencies->mailer();
        $mailer->addAddress( $_POST['newEmailConfirm'] );
        $mailer->Subject = "Linkenfest: Confirm your email";

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
               .         "Hello, someone just tried to register this email address on the https://linkenfest.co.uk site.<br />If this was you, please click the link below:<br /><br />"
               .         "https://linkenfest.co.uk/completePendingAction.php?identifier=" . $uuid . "<br /><br />"
               .         "If this was not you, you do not need to do anything."
               .     "</h4><br /><br />"
               .     "Questions? Contact us!<br />0751 174 9870<br />https://www.linkenfest.co.uk"
               . "</div>";

        $mailer->Body = $emailBody;
        $mailer->send();
    } else {
        //An update has not happened
        $updateHappened = false;
    }

    //Select all the information for this users orderr
    $myOrdersQuery = $db->prepare("SELECT * FROM orders WHERE user_id=:user_id ORDER BY created DESC");
    $myOrdersQuery->bindParam( ":user_id", $_SESSION['id'] );
    $myOrdersQuery->execute();
    $myOrders = $myOrdersQuery->fetchAll( PDO::FETCH_ASSOC );

    //Get the products that are in each of those orders
    foreach( $myOrders as $key=>$order ){
        //Get the products
        $myOrderProductsQuery = $db->prepare( "SELECT op.*, p.product_name, p.product_price FROM order_products op JOIN products p ON op.product_id=p.id WHERE op.order_id=:id" );
        $myOrderProductsQuery->bindParam( ":id", $order['id'] );
        $myOrderProductsQuery->execute();
        $myOrderProducts = $myOrderProductsQuery->fetchAll( PDO::FETCH_ASSOC );

        //Build a new array structure with those products
        $myOrder = array(
                "id"      => $order['id'],
                "created" => $order['created'],
                "items"   => $myOrderProducts
            );

        //Remove the old order
        unset( $myOrders[$key] );

        //Add a new order
        $myOrders[ $order['order_reference'] ] = $myOrder;

        //This is the total cost
        $orderTotal = 0;

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
            <h1 class="noMargin">My Details:</h1>
            <p>
                Registered email address: <?= $_SESSION['email']; ?><br />
                <h3 class="noMargin">Change Email:</h3><br />
                <?php if( !$updateHappened || !$emailsMatch ){ ?>
                    <?php if( !$emailsMatch ){ ?>
                        The emails entered did not match. Please try again.<br />
                    <?php } ?>
                    <form name="updateEmail" action="" method="POST">
                        <input type="text" name="newEmail" class="signInWidgetControls" placeholder="New Email"/>
                        <input type="text" name="newEmailConfirm" class="signInWidgetControls" placeholder="Confirm Email"/>
                        <button type="submit" name="changeEmailForm" class="signOutButton">Update Email</button>
                    </form>
                <?php } else { ?>
                    <h2 class="noMargin">Success! Please check your email inbox and click the link to complete this process.</h2>
                <?php } ?>
            </p>
            <br />
            <h1 class="noMargin">My Orders:</h1>
            <?php foreach($myOrders as $reference=>$order){?>
                <?php $orderTotal = 0; ?>
                <h3 class="noMargin">
                    Order: <?= $reference; ?><br />
                    Placed: <?= $order['created']; ?><br /><br />
                </h3>
                <table style="margin-left: 30px;">
                    <tr>
                        <td class="itemsList noBgTableHead">Item Name</td>
                        <td class="itemsList noBgTableHead">Product Price</td>
                        <td class="itemsList noBgTableHead">Quantity</td>
                        <td class="itemsList noBgTableHead">Sub-Total</td>
                    </tr>
                    <tr>
                        <td class="itemsList">&nbsp;</td>
                        <td class="itemsList">&nbsp;</td>
                        <td class="itemsList">&nbsp;</td>
                        <td class="itemsList">&nbsp;</td>
                    </tr>
                    <?php foreach( $order['items'] as $key=>$product ){ ?>
                        <?php
                            //Calculate the sub-total
                            $subTotal   = ( $product['quantity'] * $product['product_price'] );

                            //Re-Calculate the order total
                            $orderTotal += $subTotal;
                        ?>
                        <tr>
                            <td class="itemsList"><?= $product['product_name']; ?></td>
                            <td class="itemsList"><?= $product['product_price']; ?></td>
                            <td class="itemsList"><?= $product['quantity']; ?></td>
                            <td class="itemsList">&pound;<?= number_format( $subTotal, 2, '.', ''); ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td class="itemsList">&nbsp;</td>
                        <td class="itemsList">&nbsp;</td>
                        <td class="itemsList">&nbsp;</td>
                        <td class="itemsList">&nbsp;</td>
                    </tr>
                    <?php
                        //Calculate how much processing fee was paid on the order
                        $processingCharge =  number_format( ( $orderTotal / 40 ) , 2, '.', '');

                        //Add the processing fee to the order total
                        $orderTotal += $processingCharge;
                    ?>
                    <tr>
                        <td class="itemsList">Processing Fee:</td>
                        <td class="itemsList">&nbsp;</td>
                        <td class="itemsList">&nbsp;</td>
                        <td class="itemsList">&pound;<?= $processingCharge; ?></td>
                    </tr>
                    <tr>
                        <td class="itemsList">Total</td>
                        <td class="itemsList">&nbsp;</td>
                        <td class="itemsList">&nbsp;</td>
                        <td class="itemsList">&pound;<?= number_format( $orderTotal, 2, '.', ''); ?></td>
                    </tr>
                </table>
                <br /><br />
            <?php } ?>
        </div>
    </body>
</html>
