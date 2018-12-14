<?php
    //Uses session
    session_start();
?>
<html>
    <head>
        <link rel="stylesheet" href="main.css" type="text/css"/>
        <title>Linkenfest 2019</title>
    </head>
    <body onload="processOrder()">
        <img src="https://files.linkenfest.co.uk/logo_png.png" class="main-logo"/>
        <div class="signInWidget">
            <?php include 'signInWidget.php'; ?>
        </div>
        <div class="links" align="right">
            <?php include 'menu.php'; ?>
        </div>
        <div class="mainBodyContainer" align="center">
            <h1 class="noMargin">
                Thank You!
            </h2>
            <h4>
                Your order has been created, and a confirmation email sent to `<?= $_SESSION['email']; ?>`<br /><br />
                Please keep it safe, and bring a printed copy with you to the event.
            </h4>
        </div>
    </body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript">
        function processOrder(){
            $.post(
                "https://api.linkenfest.co.uk/checkout/processOrder",
                { session: '<?= session_id(); ?>' }
            ).done(function( data ){
                alert(data.data.message);
            });
        }
    </script>
</html>
