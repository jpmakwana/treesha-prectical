<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Forecast Application</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <style>
        #map {
            height: 400px;
            width: 100%;
        }

        #map_current {
            height: 400px;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container mt-5 p-5 shadow-lg p-3 mb-5 bg-white rounded">
        <h1 class="text-center">Weather Forecast Application</h1>
        <form class="form-inline justify-content-center" id="weatherform" method="POST">
            <?php $city = isset($_POST["city"]) ? $_POST["city"] : ""; ?>
            <div class="form-group my-3 mx-2">
                <label for="city" class="sr-only">City</label>
                <input type="text" name="city" id="city" class="form-control" placeholder="Enter city name" value="<?php echo $city; ?>" required>
            </div>

            <?php $tem_type = isset($_POST["temprature_type"])
                ? $_POST["temprature_type"]
                : ""; ?>
            <select name="temprature_type" class="form-control">
                <option value="metric" <?php if ($tem_type == "metric") {
                                            echo "selected";
                                        } ?>>Celsius</option>
                <option value="imperial" <?php if ($tem_type == "imperial") {
                                                echo "selected";
                                            } ?>>Fahrenheit</option>
            </select>

            <button type="submit" id="checkweather" class="btn btn-primary my-3 mx-2">Check Weather</button>
            <button type="submit" name="clear" class="btn btn-secondary my-3">Clear</button>
        </form>
        <div class="row" id="current_location">
            <div class="col-md-6 m-auto">
                <div id="weather-info" class="text-center mt-5"></div>
            </div>
            <div class="col-md-6">
                <div id="map_current" class="mt-5 d-none"></div>
            </div>
        </div>
        <?php
        define("APIKEY", "5e3933df49fe2a2257a2bf53a7c04422");
        define(
            "WEATHER_URL",
            "https://api.openweathermap.org/data/2.5/weather"
        );
        define(
            "FORECAST_URL",
            "https://api.openweathermap.org/data/2.5/forecast"
        );
        define("CACHE_DIR", __DIR__ . "/cache/");
        define("CACHE_EXPIRY", 600);

        function filePath($key)
        {
            return CACHE_DIR . md5($key) . ".cache";
        }

        function save_file_cache($key, $data)
        {
            if (!is_dir(CACHE_DIR)) {
                mkdir(CACHE_DIR, 0777, true);
            }
            file_put_contents(filePath($key), serialize($data));
        }

        function load_Cache_Data($key)
        {
            $file = filePath($key);
            if (
                file_exists($file) &&
                time() - filemtime($file) < CACHE_EXPIRY
            ) {
                return unserialize(file_get_contents($file));
            }
            return false;
        }

        function getWeatherData($url)
        {
            $cacheKey = $url;
            if ($cachedData = load_Cache_Data($cacheKey)) {
                return $cachedData;
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            // echo '<pre>'; print_r($response); exit;
            curl_close($ch);

            $data = json_decode($response, true);
            save_file_cache($cacheKey, $data);
            return $data;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST["clear"])) {

            $city = trim($_POST["city"]);
            $temprature_type = trim($_POST["temprature_type"]);
            $weatherUrl =
                WEATHER_URL .
                "?q={$city}&appid=" .
                APIKEY .
                "&units=" .
                $temprature_type;
            $forecastUrl =
                FORECAST_URL .
                "?q={$city}&appid=" .
                APIKEY .
                "&units=" .
                $temprature_type;

            $weatherData = getWeatherData($weatherUrl);
            $forecastData = getWeatherData($forecastUrl);
        ?>
            <div class="row">
                <div class="col-md-6 m-auto">
                    <div id="weatherResult" class="mt-5 text-center">
                        <?php if ($weatherData["cod"] == 200) { ?>
                            <h3>Weather in <?php echo $weatherData["name"]; ?></h3>
                            <img src='https://openweathermap.org/img/w/<?php echo $weatherData["weather"][0]["icon"]; ?>.png' alt='icon'>
                            <p>Current Temperature:<?php
                                                    echo $weatherData["main"]["temp"];
                                                    if ($temprature_type == "metric") {
                                                        echo "°C";
                                                    } else {
                                                        echo "°F";
                                                    }
                                                    ?></p>
                            <p>Weather Condition:<?php echo $weatherData["weather"][0]["description"]; ?></p>
                            <p>Humidity:<?php echo $weatherData["main"]["humidity"]; ?>%</p>
                            <p>Wind Speed:<?php echo $weatherData["wind"]["speed"]; ?> m/s</p>
                            <p>Country Code:<?php echo $weatherData["sys"]["country"]; ?></p>
                        <?php } else { ?>
                            <h4 class='text-capitalize text-danger'><?php echo $weatherData["message"]; ?></h4>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="map" class="mt-5 d-none"></div>
                    <script>
                        var lat = "<?php echo $forecastData["city"]["coord"]["lat"]; ?>";
                        var long = "<?php echo $forecastData["city"]["coord"]["lon"]; ?>";
                        var name = "<?php echo $weatherData["name"]; ?>";
                        var description = "<?php echo $weatherData["weather"][0]["description"]; ?>";
                        document.getElementById('map').classList.remove('d-none');
                        const map = L.map('map').setView([lat, long], 10);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                        }).addTo(map);
                        var S_Contant = `
                        <b>${name}</b><br>
                        ${description}<br>
                        <?php
                        echo $weatherData["main"]["temp"];
                        if ($temprature_type == "metric") {
                            echo "°C";
                        } else {
                            echo "°F";
                        }
                        ?>
                            `;
                        L.marker([lat, long]).addTo(map)
                            .bindPopup(S_Contant).openPopup();
                    </script>
                </div>
            </div>

            <div id="forecastResult" class="mt-5">
                <?php if ($forecastData["cod"] == "200") {

                    $dailyData = [];
                    $today = date("l, M j", time());
                    foreach ($forecastData["list"] as $item) {
                        // echo '<pre>';  print_r($item); exit;
                        $date = date("l, M j", $item["dt"]);
                        if (!isset($dailyData[$date]) && $date != $today) {
                            $dailyData[$date] = [
                                "temps" => [],
                                "description" => [],
                                "icons" => [],
                            ];
                        }
                        if ($date != $today) {
                            $dailyData[$date]["temps"][] =
                                $item["main"]["temp"];
                            $dailyData[$date]["description"][] =
                                $item["weather"][0]["description"];
                            $dailyData[$date]["icons"][] =
                                $item["weather"][0]["icon"];
                        }
                    }
                ?>

                    <h3 class="mb-5">5-Day Forecast</h3>
                    <div class="row">
                        <?php foreach ($dailyData as $date => $data) {
                            // echo '<pre>';  print_r($data); exit;
                            $averageTemp =
                                array_sum($data["temps"]) /
                                count($data["temps"]);
                            $condition = $data["description"][0];
                            $icon = $data["icons"][0];
                        ?>
                            <div class='col-md-4'>
                                <div class='p-3 my-3 bg-white rounded shadow-lg'>
                                    <h4><?php echo $date; ?></h4>
                                    <img src='https://openweathermap.org/img/w/<?php echo $icon; ?>.png' alt='icon'>
                                    <p>(Average)Temperature:<?php
                                                            echo number_format($averageTemp, 2);
                                                            if ($temprature_type == "metric") {
                                                                echo "°C";
                                                            } else {
                                                                echo "°F";
                                                            }
                                                            ?></p>
                                    <p>Weather Condition: <?php echo $condition; ?></p>
                                </div>
                            </div>
                        <?php
                        } ?>
                    </div>
                <?php
                } ?>
            </div>
        <?php
        } elseif (isset($_POST["clear"])) {

            array_map("unlink", glob(CACHE_DIR . "*.cache"));
            $_POST = [];
        ?>
            <script>
                window.location.href = "<?php echo $_SERVER["PHP_SELF"]; ?>";
            </script>
        <?php
        }
        ?>
    </div>
    <script>
        $(document).ready(function() {
            var city = "<?php echo isset($_POST["city"])
                            ? trim($_POST["city"])
                            : ""; ?>";
            if (city) {
                $('#current_location').remove();
            }
        });

        function currentLocationWeatherData(lat, long) {
            const apiKey = "<?php echo APIKEY; ?>";
            const apiUrl = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${long}&appid=${apiKey}&units=metric`;
            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    displayWeatherData(data);
                })
                .catch(error => console.log('Error fetching weather data:', error));
        }

        function displayWeatherData(weatherData) {
            var lat = weatherData.coord.lat;
            var long = weatherData.coord.lon;
            var name = weatherData.name;
            // console.log(weatherData);
            var description = weatherData.weather[0].description;
            var mapCurrentElement = document.getElementById('map_current');
            if (mapCurrentElement) {
                mapCurrentElement.classList.remove('d-none');
                const map = L.map('map_current').setView([lat, long], 10);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                var C_Contant = `
                <b>${name}</b><br>
                ${description}<br>
                ${weatherData.main.temp}°C
                    `;
                L.marker([lat, long]).addTo(map)
                    .bindPopup(C_Contant).openPopup();
            }

            const weatherInfo = document.getElementById('weather-info');
            if (weatherInfo) {
                weatherInfo.innerHTML = `
                <h3>Current Location</h3>
                <h3>Weather in ${weatherData.name}</h3>
                <img src='https://openweathermap.org/img/w/${weatherData.weather[0].icon}.png' alt='icon'>
                <p>Temperature: ${weatherData.main.temp} °C</p>
                <p>Weather: ${weatherData.weather[0].description}</p>
                <p>Humidity: ${weatherData.main.humidity}%</p>
                <p>Wind Speed: ${weatherData.wind.speed} m/s</p>
                <p>Country Code: ${weatherData.sys.country}</p>
            `;
            }
        }

        function getGeolocationAndWeather() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    const lat = position.coords.latitude;
                    const long = position.coords.longitude;
                    currentLocationWeatherData(lat, long);
                }, error => {
                    alert('Error getting geolocation:', error.message);
                });
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        }
        getGeolocationAndWeather();
    </script>
</body>

</html>