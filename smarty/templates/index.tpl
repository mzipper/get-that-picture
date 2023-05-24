<html>
<head>
    <title>Get that picture</title>
    <link rel="stylesheet" href="includes/jquery-ui-1.13.2/jquery-ui.min.css">
    <script src="includes/js/jquery-3.6.3.min.js"></script>
    <script src="includes/jquery-ui-1.13.2/jquery-ui.min.js"></script>
</head>

<h1>Get that picture</h1>
<br/>

{* <form id="address-form"> *}
<label for="origin-address">Origin Address (street address, city, state/province, postal code):</label>
<input type="text" id="origin-address" name="origin_address" placeholder="e.g. 1600 Pennsylvania Ave NW, Washington, DC 20500" size="60">
<br>
<label for="destination-address">Destination Address (street address, city, state/province, postal code):</label>
<input type="text" id="destination-address" name="destination_address" placeholder="e.g. 1 Times Square, New York, NY 10036" size="60">
<br>
<label for="datepicker">Select a Date for Sunset:</label>
<input type="text" id="datepicker" placeholder="Date for Sunset">
<br>
<button id="submit-addresses">Submit Addresses</button>
{* </form> *}

<div id="ajaxInput"> </div>


{literal}
    <script>
        $(document).ready(function() {
            $("#datepicker").datepicker({
                minDate: 0,
                constrainInput: true,
            });
            $("#datepicker").datepicker("setDate", "0"); //start the date as the current date
        });
    </script>
{/literal}



{literal}
    <script>
        $(document).ready(function() {
            $('#submit-addresses').click(function(event) {
                // Prevent the form from submitting normally
                //event.preventDefault();

                // Get the 2 addresses
                var originAddress = $('#origin-address').val();
                var destinationAddress = $('#destination-address').val();

                // Get the selected date from the Datepicker
                var date = $("#datepicker").datepicker("getDate");

                // Format the date for use in the sunset API (in YYYY-MM-DD format)
                var arrivalDate = $.datepicker.formatDate("yy-mm-dd", date);

                // Send an AJAX request to a PHP file, passing the origin and destination addresses and arrival date as data
                $.get('apicalls.php', {origin_address: originAddress, destination_address: destinationAddress, arrival_date: arrivalDate}, function(data) {
                    alert(data);
                    // Parse the JSON response into a JavaScript object
                    var response = JSON.parse(data);


                    var html = "<p>Origin: " + response.origin_address + "</p>" +
                        "<p> Sunset time at destination of: " + response.destination_address + " is " +  response.sunset_local_formatted + " on date of " + response.arrival_date + "</p>"  +
                        "<p> Leave on " + response.departureDate_formatted+ " at " + response.departureTime_formatted + " with a travel time of " + response.travel_time_response_minutes + " minutes to arrive at sunset</p>";

                    // Add Save button and div
                    html += "<button id='save-button'>Save</button>";

                    html += "<div id=\"respInput\"> </div>";

                    // Display the response in the "ajaxInput" div element
                    document.querySelector("#ajaxInput").innerHTML = html;

                    // Handle click event of the Save button
                    $('#save-button').click(function() {
                        var saveButton = $(this);

                        // Disable the button
                        saveButton.prop('disabled', true);

                        saveButton.text('Saving');

                        // Send data to saveData.php
                        $.post('saveData.php', {data: data}, function(resp) {
                            saveButton.text('Saved');
                            document.querySelector("#respInput").innerHTML = "<p>"+ resp +"</p>";
                        });
                    });
                });
            });
        });
    </script>
{/literal}
