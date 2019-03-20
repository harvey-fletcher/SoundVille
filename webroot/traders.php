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
        $insertVolunteerRequest = $db->prepare( "INSERT INTO vendor_requests ( name, email, phone ) VALUES ( :name, :email, :phone )");
        $insertVolunteerRequest->execute(array(
            ":name"  => $_POST['personName'],
            ":email" => $_POST['email'],
            ":phone" => $_POST['phone']
        ));

        //Make a mail
        $emailBody = "<div style='width: 650'>"
               .     "<div style='float: left; width: 100px; height: 100px;'>"
               .         "<img src='https://files.soundville.co.uk/logo_png.png' style='width: 100px; height: 100px;' />"
               .     "</div>"
               .     "<div style='float: left; height: 100;' align='right'>"
               .         "<h1 style='margin: 0; font-size: 80px;'>SoundVille</h1>"
               .     "</div>"
               . "</div>"
               . "<div style='width: 750; margin-top: 25px; display: inline-block;'>"
               .     "<h4 style='margin: 0;'>"
               .         "Hello, A new vendor request was received on the SoundVille site. Here are the details:<br /><br />"
               .         "Person Name: " . $_POST['personName'] . "<br />"
               .         "Phone Number: " . $_POST['phone'] . "<br />"
               .         "Email Address: " . $_POST['email'] . "<br /><br />"
               .     "</h4><br /><br />"
               . "</div>";

        //Send the mail
        include '../serverSide/emailScript.php';
        $email = new email();
        $email->send( "harvey.fletcher1@ntlworld.com", "do-not-reply", "SoundVille: New vendor request", $emailBody );
    }
?>
<html>
    <head>
        <link rel="stylesheet" href="main.css" type="text/css"/>
        <title>SoundVille 2019</title>
        <script src='https://www.google.com/recaptcha/api.js'></script>
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
                <?php if( isset( $_POST['submit'] ) ){ ?>
                    <?php if( $error ){ ?>
                        <h1 class="warning noMargin"><?= $errorText; ?></h1>
                    <?php } ?>
                    <?php if( $success ){ ?>
                        <h1 class="success noMargin">Success! We will be in touch shortly.<br /><br /></h1>
                    <?php } ?>
                <?php } ?>
                <span class="title">
                    <i><b>Apply to be a vendor at SoundVille</b></i>
                </span>
                You must register here first if you wish to trade goods at the festival. There is a charge of &pound;20, this will give you the following:<br />
                <ul>
                    <li>Permission to trade goods at SoundVille</li>
                    <li>A space within the festival grounds to park your vehicle and set up your trade stall</li>
                    <li>2 vendor weekend tickets are included in the price.</li>
                    <li>Come and go whenever you want across the weekend.</li>
                    <li>You can keep all the money you make at the festival. We do not charge commission.</li>
                </ul>
                <br />
                <br />
                In order to be a trader at SoundVille, you'll need to meet the following conditions:
                <ul class="title">
                    <li>Be older than 18 years of age.</li>
                    <li>Have your own insurance policies, and understand that your equipment and employees will not be covered by the event insurance policy.</li>
                    <li>If you are a food vendor, you must have the required licenses.</li>
                    <li>Have a valid form of photo ID that is not expired.</li>
                    <li>Can pay the trader registration fee of &pound;20. This is for 2 people. Additional staff are charged at &pound;8 per person.</li>
                </ul>
                <br />
                Due to licensing restrictions, the sale of alcohol is prohibited at this event.
                If you meet these conditions, and would like to apply as a trader, please fill out the form below.
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
                                    I have read the criteria for trading at SoundVille and confirm I meet all requirements.*
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
                                    <div class="g-recaptcha" data-sitekey="6LcOKn4UAAAAALBQMY5TPjp-mLoZcPBauPsg4c9I" data-callback="confirmCaptcha"></div>
                                    <button type="submit" name="submit" class="largeFormSubmit">Apply</button>
                                </td>
                            </tr>
                    </form>
                <?php } else { ?>
                    You must be signed in to apply. <a href="createAccount.php"><u>Click here to register</u></a>
                <?php } ?>
            </p>
        </div>
    </body>
</html>
