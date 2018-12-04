<?php
    session_start();

    //This page needs the database and session access restrictions
    include 'sessionAccess.php';

    //Select all the information for this users orderr
    $myOrdersQuery = $db->prepare("SELECT * FROM orders WHERE user_id=:user_id ORDER BY created ASC");
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
                <form name="updateEmail" action="">
                    <input type="text" name="newEmail" class="signInWidgetControls" placeholder="New Email"/>
                    <input type="text" name="newEmailConfirm" class="signInWidgetControls" placeholder="Confirm Email"/>
                    <button type="submit" class="signOutButton">Update Email</button>
                </form>
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
            <?php } ?>
        </div>
    </body>
</html>
