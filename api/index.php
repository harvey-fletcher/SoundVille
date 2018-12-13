<?php

    //API always returns JSON
    header('Content-Type: application/json');

    //Access denied
    http_response_code( 403 );
    echo json_encode( array( "status" => 403, "msg" => "You do not have permission to access this." ) );
