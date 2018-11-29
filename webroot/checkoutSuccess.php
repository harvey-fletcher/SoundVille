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
    $mailer->Body = "<p>This is a test.</p>";
    $mailer->send();
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
            <h1 class="noMargin">
                Thank You!
            </h2>
            <h4>
                Your order has been created, and a confirmation email sent to `<?= $_SESSION['email']; ?>`<br /><br />
                Please keep it safe, and bring a printed copy with you to the event.
            </h4>
        </div>
    </body>
</html>
