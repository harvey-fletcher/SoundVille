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
                    $this->granted( $_GET['referrer'] );
                }
            }

            //This is here to catch anything that fails authorization
            $this->denied( $_GET['referrer'] );
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
