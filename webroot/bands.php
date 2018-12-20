<?php
    session_start();

    //Uses DB
    include '../config/database.php';

    //This page will send a mail
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
                "actName" => false,
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
        $insertPerformerRequest = $db->prepare( "INSERT INTO performer_requests ( performer_name, person_name, performer_email, performer_phone ) VALUES ( :performer_name, :person_name, :performer_email, :performer_phone )");
        $insertPerformerRequest->bindParam( ":performer_name", $_POST['actName'] );
        $insertPerformerRequest->bindParam( ":person_name", $_POST['personName'] );
        $insertPerformerRequest->bindParam( ":performer_email", $_POST['email'] );
        $insertPerformerRequest->bindParam( ":performer_phone", $_POST['phone'] );
        $insertPerformerRequest->execute();

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
               .         "Hello, A new performer request was received on the linkenfest site. Here are the details:<br /><br />"
               .         "Act Name: " . $_POST['actName'] . "<br />"
               .         "Person Name: " . $_POST['personName'] . "<br />"
               .         "Phone Number: " . $_POST['phone'] . "<br />"
               .         "Email Address: " . $_POST['email'] . "<br /><br />"
               .     "</h4><br /><br />"
               . "</div>";

        //This email gets sent to the user who completed the form
        $confirmationEmail = "<div style='width: 650'>"
               .     "<div style='float: left; width: 100px; height: 100px;'>"
               .         "<img src='https://files.linkenfest.co.uk/logo_png.png' style='width: 100px; height: 100px;' />"
               .     "</div>"
               .     "<div style='float: left; height: 100;' align='right'>"
               .         "<h1 style='margin: 0; font-size: 80px;'>Linkenfest</h1>"
               .     "</div>"
               . "</div>"
               . "<div style='width: 750; margin-top: 25px; display: inline-block;'>"
               .     "<h4 style='margin: 0;'>"
               .         "Hello,<br />"
               .         "We're just writing to let you know that we received your application to perform at Linkenfest.<br />"
               .         "We will be in touch with you shortly with further details.<br />"
               .         "<br />"
               .         "Many thanks,<br />The Linkenfest team.<br /><br />"
               .     "</h4><br /><br />"
               . "</div>";

        //Send the email
        include '../serverSide/emailScript.php';
        $email = new email();
        $email->send( "harvey.fletcher1@ntlworld.com", 'do-not-reply', "Linkenfest: New Band Application", $emailBody );
        $email->send( $_POST['email'] , 'do-not-reply', "Linkenfest: We received your application", $confirmationEmail );
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
                    <i><b>Want to perform at Linkenfest?</b></i>
                </span>
                It's simple. All you need to do is fill out the below form, and we will get in touch with you to arrange further details.<br /><br />
                Contract and Payment negotiation will take place after we have established contact with you.<br /><br />
                In order to perform at Linkenfest, you'll need to meet the following criteria:<br />
                <ul class="title">
                    <li>Be older than 18 years of age.</li>
                    <li>Be available on Friday 19th July from 17:30 to 23:30, your performance will be scheduled between these times.</li>
                    <li>Have at least 1 example of a prior gig, and the contact details of the organiser.</li>
                    <li>Have your own transport to and from Linkenfest.</li>
                    <li>Have a valid form of photo ID that is not expired.</li>
                </ul>
                <br />
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
                                Performer Name:&nbsp;
                            </td>
                            <td>
                                <input type="text" name="actName" class="signInWidgetControls" />
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
                                <input type="text" name="email" class="signInWidgetControls" required />
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
                                I have read the criteria for performing at Linkenfest and confirm I meet all requirements.*
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
                                You also agree to be contacted by Linkenfest regarding your performance. You will not be contacted for any reason other than this.
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="g-recaptcha" data-sitekey="6LcOKn4UAAAAALBQMY5TPjp-mLoZcPBauPsg4c9I" data-callback="confirmCaptcha"></div>
                                <button type="submit" name="submit" class="largeFormSubmit">Apply</button>
                            </td>
                        </tr>
                </form>
            </p>
        </div>
    </body>
</html>
