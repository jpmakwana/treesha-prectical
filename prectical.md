Task: Weather Forecast Application using PHP

Description:
Create a Weather Forecast application that allows users to search for weather forecasts of different cities. The application should fetch weather data from the OpenWeatherMap API and display the weather information to the user.

API: OpenWeatherMap API

API Documentation: You can find the documentation for the OpenWeatherMap API at the following link: https://openweathermap.org/api

API Key: To access the OpenWeatherMap API, you'll need to sign up for a free API key. Follow the instructions on the OpenWeatherMap website to get your API key.

Weather Data to Display:

1. Current weather: City name, current temperature, weather condition (e.g., sunny, cloudy, rainy), humidity, wind speed, and any other relevant details.
2. 5-day weather forecast: Display the temperature and weather conditions for each day.

API Requests:
To fetch weather data for a specific city, make an API request to the following endpoint (using the city name and your API key):

```
GET https://api.openweathermap.org/data/2.5/weather?q={city name}&appid={your_api_key}
```

For the 5-day weather forecast, you can use the following endpoint:

```
GET https://api.openweathermap.org/data/2.5/forecast?q={city name}&appid={your_api_key}
```

Units:
By default, the API returns temperature in Kelvin. You can specify the units in the API request to get temperature in Celsius or Fahrenheit. For example, you can add `&units=metric` to the API request URL to get temperature in Celsius.

Requirements:

1. The main page should have a search input field where users can enter the name of the city they want to check the weather for. - Done
2. When the user submits the city name, the application should fetch the weather data for that city from the OpenWeatherMap API.- Done
3. Display the weather forecast information in a user-friendly way. Show the city name, current temperature, weather condition, humidity, wind speed, and any other relevant details. - Done
4. Provide a 5-day weather forecast for the selected city, showing the temperature and weather condition for each day.- Done
5. Use appropriate icons or images to represent different weather conditions (e.g., sun icon for sunny weather, cloud icon for cloudy weather, etc.).- Done
6. Implement error handling for cases when the API request fails or when the entered city name is not found.- Done
7. You have to implement api request's response cache to reduce sending api request to openweathermap
8. Add the user's geolocation to automatically display the weather for their current location.
9. Allow users to switch between Celsius and Fahrenheit units for temperature display.
10. Implement a feature to display weather conditions on an interactive map.

Submission Guidelines:

- Provide the complete source code of the project along with any necessary instructions to run the application locally.
- Mention the OpenWeatherMap API or any other external APIs used in the project.
- Make sure the application is functional.

If you have any further questions or need additional details, feel free to ask. Happy coding!
