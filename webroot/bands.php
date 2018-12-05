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
            <br />
            <p class="largePara inlineText" >
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
                    <table>
                        <tr>
                            <td colspan="2" align="center">
                                * marks required field.
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
                            <td class="title" align="right">
                                Your Name*:&nbsp;
                            </td>
                            <td>
                                <input type="text" name="personName" class="signInWidgetControls" required/>
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
                            <td class="title" align="right">
                                Phone Number*:&nbsp;
                            </td>
                            <td>
                                <input type="text" name="phone" class="signInWidgetControls" required/>
                            </td>
                        </tr>
                        <tr>
                            <td class="title" align="right">
                                I have read the<br />criteria for performing<br />at Linkenfest and<br />confirm I meet all<br />requirements.*
                            </td>
                            <td align="center">
                                <input type="checkbox" name="over18checkbox" class="largeCheckbox" required/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="title" align="center">
                                By clicking the below button,<br /> you confirm that the details<br />that you have provided<br />above, are accurate<br />and true.<br /><br />
                                You also agree to be contacted<br />by Linkenfest regarding your<br />performance. You will not be contacted<br />for any reason other than<br />this.
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button type="submit" class="largeFormSubmit">Apply</button>
                            </td>
                        </tr>
                </form>
            </p>
        </div>
    </body>
</html>
