<?php
    session_start();

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
    }

    //If there's an error, it's impossible to display success
    if( $error ){
        $success = false;
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
                        <h1 class="success noMargin">Success! We will be in touch shortly.<br /><br /></h1>
                    <?php } ?>
                <?php } ?>
                <span class="title">
                    <i><b>Want to perform at Linkenfest?</b></i>
                </span>
                It's simple. All you need to do is fill out the below form, and we will get in touch with you to arrange further details.<br /><br />
                In order to perform at Linkenfest, you'll need to meet the following criteria:<br />
                <ul class="title">
                    <li>Be older than 18 years of age.</li>
                    <li>Be available on Friday 19th July from 09:00 to 23:30, your performance will be scheduled between these times.</li>
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
                                <button type="submit" name="submit" class="largeFormSubmit">Apply</button>
                            </td>
                        </tr>
                </form>
            </p>
        </div>
    </body>
</html>
