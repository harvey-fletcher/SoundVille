<?php
    //Uses session
    session_start();

    //This page should only be accessible if the user is signed in
    include 'sessionAccess.php';

    //Need to see the products in the basket
    include '../controllers/basketController.php';
    $basketController = new basketController();
    $basket = $basketController->getContents();

    //By default, there are no issues in the basket
    $canCheckout = true;

    //Is checkout allowed?
    $checkoutOpen = true;
?>
<html>
    <head>
        <link rel="stylesheet" href="main.css" type="text/css"/>
        <title>SoundVille 2019</title>
        <script src='https://www.google.com/recaptcha/api.js'></script>
    </head>
    <body onload="">
        <img src="https://files.soundville.co.uk/logo_png.png" class="main-logo"/>
        <div class="signInWidget">
            <?php include 'signInWidget.php'; ?>
        </div>
        <div class="links" align="right">
            <?php include 'menu.php'; ?>
        </div>
        <div class="mainBodyContainer" align="center">
            <h2 class="noMargin"><u>Please confirm your order</u></h2>
            <br />
            <?php foreach( $basket['basket_items'] as $key=>$product ){ ?>
                <div class="productParent" align="left">
                    <div class="productImageContainer">
                        <img src="<?= $product['image_url']; ?>" width="100%" height="100%" />
                        <?php if( $product['promotion'] == '1' ){ ?>
                            <img src='http://www.uppercutsmeat.com/wp-content/uploads/2018/02/Special-Offer-Image-Chiropractor-Canberra.png' class='specialOfferBanner' />
                        <?php } ?>
                    </div>
                    <div class="productDetails">
                        <h2 class="noMargin">
                            <?= $product['product_name'];?><br  />
                            £<?= $product['product_price'];?> each<br />
                            <br />
                            Quantity: <?= $product['quantity'];?><br />
                            Total Price ( <?= $product['quantity'];?> X £<?= $product['product_price'];?> ): £<?= $product['item_total'];?>
                        </h2>
                        <br />
                        <?php if( $product['quantity'] > $product['product_max_per_purchase'] ){ ?>
                           <h3 class="warning noMargin">
                               You have selected too many of this item and you will not be able to check out.<br />
                               You must first amend your order in the basket.
                           </h3>
                           <?php
                               //This error should stop the user from being able to check out
                               $canCheckout = false;
                           ?>
                        <?php } ?>
                        <?php if(!$product['in_stock']){ ?>
                            <h3 class="warning noMargin">
                                There is not enough of this product remaining.<br />
                                You will need to decrease your selection before you can check out.<br />
                            </h3>
                            <?php
                                //This error should stop the user from being able to check out
                                $canCheckout = false;
                            ?>
                        <?php } ?>
                    </div>
                </div>
            <?php
            }
            ?> 
            <?php if( sizeof($basket['basket_items']) > 0 ){ ?>
                <div class="productParent">
                    <div class="orderOptions" align="right">
                        <h3 class="noMargin">
                            Order SubTotal: £<?= $basket['sub_total']; ?><br />
                            Processing Charge: £<?= $basket['processing_fee']; ?> <a href="info.php?section=fees">(?)</a><br />
                            <br />
                            Order Total: £<span id='totalDisplay'><?= $basket['order_total']['decimal']; ?></span><br />
                            <br />
                            <p class="smallPrint">By clicking the button below, you agree to make this purchase, our privacy policy, and cancellation policy.</p>
                            <?php if( $canCheckout && $checkoutOpen ){?>
                                <div id="secretCodeContainer">
                                    <p class="noMargin">
                                        If you have a promo code, enter it here to receive a <b>free</b> printed copy of set times.<br />
                                        Otherwise, click continue.
                                    </p>
                                    <input type="text" id="secretCode" name="secretCode" placeholder="Promo Code" class="doubleFormControl"/><br /><br />
                                    <button onclick="verifySecretCode();" class="doubleFormControl">Continue</button><br /><br />
                                </div>
                                <div id="paymentForm" class="hidden">
                                    <div class="g-recaptcha" data-sitekey="6LcOKn4UAAAAALBQMY5TPjp-mLoZcPBauPsg4c9I" data-callback="confirmCaptcha"></div>
                                    <form action="processPayment.php" method="POST" style='display: none' id="checkout-form">
                                    </form>
                                </div>
                            <?php } else if( !$checkoutOpen ) { ?>
                                <h3 class="warning noMargin">
                                    The checkout is currently disabled<br />We're sorry for any inconvenience this causes.
                                </h3>
                            <?php } else { ?>
                                <h3 class="warning noMargin">
                                    There is an issue with your basket.<br />
                                    Please correct this issue <a href="basket.php">here</a> to continue</a>
                                </h3>
                            <?php } ?>
                        </h3>
                    </div>
                </div>
            <?php } else { ?>
                <h4 class="noMargin">Your basket is empty</h4>
            <?php } ?>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script type='text/javascript'>
            function verifySecretCode(){
                var SecretCode = $('#secretCode').val();

                $.post(
                    "https://api.soundville.co.uk/checkout/verifySecretCode",
                    {
                        session: '<?= session_id(); ?>',
                        secretCode: SecretCode
                    }
                ).done(function (data){
                    if( !data.data.codeValid ){
                        SecretCode = "XXXXXX";
                    }

                    //The total amount of the order
                    var orderTotal =  '<?= $basket["order_total"]["decimal"]; ?>';

                    //If there is a discount percentage
                    if( parseInt( data.data.discount ) > 0 ){
                        //Work out the new value of the total
                        orderTotal = ( ( parseInt( '<?= $basket["order_total"]["plain"]; ?>' ) / 100 ) * ( 1 - ( data.data.discount / 100 ) ) ).toFixed(2);

                        //Alert that a discount has been applied
                        alert( data.data.discount + "% discount applied. New total: £" + orderTotal );

                        //Update the display of the total
                        $("#totalDisplay").text( orderTotal );
                    }

                    //Create a new checkout form
                    $("#checkout-form")
                    .append(
                        $("<input></input>")
                             .attr("id", "secretCodeConfirmed")
                             .attr("name", "secretCodeConfirmed")
                             .attr("hidden", true)
                             .attr("type", "text")
                             .val( SecretCode )
                    )
                    .append(
                        $("<script>")
                            .attr("src", "https://checkout.stripe.com/checkout.js")
                            .addClass("stripe-button")
//                            .attr("data-key", "pk_test_RZIMubVhyxMcq7jNaWpiZGrX")
                            .attr("data-key", "pk_live_CQKwBSpMlqkJDj1l1hfBG1aE")
                            .attr("data-amount", (orderTotal * 100 ).toFixed(0) )
                            .attr("data-name", "SoundVille")
                            .attr("data-description", "Complete Purchase")
                            .attr("data-email", "<?= $_SESSION['email']; ?>")
                            .attr("data-image", "https://files.soundville.co.uk/logo_png.png")
                            .attr("data-locale", "auto")
                            .attr("data-currency", "gbp")
                    )

                    $('#secretCodeContainer').addClass('hidden');
                    $('#paymentForm').removeClass('hidden');
                });
            }

            function confirmCaptcha( data ){
                $.post(
                    "verifyCaptcha.php",
                    { response: data }
                ).done(function( data ){
                    if( data.status == 200 ){
                        $("#checkout-form").toggle();
                    } else {
                        alert( data.message );
                    }
                });
            }
        </script>
    </body>
</html>
