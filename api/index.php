<?php

    //API always returns JSON
    header('Content-Type: application/json');

    //Need to allow cross origins
    header("Access-Control-Allow-Origin: *");

    //There is a set of functions for use by the API
    include '../controllers/apiController.php';
    $api = new api();

    //Break up the request URL
    $request_url = array_values( array_filter( explode( '/', $_SERVER['REQUEST_URI'] ) ) );
    $requestController = $request_url[0];
    $requestFunction   = $request_url[1];

    //Connect to the database
    include '../config/database.php';

    //The only endpoint that doesn't require auth is login
    //Need username and password to access the API
    if( !isset( $_POST['session'] ) ){
        if( $requestController == 'access' && $requestFunction == 'login' ){
            include '../controllers/accessController.php';
	    $access = new accessController();

            $data = $access->login();
            $data['referrer'] = $request_url[2];

            $api->out( 200, $data );
        } else if( $requestController == 'access' && $requestFunction == 'reset_password' ) {
            include '../controllers/accessController.php';
            $access = new accessController();
            $api->out( 200, $access->resetPassword( $_POST['email'] ));
        } else {
            $api->out( 400, "You need to specify the session token " . $requestController . " " . $requestFunction );
        } 
    }

    //Start session
    session_id( $_POST['session'] );
    session_start();

    //Load the access controller
    include '../controllers/accessController.php';
    $access = new accessController();

    //Ensure that the user is signed in
    if( !$access->apiAuth() ){
        $api->out( 403, array( "message" => "Your session is not active" ) );
    }

    /**
        If the user has made it this far, then they have correctly authenticated.
        Load the controller from here.
    **/

    //Now that the controller and function are in seperate variables, delete them from the request URL
    unset( $request_url[0] );
    unset( $request_url[1] );

    //Re-label keys
    $request_url = array_values( $request_url );

    //Check what functions are available on the api
    $availableControllers = $api->listAvailableControllers();

    //Is the requested controller available
    if( !in_array( $requestController, $availableControllers ) ){
        $api->out( 400, "That controller does not exist." );
    }

    //Load the requested controller
    include '../controllers/' . $requestController . 'Controller.php';
    $controller = $requestController . 'Controller';
    $controller = new $controller();

    //Get the list of functions in the controller
    $functionList = get_class_methods( $controller );

    //Does the requested function exist
    if( !in_array( $requestFunction, $functionList ) ){
        $api->out( 400, "That function does not exist in the controller");
    }

    /**
        Now that the user has made it this far, we know that they are
        trying to access a controller that exists, and a function in
        that controller that also exists. So, it's safe to run it.
    **/
    $api->out( 200, $controller->$requestFunction() );
?>
