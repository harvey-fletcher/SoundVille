<?php

    //Create a new object which can be accessed
    $access = new accessController();

    //The access controller class
    class accessController
    {
        function userExists( $email = NULL )
        {
            global $db;

            //Check that the email is set
            if( $email == NULL )
            {
                return false;
            }

            //Create the query to identify user rows
            $loginQuery = $db->prepare("SELECT * FROM users WHERE email=:email");

            //bind the parameters to the query
            $loginQuery->bindParam( ":email", $email );

            //Execute the result
            $loginQuery->execute();

            //Get the results
            $results = $loginQuery->fetchAll( PDO::FETCH_ASSOC );

            //How many rows are there
            if( sizeof( $results ) == 1 )
            {
                return $results[0];
            } else {
                return false;
            }
        }

        function resetPassword( $email ){
            //Uses DB
            global $db;

            //Entity that can be used
            $entity = array(
                    "a",
                    "b",
                    "c",
                    "d",
                    "e",
                    "f",
                    "g",
                    "h",
                    "i",
                    "j",
                    "k",
                    "l",
                    "m",
                    "n",
                    "o",
                    "p",
                    "q",
                    "r",
                    "s",
                    "t",
                    "u",
                    "v",
                    "w",
                    "x",
                    "y",
                    "z",
                    "0",
                    "1",
                    "2",
                    "3",
                    "4",
                    "5",
                    "6",
                    "7",
                    "8",
                    "9"
                );

            //A container for the new password
            $newPassword = "";

            //Generate the new password
            for( $x=0; $x<12; $x++){
                //Select a random character
                $char = $entity[ rand( 0, 35 ) ];

                //Randomly decide if it is uc
                $selector = rand(0, 1);
                if( $selector == 1){
                    $char = ucwords( $char );
                }

                //Append the new character to the new password
                $newPassword .= $char;
            }

            //Hash the new password
            $passwordPlainText = $newPassword;
            $newPassword = password_hash( $newPassword, PASSWORD_DEFAULT );

            //Get the user id of the user requesting the new password
            $query = $db->prepare( "SELECT id FROM users WHERE email=:email" );
            $query->bindParam( ":email", $email );
            $query->execute();
            $users = $query->fetchAll( PDO::FETCH_ASSOC );

            //Check the user exists
            if( sizeof($users) != 1 ){
                return array( "status" => 400, "message" => "Error changing password. Check the email address you entered is correct." );
            }

            //Get the user ID
            $userID = $users[0]['id'];

            //Build the query which stores the data in the DB, for email update
            //Create a new unique reference
            $uuid = hash('sha1', $_POST['email'] . date('Y-m-d H:i:s') );
            $actionName = "resetpass";
            $updateColumn = "password";

            $query = $db->prepare("INSERT INTO pending_user_updates( unique_identifier, user_id, do_action, update_column, old_value, new_value ) VALUES ( :uuid, :user_id, :do_action, :update_column, 0, :newPassword )");
            $query->bindParam( ":uuid", $uuid );
            $query->bindParam( ":user_id", $userID );
            $query->bindParam( ":do_action", $actionName );
            $query->bindParam( ":update_column", $updateColumn );
            $query->bindParam( ":newPassword", $newPassword );

            $query->execute();

            //OK, now that's done, we need to build an email containing a password reset link
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
                   .         "Hello, someone just tried to reset your password on https://soundville.co.uk. If this was you, please click the link below:<br /><br />"
                   .         "<a href='https://soundville.co.uk/completePendingAction.php?identifier=" . $uuid . "'>https://soundville.co.uk/completePendingAction.php?identifier=" . $uuid . "</a><br /><br />"
                   .         "After you click this link, your new password will be:<br />&nbsp;&nbsp;&nbsp;&nbsp;<b>" . $passwordPlainText . "</b><br /><br />"
                   .         "If this was not you, you do not need to do anything, and your account will not be changed.<br /><br />"
                   .     "</h4><br /><br />"
                   . "</div>";

            include '../serverSide/emailScript.php';
            $email = new email();
            $email->send( $_POST['email'], "do-not-reply", "SoundVille: Reset Password", $emailBody );

            return array("status" => 200, "message" => "Ok, we've reset your password. Please check your emails for a link to complete the process.");
        }

        function passwordVerify( $correctPassword ){
            if( password_verify( $_POST['password'], $correctPassword ) ){
                return true;
            } else {
                return false;
            }
        }

        function login(){
            //This function needs DB access
            global $db;

            //start session
            session_start();

            //Check user email address
            $userDetail = $this->userExists( $_POST['email'] );

            //If a user exists in the database with that email address, check the password
            if( $userDetail !== false ){
                if( $this->passwordVerify( $userDetail['password'] ) ){
                    //Login success, set the session
                    $_SESSION = $userDetail;

                    //Get the number of items in that user's basket
                    $basketSizeQuery = $db->prepare("SELECT COUNT(*) AS 'size' FROM baskets WHERE user_id=:user_id");
                    $basketSizeQuery->bindParam( ":user_id", $_SESSION['id'] );
                    $basketSizeQuery->execute();

                    //Add the size of the basket to the session
                    $_SESSION['basketSize'] = $basketSizeQuery->fetch( PDO::FETCH_ASSOC )['size'];

                    //Grant access
                    return array( "status" => 200, "message" => "Successful login!", "session" => session_id() );
                } else {
                    return array( "status" => 403, "message" => "Your password is incorrect. <a href='forgotPassword.php' class='noMargin warning'><u>Forgotten Password?</u></a>" );
                }
            } else {
                return array( "status" => 403, "message" => "A user with that email does not exist." );
            }

            //This is here to catch anything that fails authorization
            return array( "status" => 403, "message" => "Incorrect username or password, please try again." );
        }

        function apiAuth(){
            global $api;
            global $db;

            if( !isset( $_SESSION['email'] ) || !isset( $_SESSION['password'] ) ){
                return false;
            } else {
                //Check that the user account exists
                //Create the query to identify user rows
                $loginQuery = $db->prepare("SELECT * FROM users WHERE email=:email");

                //bind the parameters to the query
                $loginQuery->bindParam( ":email", $_SESSION['email'] );

                //Execute the result
                $loginQuery->execute();

                //Get the results
                $results = $loginQuery->fetchAll( PDO::FETCH_ASSOC );

                //How many rows are there
                if( $loginQuery->rowCount() == 1 ){
                    if( $_SESSION['password'] != $results[0]['password'] ){
                        return false;
                    }

                    return true;
                } else {
                    return false;
                }
            }
        }

        function checkAuth(){
            global $db;

            if( !isset( $_SESSION['email'] ) || !isset( $_SESSION['password'] ) ){
                $this->denied( "index.php" );
            } else {
                //Check that the user account exists
                //Create the query to identify user rows
                $loginQuery = $db->prepare("SELECT * FROM users WHERE email=:email");

                //bind the parameters to the query
                $loginQuery->bindParam( ":email", $_SESSION['email'] );

                //Execute the result
                $loginQuery->execute();

                //Get the results
                $results = $loginQuery->fetchAll( PDO::FETCH_ASSOC );

                //How many rows are there
                if( $loginQuery->rowCount() == 1 ){
                    if( $_SESSION['password'] != $results[0]['password'] ){
                        $this->denied( "index.php" );
                    }
                } else {
                    //Deny access
                    $this->denied( "index.php" );
                }
            }
        }

        function denied( $referrer ){
            http_response_code( 403 );
            header('Location: ' . $referrer . '?code=400');
        }

        function granted( $referrer ){
            http_response_code( 200 );
            header('Location: ' . $referrer . '?code=200');
        }
    }
