<?php
    session_start();

    //Uses DB
    include '../config/database.php';

    //uses dependencies
    include '../config/dependencies.php';
    $dependencies = new Dependencies();

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
                "personName" => true,
                "email" => true,
                "phone" => true,
                "confirmTermsCheckbox" => true,
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

        //Was the captcha correct?
        if( !$dependencies->confirmCaptcha( $_POST['g-recaptcha-response'] ) ){
            $error = true;
            $errorText = "You did not successfully complete the CAPTCHA";
        }
    }

    //If there's an error, it's impossible to display success
    if( $error ){
        $success = false;
    }

    if( $success ){
        //If there's a  success, insert to DB
        //Prepare to insert the data into the table
        $insertVolunteerRequest = $db->prepare( "INSERT INTO performer_requests ( name, email, phone ) VALUES ( :name, :email, :phone )");
        $insertVolunteerRequest->bindParam( ":name", $_POST['name'] );
        $insertVolunteerRequest->bindParam( ":email", $_POST['email'] );
        $insertVolunteerRequest->bindParam( ":phone", $_POST['phone'] );
        $insertVolunteerRequest->execute();

        //Make a mail
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
               .         "Hello, A new volunteer request was received on the linkenfest site. Here are the details:<br /><br />"
               .         "Person Name: " . $_POST['personName'] . "<br />"
               .         "Phone Number: " . $_POST['phone'] . "<br />"
               .         "Email Address: " . $_POST['email'] . "<br /><br />"
               .     "</h4><br /><br />"
               . "</div>";

        //Send the mail
        include '../serverSide/emailScript.php';
        $email = new email();
        $email->send( "harvey.fletcher1@ntlworld.com", "do-not-reply", "Linkenfest: New volunteer request", $emailBody );
    }
?>
<html>
    <head>
        <link rel="stylesheet" href="main.css" type="text/css"/>
        <title>Linkenfest 2019</title>
        <script src='https://www.google.com/recaptcha/api.js'></script>
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
                        <h1 class="success noMargin">Success! We will be in touch shortly.<br /><br /></h1>
                    <?php } ?>
                <?php } ?>
                <span class="title">
                    <i><b>Want to volunteer at Linkenfest?</b></i>
                </span>
                Volunteers will receive 30% off* tickets in exchange for 2x 1.5 hour shifts ( 3 hour total ) across the Linkenfest weekend, as well as the option for designated private camping in a seperate area of the field if they wish.
                <br />
                <br />
                In order to volunteer at Linkenholt, you'll need to meet the following conditions:
                <ul class="title">
                    <li>Be older than 18 years of age.</li>
                    <li>Be available from Friday 19th July at 09:00 to Sunday 21st July 21:00, your volunteer shift will be scheduled between these times.</li>
                    <li>Have your own transport to and from Linkenfest.</li>
                    <li>Have a valid form of photo ID that is not expired.</li>
                    <li>Can pay the deposit fee (full price ticket). You will be refunded 30% after the event</li>
                </ul>
                If you meet these conditions, and would like to volunteer, please fill out the form below.
                <br /><br />
                <?php if( isset( $_SESSION['email'] ) ){ ?>
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
                                    Your Name*:&nbsp;
                                </td>
                                <td>
                                    <input type="text" name="personName" class="signInWidgetControls" required/>
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
                                    <input type="text" name="email" class="signInWidgetControls" required value="<?= $_SESSION['email']; ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    &nbsp;
                                </td>
                            </tr>
                            <tr>
                                <td class="title" align="right">
                                    Phone Number*:&nbsp;
                                </td>
                                <td>
                                    <input type="text" name="phone" class="signInWidgetControls" required/>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    &nbsp;
                                </td>
                            </tr>
                            <tr>
                                <td class="title" align="right">
                                    I have read the criteria for volunteering at Linkenfest and confirm I meet all requirements.*
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
                                <td colspan="2" class="title" align="center">
                                    By clicking the below button, you confirm that the details that you have provided above, are accurate and true.<br /><br />
                                    You also agree that you will be contacted by Linkenfest, and agree to the following conditions:<br />
                                    <ul>
                                        <li>No-Shows will not be refunded their deposit fee or the 30% refund</li>
                                        <li>If accepted, you will need to purchase a full price weekend ticket, the 30% off will be refunded to you after you have completed your assigned shifts.</li>
                                        <li>Should the event be cancelled, you will receive a full deposit refund.</li>
                                        <li>Tickets purchased prior to volunteer approval will not be deducted.</li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="g-recaptcha" data-sitekey="6LcOKn4UAAAAALBQMY5TPjp-mLoZcPBauPsg4c9I" data-callback="confirmCaptcha"></div>
                                    <button type="submit" name="submit" class="largeFormSubmit">Apply</button>
                                </td>
                            </tr>
                    </form>
                <?php } else { ?>
                    You must be signed in to apply.
                <?php } ?>
            </p>
        </div>
    </body>
</html>
