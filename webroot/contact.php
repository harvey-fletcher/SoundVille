<?php
    session_start();
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
            <p class="largepara">
                If you want to get hold of us, you can do this using one of the methods below<br /><br />
                <span class="title">
                    By Email
                </span><br />
                &nbsp;&nbsp;&nbsp;&nbsp;<span id="eml_1_pt_1"></span>@<span id="eml_1_pt_2"></span>.co.uk<br /><br />
                <span class="title">
                    By Phone
                </span><br />
                &nbsp;&nbsp;&nbsp;&nbsp;+44 (0) 751 174 987 0<br /><br />
                <span class="title">
                    By Post
                </span><br />
                &nbsp;&nbsp;&nbsp;&nbsp;SoundVille Information<br />
                &nbsp;&nbsp;&nbsp;&nbsp;24 Saffron Close<br />
                &nbsp;&nbsp;&nbsp;&nbsp;Newbury<br />
                &nbsp;&nbsp;&nbsp;&nbsp;West Berkshire<br />
                &nbsp;&nbsp;&nbsp;&nbsp;RG14 1XD<br /><br />
                We aim to respond to all contacts within 72 hours, if contacting us by post, please allow up to 3 working days for delivery for your letter to reach us.<br /><br />
                An emergency contact number will be posted here during the event should you need it.
            </p>
        </div>
        <script type="text/javascript">
            window.addEventListener("load", function(){
              //eml_pt_1
              //eml_pt_2
              //Assemble the email address, we do this here to prevent bots from scraping the web page.
              $("#eml_1_pt_1")
                  .text( "information" );

               $("#eml_1_pt_2")
                  .text( "soundville" );
            })
        </script>
    </body>
</html>
