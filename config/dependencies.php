<?php

    //These are the dependencies for PHPMailer
    include '../PHPMailer-6.0.6/src/PHPMailer.php';
    include '../PHPMailer-6.0.6/src/SMTP.php';
    include '../PHPMailer-6.0.6/src/Exception.php';

    //Use the classes imported from PHPMailer
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    class Dependencies{

        function mailer(){
            //Initialise a mailer
            $mailer = new PHPMailer();

            //We send the mail
            $mailer->isSMTP();
            $mailer->Host = 'smtp.gmail.com';

            //The SMTP server needs credentials
            $mailer->SMTPAuth = true;
            $mailer->Username = '10fletcherh@googlemail.com';
            $mailer->Password = 'vlyfylsgcgspupod';
            $mailer->Port = 465;
            $mailer->SMTPSecure = 'ssl';

            //The mail that will be sent is HTML
            $mailer->IsHTML(true);

            //The address that the mailer is using to send mails
            $mailer->SetFrom("do-not-reply@linkenfest.co.uk", "Linkenfest");

            //Any additional email address that see EVERY email sent
            $mailer->AddBCC( "10fletcherh@googlemail.com" );

            return $mailer;
        }

    }
