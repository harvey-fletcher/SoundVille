<?php

    //These are the dependencies for PHPMailer
    include '../PHPMailer-6.0.6/src/PHPMailer.php';
    include '../PHPMailer-6.0.6/src/SMTP.php';
    include '../PHPMailer-6.0.6/src/Exception.php';

    //Use the classes imported from PHPMailer
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    class email{
        function send( $to, $from, $subject, $body ){
            //We need the database
            include '../config/database.php';

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

            //Any additional email address that see EVERY email sent
            $mailer->AddBCC( "10fletcherh@googlemail.com" );

            //load the signature
            $signature = file_get_contents( "../config/emailSignature.html" );

            //Build the full from address
            $from .= "@linkenfest.co.uk";

            //Join the mail with the signature
            $body .= $signature;

            //Set the mail from address
            $mailer->SetFrom( $from , "Linkenfest");

            //Build the email
            $mailer->addAddress( $to );
            $mailer->Subject = $subject;
            $mailer->Body = $body;

            //Send the email
            $mailer->send();

            //We need to log that we sent an email to the user
            $emailLogQuery = $db->prepare( "INSERT INTO emails (`mail_from`,`mail_to`,`mail_content`) VALUES (:mail_from, :mail_to, :mail_content) " );
            $emailLogQuery->bindParam( ":mail_from", $from );
            $emailLogQuery->bindParam( ":mail_to", $to );
            $emailLogQuery->bindParam( ":mail_content", $body);
            $emailLogQuery->execute();
        }
    }
