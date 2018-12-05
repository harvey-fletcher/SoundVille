<?php

    //Start a new math object
    $math = new mathController();

    class mathController{
        function calcProcessingFee( $orderTotal ){
            //Work out the fee
            $processingFee = ( $orderTotal * 0.04 ) + 0.25;

            //Return the processing fee
            return $processingFee;
        }
    }
