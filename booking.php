<!--
    Student name: Ina Youn
    Student ID: 21140457
    Description: This folder has been created to store all the data from booking.html using SQL (phpmyadmin). The commands 
    are created where the main goal of this folder is to simply fetch all the data and create a unique booking reference 
    number, pickup time and date by using JSON. Error checking has also been created to let user know whether there was an 
    issue retrieving data from the database.
-->

<?php
        //connecting to settings to retrieve data 
        require_once ("../../files/settings.php");
        $conn = new mysqli($host, $user, $pswd, $dbnm);

        //if connection failed 
        if($conn->connection_error)
        {
            die(json_encode(['success' => false, 'error' => 'Connection failed: '.$conn->connect_error]));
        }

        //generate a booking reference number
        function generatenumber($conn)
        {
            $sql = "SELECT COUNT(*) AS booking_ref FROM bookings";
            $result = $conn->query($sql);

            if(!$result)
            {
                echo json_encode(['success' => false, 'error' => 'Failed to generate booking number. Please try again']);
                exit();
            }
            $row = $result->fetch_assoc();
            $count = $row['booking_ref'] + 1;
            return 'BRN' . str_pad($count, 5, '0', STR_PAD_LEFT);
        }

        //Validate and input data
        $cname = $conn->real_escape_string($_POST['cname']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $unumber = isset($_POST['unumber']) ? $conn->real_escape_string($_POST['unumber']) : NULL;
        $snumber = $conn->real_escape_string($_POST['snumber']);
        $stname = $conn->real_escape_string($_POST['stname']);
        $sbname = isset($_POST['sbname']) ? $conn->real_escape_string($_POST['sbname']) : NULL;
        $dsbname = isset($_POST['dsbname']) ? $conn->real_escape_string($_POST['dsbname']) : NULL;
        $pickup_date = $conn->real_escape_string($_POST['date']);
        $pickup_time = $conn->real_escape_string($_POST['time']);
        $status = 'unassigned';
        $booking_ref = generateNumber($conn);

        // Insert data into the database
        $sql = "INSERT INTO bookings (booking_ref, customer_name, phone, unit_number, street_number, street_name, suburb, destination_suburb, pickup_date, pickup_time, status)
        VALUES ('$booking_ref', '$cname', '$phone', '$unumber', '$snumber', '$stname', '$sbname', '$dsbname', '$pickup_date', '$pickup_time', '$status')";

        if ($conn->query($sql) === TRUE) 
        {
            //display data of bookingref, pickuptime and pickupdate
            echo json_encode([
                'success' => true,
                'bookingRef' => $booking_ref,
                'pickuptime' => $pickup_time,
                'pickupdate' => $pickup_date
            ]);
        } 
        else 
        {
            echo json_encode (['success' => false, 'error' => 'Error: ' . $sql . '<br>' . $conn->error]);
        }

        // Close the connection
        $conn->close();
    

?>