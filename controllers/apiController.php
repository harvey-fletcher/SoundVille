<?php

    class api{
        function out( $status, $data ){
            //Build the output data array
            $outputData = array(
                    "status" => $status,
                    "data"   => $data
                );

            //json encode the array
            $outputData = json_encode( $outputData );

            //Output the data
            echo $outputData;

            //Return nothing and exit any function
            die();
        }

        function listAvailableControllers(){
            //Get all the files in the controllers directory, but not . and ..
            $Controllers = preg_grep('/^([^.])/', scandir( '../controllers/' ) );;

            //Replace all occurences of the word "Controller"
            $Controllers = str_replace('Controller.php', '', $Controllers);

            //Return the sanitised list of controllers
            return $Controllers;
        }
    }
