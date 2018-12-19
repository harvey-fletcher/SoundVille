<?php
    $referrer = str_replace( "/", "", $_SERVER['PHP_SELF'] );

    //Are we starting a session?
    if( isset( $_GET['session'] ) ){
        if( sizeof( $_SESSION ) == 0 ){
            session_destroy();
        }
        session_id( $_GET['session'] );
        session_start();
    }

    if( !isset( $_SESSION['email'] ) ){
?>
        <p class="noMargin warning" id="errorMessageLoginForm"></p>
        <input type="text" id="email" name="email" placeholder="E-mail address" class="signInWidgetControls">
        <input type="password" id="password" name="password" placeholder="Password" class="signInWidgetControls">
        <button name="loginButton" id="loginButton" onclick="doLogin()" class="signOutButton">Sign In</button>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script type="text/javascript">
            function doLogin(){
                var userEmail = $("#email").val();
                var userPassword = $("#password").val();

                $.post(
                    "https://api.linkenfest.co.uk/access/login/<?= $referrer ?>",
                    {
                      email: userEmail,
                      password: userPassword
                    }
                ).done(function( data ){
                    var status   = data.data.status;
                    var referrer = data.data.referrer;
                    var message  = data.data.message;
                    var session  = data.data.session;

                    //Set the info text
                    $('#errorMessageLoginForm').html( message );

                    if( status == 200 ){
                      window.location.replace( referrer + "?session=" + session );
                    }
                });
            }
        </script>
<?php
    } else {
?>
        <a href="basket.php">
            <button class="signOutButton" id="cartButton">
                Basket (<?= $_SESSION['basketSize']; ?>)
            </button>
        </a>
        <a href="checkout.php">
            <button class="signOutButton">
                Checkout
            </button>
        </a>
        <a href="signOut.php?referrer=<?= $referrer; ?>">
            <button class="signOutButton">
                Logout
            </button>
        </a>
<?php
    }
?>
