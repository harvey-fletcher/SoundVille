<?php
    //Uses session
    session_start();
?>
<html>
    <head>
        <link rel="stylesheet" href="main.css" type="text/css"/>
        <title>Linkenfest 2019</title>
    </head>
    <body onload="doPayment()">
        <img src="https://files.linkenfest.co.uk/logo_png.png" class="main-logo"/>
        <div class="signInWidget">
            <?php include 'signInWidget.php'; ?>
        </div>
        <div class="links" align="right">
            <?php include 'menu.php'; ?>
        </div>
        <div class="mainBodyContainer" align="center">
            <h1 class="noMargin">
                Please wait.
            </h2>
            <h4>
                We're currently processing your order.<br />Please <b>DO NOT</b> refresh this page.
            </h4>
        </div>
    </body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript">
        function doPayment(){
            $.post(
                "https://api.linkenfest.co.uk/checkout/payment",
                {
                  session: '<?= session_id(); ?>',
                  stripeToken: '<?= $_POST["stripeToken"] ?>'
                }
            ).done(function( data ){
                processOrder( data.data.order_id );
            });
        }

        function processOrder( order_ref ){
            $.post(
                "https://api.linkenfest.co.uk/checkout/processOrder",
                {
                    session: '<?= session_id(); ?>',
                    orderReference: order_ref,
                    secretCode: '<?= $_POST["secretCodeConfirmed"]; ?>',
                }
            ).done(function( data ){
                alert(data.data.message);
                window.location.replace('checkoutSuccess.php');
            });
        }
    </script>
</html>
