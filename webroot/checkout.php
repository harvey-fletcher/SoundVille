<?php
    //Uses session
    session_start();

    //This page should only be accessible if the user is signed in
    include 'sessionAccess.php';

    //Need to see the products in the basket
    include 'getBasketContents.php';

    //This is the order total
    $orderTotal = 0;
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
            <h2 class="noMargin"><u>Please confirm your order</u></h2>
            <br />
            <?php foreach( $basketItems as $key=>$product ){ ?>
                <div class="productParent" align="left">
                    <div class="productImageContainer">
                        <img src="<?= $product['image_url']; ?>" width="100%" height="100%" />
                    </div>
                    <div class="productDetails">
                        <h2 class="noMargin">
                            <?= $product['product_name'];?><br  />
                            £<?= $product['product_price'];?> each<br />
                            <br />
                            Quantity: <?= $product['quantity'];?><br />
                            Total Price ( <?= $product['quantity'];?> X £<?= $product['product_price'];?> ): £<?= $product['quantity'] * $product['product_price'];?>
                        </h2>
                        <br />
                        <?php if( $product['quantity'] > $product['product_max_per_purchase'] ){ ?>
                           <h3 class="warning noMargin">
                               You have selected too many of this item and you will not be able to check out.<br />
                               You must first amend your order in the basket.
                           </h3>
                        <?php } ?>
                    </div>
                </div>
            <?php
               //Add the price of this product on to the total
               $orderTotal += $product['quantity'] * $product['product_price'];

               //Put two DP on the subtotal
               if( floor( $orderTotal ) == $orderTotal ){
                   $orderTotal .= '.00';
               }

               //Calculate the processing charge
               $processingCharge = number_format( ( $orderTotal / 100 ) , 2, '.', '');
            }
            ?>
            <div class="productParent">
                <div class="orderOptions" align="right">
                    <h3 class="noMargin">
                        Order SubTotal: £<?= $orderTotal; ?><br />
                        Processing Charge: £<?= $processingCharge ?> <a href="info.php?section=fees">(?)</a><br />
                        <br />
                        Order Total: £<?= number_format( $orderTotal + $processingCharge, 2, '.', '' ); ?><br />
                        <br />
                        <p class="smallPrint">By clicking the button below, you agree to make this purchase, our privacy policy, and cancellation policy.</p>
                        <div id="paypal-button-container"></div>
                    </h3>
                </div>
            </div>
        </div>
    </body>
    <script src="https://www.paypalobjects.com/api/checkout.js"></script>
    <script>
        // Render the PayPal button
        paypal.Button.render({
            // Set your environment
            env: 'sandbox', // sandbox | production

            // Specify the style of the button
            style: {
                layout: 'vertical',  // horizontal | vertical
                size:   'medium',    // medium | large | responsive
                shape:  'rect',      // pill | rect
                color:  'gold'       // gold | blue | silver | white | black
            },

            // Specify allowed and disallowed funding sources
            //
            // Options:
            // - paypal.FUNDING.CARD
            // - paypal.FUNDING.CREDIT
            // - paypal.FUNDING.ELV
            funding: {
                allowed: [
                    paypal.FUNDING.CARD,
                ],
                disallowed: [
                    paypal.FUNDING.CREDIT
                ]
            },

            // Enable Pay Now checkout flow (optional)
            commit: true,

            // PayPal Client IDs - replace with your own
            // Create a PayPal app: https://developer.paypal.com/developer/applications/create
            client: {
                sandbox: 'AZDxjDScFpQtjWTOUtWKbyN_bDt4OgqaF4eYXlewfBP4-8aqX3PiV8e1GWU6liB2CUXlkA59kJXE7M6R',
                production: '<insert production client id>'
            },

            payment: function (data, actions) {
                return actions.payment.create({
                    payment: {
                        transactions: [
                            {
                                amount: {
                                    total: '<?= number_format( $orderTotal + $processingCharge, 2, '.', '' ); ?>',
                                    currency: 'GBP'
                                }
                            }
                        ]
                    },

                    item_list: {
                       items: <?= json_encode( $basketItems ); ?>
                    }
                });
            },

            onAuthorize: function (data, actions) {
                return actions.payment.execute()
                .then(function () {
                    window.location.replace('checkoutSuccess.php');
                });
            }
        }, '#paypal-button-container');
    </script>
</html>
