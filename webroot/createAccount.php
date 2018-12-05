<?php
    session_start();

    //Uses DB
    include '../config/database.php';

    //This page will send a mail
    include '../config/dependencies.php';
    $dependencies = new Dependencies();

    //We need the access controller for user existance checking
    include '../controllers/accessController.php';
    $access = new accessController();

    //By default, there is a failure
    $error = false;
    $errorText = "An unexpected error has occurred.";

    //There is no success
    $success = false;

    //Has the form been submitted?
    if( isset( $_POST['submit'] ) ){
        //These are the fields that should be on the form
        //Value is true if it is a required field
        $fields = array(
                "email" => true,
                "password" => true,
            );

        //Check if all the required fields were submitted
        foreach( $fields as $name=>$required ){
            if( isset( $_POST[ $name ] ) ){
                //If the field is a required field and is null, throw error
                if( ( strlen( trim( $_POST[ $name ] ) ) == 0) && $required ){
                    $error = true;
                    $errorText = "Field " . $name . " cannot be blank";
                } else {
                    //Success!
                    $success = true;
                }
            } else {
                $error = true;
                $errorText = "You are missing required field " . $name;
            }
        }
    }

    if( $_POST['password'] != $_POST['passwordConfirm'] ){
        $error = true;
        $errorText = "The passwords did not match. Please try again.";
    }

    //Check the user don't exist
    if( $access->userExists( $_POST['email'] ) ){
        $error = true;
        $errorText = "A user with that email is already registered.";
    }

    //If there's an error, it's impossible to display success
    if( $error ){
        $success = false;
    }

    if( $success ){
        //Insert the new user account
        $query = $db->prepare( "INSERT INTO users ( email, password ) VALUES ( :email, :password )" );
        $query->bindParam( ":email", $_POST['email'] );
        $query->bindParam( ":password", password_hash( $_POST['passwordConfirm'] , PASSWORD_DEFAULT) );
        $query->execute();
        $userID = $db->lastInsertId();

        //Create a new unique reference
        $uuid = hash('sha1', $_POST['email'] . date('Y-m-d H:i:s') );
        $actionName = "activate";

        $query = $db->prepare("INSERT INTO pending_user_updates( unique_identifier, user_id, do_action, update_column, old_value, new_value ) VALUES ( :uuid, :user_id, :do_action, :update_column, 0, 1 )");
        $query->bindParam( ":uuid", $uuid );
        $query->bindParam( ":user_id", $userID );
        $query->bindParam( ":do_action", $actionName );
        $query->bindParam( ":update_column", $actionName );

        $query->execute();

        //Make a mail
        //Set the mail parameters
        $mailer = $dependencies->mailer();
        $mailer->addAddress( $_POST['email'] );
        $mailer->Subject = "Linkenfest: Activate your account!";

        $emailBody = "<div style='width: 650'>"
               .     "<div style='float: left; width: 100px; height: 100px;'>"
               .         "<img src='https://files.linkenfest.co.uk/logo_png.png' style='width: 100px; height: 100px;' />"
               .     "</div>"
               .     "<div style='float: left; height: 100;' align='right'>"
               .         "<h1 style='margin: 0; font-size: 80px;'>Linkenfest</h1>"
               .     "</div>"
               . "</div>"
               . "<div style='width: 750; margin-top: 25px; display: inline-block;'>"
               .     "<h4 style='margin: 0;'>"
               .         "Hello, someone just tried to create an account at https://linkenfest.co.uk. If this was you, please click the link below:<br /><br />"
               .         "https://linkenfest.co.uk/completePendingAction.php?identifier=" . $uuid . "<br /><br />"
               .     "</h4><br /><br />"
               .     "Questions? Contact us!<br />0751 174 9870<br />https://www.linkenfest.co.uk"
               . "</div>";

        $mailer->Body = $emailBody;
        $mailer->send();
    }
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
            <br />
            <p class="largePara inlineText" >
                <?php if( isset( $_POST['submit'] ) ){ ?>
                    <?php if( $error ){ ?>
                        <h1 class="warning noMargin"><?= $errorText; ?></h1>
                    <?php } ?>
                    <?php if( $success ){ ?>
                        <h1 class="success noMargin">Success! Check your email for activation link.<br /><br /></h1>
                    <?php } ?>
                <?php } ?>
                <span class="title">
                    <i><b>Create account</b></i>
                </span>
                <form name="bandSignUpForm" action="" method="POST" class="title">
                    <table width="75%" align="center">
                        <tr>
                            <td colspan="2" align="center">
                                * marks required field.
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td class="title" align="right">
                                Your email*:&nbsp;
                            </td>
                            <td>
                                <input type="text" name="email" class="signInWidgetControls" required"/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td class="title" align="right">
                                Password*:&nbsp;
                            </td>
                            <td>
                                <input type="text" name="password" class="signInWidgetControls" required/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td class="title" align="right">
                                Confirm Password*:&nbsp;
                            </td>
                            <td>
                                <input type="text" name="passwordConfirm" class="signInWidgetControls" required/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td class="title" align="right">
                                I have read and agree to the terms and conditions and privacy statement listed on the <a href="info.php">information</a> page.*
                            </td>
                            <td align="center">
                                <input type="checkbox" name="confirmTermsCheckbox" class="largeCheckbox" required/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button type="submit" name="submit" class="largeFormSubmit">Create Account</button>
                            </td>
                        </tr>
                    </table>
                </form>
            </p>
        </div>
    </body>
</html>
