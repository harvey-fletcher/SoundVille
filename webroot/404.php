<?php
    session_start();
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
            <h1 class="noMargin">Oops...</h1>
            <h4 class="noMargin">We're sorry, we couldn't find that page.</h4>
            <br />
            <a href="javascript:history.go(-1)" class="pageLink">Go Back</a>
        </div>
    </body>
</html>
