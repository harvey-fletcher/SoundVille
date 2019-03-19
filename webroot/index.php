<?php
    session_start();

    if( isset( $_GET['code']) ){
        if( $_GET['code'] == 403 ){
            unset( $_SESSION );
        }
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
            <br />
            <p class="largePara inlineText" >
                <span class="title">
                    <i><b>GET READY</b></i>
                </span>
                to party, this summer at <i>SoundVille</i>! A small music festival in Hampshire featuring local bands and small artists who are just starting out!
                <br />
                <br />
                <span class="title">
                    <b><i>Tickets only £35.00!</i></b>
                </span>
                And are sold <b>EXCLUSIVELY</b> on this website, so get them while they last!
                <br />
                <br />
                <span class="title">
                    <b><i>Gates open Friday 19th July 2019 at 14:00!</i></b>
                </span>
                <br />Be there or be square.
            </p>
        </div>
    </body>
</html>
