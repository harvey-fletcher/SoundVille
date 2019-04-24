<?php

    //Include the database connection
    include '../config/database.php';

    //Instantiate a new event class using the database
    $event = new eventStatistics( $db );

    class eventStatistics
    {
        protected $database;

        function __construct( $db ){
            //Check database connection is OK
            if( $db === false || $db === NULL ){
                die("Database connection failed.");
            }

            //Set the class variable so we can call it and I can remember what it is
            $this->database = $db;
        }

        function getEventProperties(){
            //Make a query to find out how many people are attending the event
            $stmt = $this->database->prepare("SELECT *
                                              FROM event_properties");

            //Run that query on the database
            $stmt->execute();

            //Get results from that query
            $eventData = $stmt->fetchAll( PDO::FETCH_ASSOC );

            //Return the event data
            if( sizeof( $eventData ) > 0 ){
                return $eventData[0];
            } else {
                //something went wrong
                return false;
            }
        }

        function addAttendeesToEvent( $count ){
            //Check that count is a valid integer
            if( gettype( $count ) !== "integer" )die("Can't add non-numeric value to count");

            //Build a query to update the count
            if(
                !$this->database->prepare( "UPDATE event_properties SET current_attendance=( current_attendance + :count )" )->execute(array(
                    ":count" => $count
                ))
            ){
                return false;
            } else {
                return true;
            }
        }

        function willSaleBeUnderLimit( $count ){
            //Check that count is a valid integer
            if( gettype( $count ) !== "integer" )die("Can't add non-numeric value to count");

            //Get the event data
            $eventData = $this->getEventProperties();

            if( ( $eventData["current_attendance"] + $count ) > $eventData["max_attendance"] ){
                //No, the sale will not be under the limit
                return false;
            } else {
                //Yes, the sale will be under the limit
                return true;
            }
        }
    }
