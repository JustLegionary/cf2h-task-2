// Listen for form submission
document.getElementById('weather-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent form from submitting

    // Get the value of the input field
    var cityName = document.getElementById('city').value.trim(); // Trim extra spaces

    // Check if city name is not empty or just whitespace
    if (cityName !== '') {
        // Make API request to weather.php
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'weather.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Display weather information
                    document.getElementById('weather-info').innerHTML = xhr.responseText;
                } else {
                    console.error('Error:', xhr.status);
                    // Display more specific error message
                    if (xhr.status === 404) {
                        document.getElementById('weather-info').innerHTML = 'Error: City not found';
                    } else {
                        document.getElementById('weather-info').innerHTML = 'Error: Failed to fetch weather data';
                    }
                }
            }
        };
        xhr.onerror = function() {
            console.error('Network Error');
            document.getElementById('weather-info').innerHTML = 'Error: Network Error. Please try again later.';
        };
        xhr.send('city=' + encodeURIComponent(cityName)); // Encode city name before sending
    } else {
        // Display error message if city name is empty or just whitespace
        document.getElementById('weather-info').innerHTML = 'Error: City name is required';
    }
});
