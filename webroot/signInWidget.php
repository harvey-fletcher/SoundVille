<?php
    $referrer = str_replace( "/", "", $_SERVER['PHP_SELF'] );

    if( !isset( $_SESSION['email'] ) ){
?>
        <form name="signInForm" method="POST" action="signIn.php?referrer=<?= $referrer ?>">
            <input type="text" name="email" placeholder="E-mail address" class="signInWidgetControls">
            <input type="password" name="password" placeholder="Password" class="signInWidgetControls">
            <input type="submit" value="Sign In" class="signInWidgetControls">
        </form>
<?php
    } else {
?>
        <a href="signOut.php?referrer=<?= $referrer; ?>">
            <button class="signOutButton">
                Logout
            </button>
        </a>
<?php
    }
?>
