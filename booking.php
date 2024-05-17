<?php
        //connecting to settings to retrieve data 
        require_once ("../../files/settings.php");
        $conn = new mysqli($host, $user, $pswd, $dbnm);

        //if connection failed 
        if($conn->connection_error)
        {
            die("Connection failed");
        }

        //generate a booking reference number
        function generatenumber($conn)
        {
            $sql = "SELECT COUNT(*) AS booking_ref FROM bookings";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $count = $row['booking_ref'] + 1;
            return 'BRN' . str_pad($count, 5, '0', STR_PAD_LEFT);
        }

        //Validate and input data
        


?>