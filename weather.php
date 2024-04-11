<?php
// Check if city name is provided in the POST request and it's not empty
if (isset($_POST['city']) && !empty($_POST['city'])) {
    // Load configuration file with API key
    $config = parse_ini_file('config.ini', true);

    // Check if the configuration for OpenWeatherMap exists and API key is provided
    if (!isset($config['openweathermap']['api_key']) || empty($config['openweathermap']['api_key'])) {
        echo 'Error: OpenWeatherMap API key is missing in configuration.';
        exit;
    }

    // Get the city name from the POST request and trim any extra spaces
    $cityName = trim($_POST['city']);

    // Validate and sanitize city name
    $cityName = filter_var($cityName, FILTER_SANITIZE_STRING);

    // API URL with URL encoding for city name and API key from configuration
    $apiUrl = 'http://api.openweathermap.org/data/2.5/weather?q=' . urlencode($cityName) . '&appid=' . $config['openweathermap']['api_key'];

    // Making API Call using cURL for better error handling and security
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $apiUrl,
        CURLOPT_TIMEOUT => 10, 
        CURLOPT_SSL_VERIFYPEER => true, // Enable SSL verification for security
    ]);
    $response = curl_exec($curl);

    // Check for errors in the cURL request
    if ($response === false || empty($response)) {
        echo 'Error: Failed to fetch weather data.';
    } else {
        // Decoding JSON response
        $weatherData = json_decode($response);

        // Check if API request was successful
        if ($weatherData && isset($weatherData->cod) && $weatherData->cod === 200) {
            echo "<h2>Weather in $cityName:</h2>";
            echo "<p>Description: {$weatherData->weather[0]->description}</p>";
            echo "<p>Temperature: " . kelvinToCelsius($weatherData->main->temp) . " Celsius</p>";

            // Log successful API request
            logAPICall($cityName, $apiUrl);
        } else {
            // Display error message
            echo isset($weatherData->message) ? 'Error: ' . $weatherData->message : 'Error: Failed to fetch weather data.';
        }
    }

    curl_close($curl);
} else {
    // If city name is not provided in the POST request or it's empty
    echo 'Error: City name is missing or empty.';
}

// Function to convert temperature from Kelvin to Celsius
function kelvinToCelsius($kelvin)
{
    return round($kelvin - 273.15, 2); // Convert Kelvin to Celsius and round to 2 decimal places
}

// Function to log API call
function logAPICall($cityName, $apiUrl)
{
    // Construct log message
    $logMessage = '[' . date('Y-m-d H:i:s') . '] API call made for city: ' . $cityName . "\n";

    // Log the message to a file
    $logFile = 'api.log';
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

?>
