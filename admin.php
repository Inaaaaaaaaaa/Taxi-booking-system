<!--
    Student name: Ina Youn
    Student ID: 21140457
    Description: This folder has been created to display results that are currently stored in the database. By using SQL query's and JSON, 
    the script connects to the database where it searches through it and retrieves data that is needed.
-->

<?php
        //set content-type as json
        header('Content-Type: application/json');
        //connecting to settings to retrieve data 
        require_once ("../../files/settings.php");
        $conn = new mysqli($host, $user, $pswd, $dbnm);

        //if connection failed 
        if($conn->connection_error)
        {
            echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
            exit();
        }

        $results = [];
        $bsearch = isset($_POST['bsearch']) ? $_POST['bsearch'] : null;
        //status button
        $action = isset($_POST['action']) ? $_POST['action'] : '';

        //if search input is not empty, search by reference number 
        if (!empty($action) && $action == 'assign') 
        {
            $booking_ref = isset($_POST['booking_ref']) ? $_POST['booking_ref'] : '';
            
            if (!empty($booking_ref)) 
            {
                //update status to assigned once button has been pressed
                $stmt = $conn->prepare("UPDATE bookings SET status = 'Assigned' WHERE booking_ref = ?");
                $stmt->bind_param("s", $booking_ref);
                $stmt->execute();
                if ($stmt->affected_rows > 0) 
                {
                    echo json_encode(['success' => true]);
                } 
                //error message
                else 
                {
                    echo json_encode(['success' => false, 'error' => 'No booking updated']);
                }
                exit;  
            }
        } 
        else if (!empty($bsearch)) 
        {
            //retrieve data from the database
            $stmt = $conn->prepare("SELECT booking_ref AS reference, customer_name, phone, suburb, destination_suburb, pickup_date, pickup_time FROM bookings WHERE booking_ref = ?");
            $stmt->bind_param("s", $bsearch);
            $stmt->execute();
            //fetch result
            $result = $stmt->get_result();
            //display results in rows
            while ($row = $result->fetch_assoc()) 
            {
                $results[] = $row;
            }
        } 
        else 
        {
            //retrieve data from the database
            $result = $conn->query("SELECT booking_ref AS reference, customer_name, phone, suburb, destination_suburb, pickup_date, pickup_time FROM bookings WHERE TIMESTAMPDIFF(HOUR, NOW(), CONCAT(pickup_date, ' ', pickup_time)) <= 2");
            while ($row = $result->fetch_assoc()) 
            {
                $results[] = $row;
            }
        }

        //fetch results
        if($result->num_rows > 0)
        {
            while($row = $result->fetch_assoc())
            {
                //display into rows
                $results[] = $row;
            }
        }

        if(count($results)> 0)
        {
            echo json_encode(['bookings' => $results]);
        }
        else
        {
            echo json_encode(['success' => false, 'error' => 'No bookings have been found']);
        }
?>