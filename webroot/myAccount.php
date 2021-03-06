<?php
    session_start();

    //This page needs the database and session access restrictions
    include 'sessionAccess.php';

    //Need the math controller
    include '../controllers/mathController.php';

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
        $emailBody = "<div style='width: 750'>"
               .     "<div style='float: left; width: 150px; height: 150px;'>"
               .         "<img src='https://files.soundville.co.uk/logo_png.png' style='width: 150px; height: 150px;' />"
               .     "</div>"
               .     "<div style='float: left; height: 150;' align='right'>"
               .         "<h1 style='margin: 0; font-size: 130px;'>SoundVille</h1>"
               .     "</div>"
               . "</div>"
               . "<div style='width: 750; margin-top: 25px; display: inline-block;'>"
               .     "<h4 style='margin: 0;'>"
               .         "Hello, someone just tried to register this email address on the https://soundville.co.uk site.<br />If this was you, please click the link below:<br /><br />"
               .         "https://soundville.co.uk/completePendingAction.php?identifier=" . $uuid . "<br /><br />"
               .         "If this was not you, you do not need to do anything."
               .     "</h4><br /><br />"
               . "</div>";

        //Send the email
        include '../serverSide/emailScript.php';
        $email = new email();
        $email->send( $_POST['newEmailConfirm'], "do-not-reply", "SoundVille: Confirm your email", $emailBody );
    } else if( isset( $_POST['changePasswordForm'] ) ){
        //An update happend
        $updateHappened = true;

        //By default, there are all the errors
        $oldPasswordMatch = false;
        $passwordsMatch   = false;

        //All fields are assumed populated and not null
        $hasMissingFields = false;
        $hasNullFields    = false;

        //This is where the change password code goes
        //These fields are required
        $requiredFields = array(
                "currentPassword",
                "newPassword",
                "newPasswordConfirm"
            );

        //Check all the required fields are specified
        foreach( $requiredFields as $rf ){
            if( !isset( $_POST[ $rf ] ) ){
                $hasMissingFields = "You are missing the " . $rf . " field. Please try again.<br />";
            } else {
                //The field cant be blank
                if( trim( $_POST[ $rf ] ) == "" ){
                  $hasNullFields = "Field " . $rf . " can't be blank. Please try again.<br />";
                }
            }
        }

        //Check the password specified matches the current password
        if( password_verify( $_POST['currentPassword'], $_SESSION['password'] ) ){
            //Passwords match
            $oldPasswordMatch = true;

            if( $_POST['newPassword'] == $_POST['newPasswordConfirm'] ){
                //new passwords match
                $passwordsMatch = true;

                //Hash the new password
                $newPassword = password_hash( $_POST['newPasswordConfirm'], PASSWORD_DEFAULT );

                //Build the update query
                $stmt = $db->prepare( "UPDATE users SET password=:newPassword WHERE id=:user_id" );
                $stmt->bindParam( ":newPassword", $newPassword );
                $stmt->bindParam( ":user_id", $_SESSION['id'] );
                $stmt->execute();

                //Update the session
                $_SESSION['password'] = $newPassword;
            }
        }
    } else {
        //An update has not happened
        $updateHappened = false;
    }

    //Select all the information for this users orderr
    $myOrdersQuery = $db->prepare("SELECT o.*, sc.id AS 'secret_code_id', sc.discount_percent AS 'discountRateModifier', sc.code FROM orders o LEFT JOIN secret_codes sc ON sc.id=o.secret_code WHERE o.user_id=:user_id ORDER BY o.created DESC");
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
                "discountRateModifier" => (int)$order['discountRateModifier'],
                "discountCode" => $order['code'],
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
        <title>SoundVille 2019</title>
    </head>
    <body>
        <img src="https://files.soundville.co.uk/logo_png.png" class="main-logo"/>
        <div class="signInWidget">
            <?php include 'signInWidget.php'; ?>
        </div>
        <div class="links" align="right">
            <?php include 'menu.php'; ?>
        </div>
        <div class="mainBodyContainer">
            <?php if( isset( $_SESSION['email'] ) ){?>
                <h1 class="noMargin">My Details:</h1>
                <p>
                    Registered email address: <?= $_SESSION['email']; ?><br />
                    <h3 class="noMargin">Change Email:</h3><br />
                    <form name="updateEmail" action="" method="POST">
                        <input type="text" name="newEmail" class="signInWidgetControls" placeholder="New Email"/>
                        <input type="text" name="newEmailConfirm" class="signInWidgetControls" placeholder="Confirm Email"/>
                        <button type="submit" name="changeEmailForm" class="signOutButton">Update Email</button>
                    </form>
                    <?php if( $updateHappened && isset( $_POST['changeEmailForm'] ) ) { ?>
                        <?php if( !$emailsMatch ){ ?>
                            The emails entered did not match. Please try again.<br />
                        <?php } else { ?>
                            <h2 class="noMargin">Success! Please check your email inbox and click the link to complete this process.</h2>
                        <?php } ?>
                    <?php } ?>
                </p>
                <br />
                <p>
                    <h3 class="noMargin">Change Password:</h3><br />
                    <form name="updatePassword" action="" method="POST">
                        <input type="password" name="currentPassword" class="signInWidgetControls" placeholder="Current Password" /><br /><br />
                        <input type="password" name="newPassword" class="signInWidgetControls" placeholder="New Password"/>
                        <input type="password" name="newPasswordConfirm" class="signInWidgetControls" placeholder="Confirm New Password"/><br /><br />
                        <button type="submit" name="changePasswordForm" class="doubleFormControl">Update Password</button>
                    </form>
                    <?php if( $updateHappened && isset( $_POST['changePasswordForm'] )){ ?>
                        <?php if( !$oldPasswordMatch ){ ?>
                            The value entered for "current password" did not match. Please try again.<br />
                        <?php } else if( !$passwordsMatch ){ ?>
                            The new passwords entered did not match. Please try again.<br />
                        <?php } else if( $hasMissingFields ){ ?>
                            <?= $hasMissingFields; ?>
                        <?php } else if( $hasNullFields ){ ?>
                            <?= $hasNullFields; ?>
                        <?php } else { ?>
                            <h2 class="noMargin">Success! Your password was updated successfully.</h2>
                        <?php } ?>
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
                            $processingCharge = $math->calcProcessingFee( $orderTotal );

                            //Add the processing fee to the order total
                            $orderTotal += $processingCharge;
                        ?>
                        <tr>
                            <td class="itemsList">Processing Fee:</td>
                            <td class="itemsList">&nbsp;</td>
                            <td class="itemsList">&nbsp;</td>
                            <td class="itemsList">&pound;<?= $processingCharge; ?></td>
                        </tr>
                        <?php if( $order['discountRateModifier'] !== 0 ){  ?>
                                <tr>
                                    <td class="itemsList">Discount Code:</td>
                                    <td class="itemsList">&nbsp;</td>
                                    <td class="itemsList"><?= $order['discountCode']; ?></td>
                                    <td class="itemsList">-£<?= round( $orderTotal * ( $order['discountRateModifier'] / 100 ), 2 ); ?></td>
                                </tr>
                        <?php } ?>
                        <tr>
                            <td class="itemsList">Total</td>
                            <td class="itemsList">&nbsp;</td>
                            <td class="itemsList">&nbsp;</td>
                            <td class="itemsList">&pound;<?= number_format( $orderTotal * ( 1 - ( $order['discountRateModifier'] / 100  )), 2, '.', ''); ?></td>
                        </tr>
                    </table>
                    <br /><br />
                <?php } ?>
            <?php } else { ?>
                <h1 class="warning noMargin">You must be signed in to view this page</h1>
            <?php } ?>
        </div>
    </body>
</html>
