/*
    Student name: Ina Youn
    Student ID: 21140457
    Description: This folder has been created to retrieve the data from admin.php and display this in a table format.
    It fetches all the data and requirements from admin.php. Creation of tables, assigned buttons have also been made
    where when user clicks on the assign button, this should update the status to assigned.
*/

//retrieve element by using eventlistener when user clicks on button
document.addEventListener('DOMContentLoaded', function() {
    const searchButton = document.getElementById('searchButton');
    const resultDiv = document.getElementById('result');

    searchButton.addEventListener('click', function() {
        const searchInput = document.getElementById('bsearch').value.trim();

        // Validate the format of the reference number
        if (searchInput !== '' && !/^BRN\d{5}$/.test(searchInput)) {
            resultDiv.innerHTML = '<p style="color: red;">Invalid reference number format. Please use the format BRN00001.</p>';
            return;
        }

        // Send the search request using Fetch API
        fetch('admin.php', 
        {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ bsearch: searchInput })
        })
        //if response has an error 
        .then(response => 
            {
            if (!response.ok)
            {
                //error message
                throw new Error('Error');
            }
            return response.json();
        })
        .then(data => 
            {
                //if data runs into an error
            if (data.error) 
            {
                //error color is red
                resultDiv.innerHTML = `<p style="color: red;">${data.error}</p>`;
            } 
            else if (data.bookings && data.bookings.length > 0) 
            {
                displayBookings(data.bookings);
            } 
            else {
                //if bookings have not been found in database
                resultDiv.innerHTML = '<p>No bookings have been found</p>';
            }
        })
        //error message
        .catch(error => 
            {
            resultDiv.innerHTML = `<p style="color: red;">Error: ${error.message}</p>`;
        });
    });

    //display booking details in table format
    function displayBookings(bookings) 
    {
        //display table boarder name
        let resultHtml = `
            <table border="1"> 
                <thead>
                    <tr>
                        <th>Booking Reference Number</th>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Pickup Suburb</th>
                        <th>Destination Suburb</th>
                        <th>Pickup Date and Time</th>
                        <th>Status</th>
                        <th>Assign</th>
                    </tr>     
                </thead>
                <tbody>`;
                
        bookings.forEach(booking => 
            {
                //putting retrieved data into a table format
            resultHtml += `
                <tr>
                    <td>${booking.reference}</td>
                    <td>${booking.customer_name}</td>
                    <td>${booking.phone}</td>
                    <td>${booking.suburb}</td>
                    <td>${booking.destination_suburb}</td>
                    <td>${booking.pickup_date} ${booking.pickup_time}</td>
                    <td>${booking.status}</td>
                    <td><button onclick="assignBooking('${booking.reference}')">Assign</button></td>
                </tr>`;
        });

        resultHtml += `</tbody></table>`;
        resultDiv.innerHTML = resultHtml;
    }
});

//function for assigned booking
function assignBooking(reference) 
{
    fetch('assign.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'assign', booking_ref: reference })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) 
        {
            //creation of assigned button
            const button = document.querySelector(`button[onclick="assignBooking('${reference}')"]`);
            //column 6 of table
            button.closest('tr').cells[6].innerText = 'Assigned';
            button.disabled = true;
        } 
        else 
        {
            alert(data.error);
        }
    })
    .catch(error => 
        {
            //display error 
        alert(`Error: ${error.message}`);
    });
}
