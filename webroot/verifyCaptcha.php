<?php

    header('Content-Type: application/json');

    include '../config/dependencies.php';
    $dependencies = new Dependencies();

    if( $dependencies->confirmCaptcha( $_POST['response'] ) ){
        http_response_code( 200 );
        $response = array( "status" => 200, "message" => "Confirmation success" );
    } else {
        http_response_code( 403 );
        $response = array( "status" => 403, "message" => "Confirmation failure" );
    }

    echo json_encode( $response );
