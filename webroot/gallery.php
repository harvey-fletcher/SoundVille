<?php
    session_start();

    if( isset( $_GET['code']) ){
        if( $_GET['code'] == 403 ){
            unset( $_SESSION );
        }
    }
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
            <br />
            <div>
                <img src="https://files.soundville.co.uk/tomClementsBanner_png.png" class="galleryHeader"/><br />
                <div class="fb-video" data-href="https://www.facebook.com/tomclementsmusic/videos/878630502346878/" data-allowfullscreen="false" data-width="400" data-show-text="false">
                    <blockquote cite="https://www.facebook.com/tomclementsmusic/videos/878630502346878/" class="fb-xfbml-parse-ignore">
                    <a href="https://www.facebook.com/tomclementsmusic/videos/878630502346878/"></a>
                    <p></p>
                    Posted by <a href="https://www.facebook.com/tomclementsmusic/">Tom Clements Music</a></blockquote>
                </div>
                <div class="fb-video" data-href="https://www.facebook.com/tomclementsmusic/videos/282354455965788/" data-width="200" data-show-text="false">
                    <blockquote cite="https://www.facebook.com/tomclementsmusic/videos/282354455965788/" class="fb-xfbml-parse-ignore">
                    <a href="https://www.facebook.com/tomclementsmusic/videos/282354455965788/"></a>
                    <p></p>
                    Posted by <a href="https://www.facebook.com/tomclementsmusic/">Tom Clements Music</a> on Sunday, 23 December 2018</blockquote>
                </div>
                &nbsp;&nbsp;&nbsp;&nbsp;<iframe width="475" height="360" src="https://www.youtube.com/embed/hetHk9wyL2k" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
            <br /><br /><br />
            More information available at <a href="https://tommycmusic.co.uk/"><u>https://tommycmusic.co.uk/</u></a> and on facebook <a href="https://www.facebook.com/tomclementsmusic/"><u>@tomclementsmusic</u></a><br /><br />
            <div class="container"><div class="fb-like" data-href="https://www.facebook.com/tomclementsmusic/" data-layout="standard" data-action="like" data-size="small" data-show-faces="true" data-share="false"></div></div>
        </div>
    </body>
</html>
