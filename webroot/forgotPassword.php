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
            <h1 class="noMargin">Reset Password</h1>
            <p class="largePara inlineText" id="mainText" >
                Unfortunately, we can't give you your password back as it is encrypted. But we can change it to something and email you a new one.
                <br /><br />
                <input type="text" id="userEmail" name="userEmail" class="doubleFormControl" placeholder="Enter your email address here" /><br /><br />
                <button onclick="resetPassword()" class="doubleFormControl" id="resetButton">Reset Password</button>
            </p>
        </div>
    </body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript">
        function resetPassword(){
            var userEmail = $('#userEmail').val();

            //Disable the input and submit fields
            $('#userEmail').prop( 'disabled', true );
            $('#resetButton').prop( 'disabled', true );

            $.post(
                "https://api.linkenfest.co.uk/access/reset_password",
                {
                    email: userEmail
                }
            ).done(function( data ){
                $('#mainText').html( data.data.message );
            });

        }
    </script>
</html>
