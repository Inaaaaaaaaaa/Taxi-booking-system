<?php
        //connecting to settings to retrieve data 
        require_once ("../../files/settings.php");
        $conn = new mysqli($host, $user, $pswd, $dbnm);

        //if connection failed 
        if($conn->connection_error)
        {
            die("Connection failed");
        }

        //if search input is not empty, search by reference number 
        if($bsearch)
        {
            $sql = "SELECT booking_ref AS reference, pickup_date, pickup_time FROM bookings WHERE booking_ref = '$bsearch'";
        }

        //else ist of bookings with a pickup time within 2 hours from the current time is returned by the server
        else 
        {
            $sql = "SELECT booking_ref AS reference, pickup_date, pickup_time FROM bookings WHERE TIMESTAMPDIFF(HOUR, NOW(), CONCAT(pickup_date, ' ', pickup_time)) <= 2"; 
        }

        $result = $conn->query($sql);

        if($result->num_rows > 0)
        {
            while($row = $result->fetch_assoc())
            {
                $results[] = $row;
            }
        }

        if(count($results) > 0)
        {
            echo json_encode(['bookings' => $results]);
        }
        else{
            echo json_encode(['error' => 'No bookings have been found']);
        }
        
        //close connection
        $conn->close();


?>