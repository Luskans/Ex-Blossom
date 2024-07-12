<?php

namespace App\Services;

use App\Interfaces\WeatherServiceInterface;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherService implements WeatherServiceInterface
{
    public function getWeatherByCity(string $city): array
    {
        $cacheKey = 'weather_' . $city;
        $cachedData = Cache::get($cacheKey);

        if ($cachedData) {
            return $cachedData;
        }

        $response = Http::get(env('WEATHER_GEO_URL') . '?q=' . $city . '&limit=1&appid=' . env('WEATHER_API_KEY'));

        if (!$response->successful()) {
            throw new Exception('Failed fetching geolocalisation');
        }

        $geo = $response->json();
        $lat = $geo['lat'];
        $lon = $geo['lon'];

        $response = Http::get(env('WEATHER_URL') . '?lat=' . $lat . '&lon=' . $lon . '&appid=' . env('WEATHER_URL'));

        if (!$response->successful()) {
            throw new Exception('Failed fetching weather data');
        }

        $weatherData = $response->json();
        $newWeatherData = [
            'main' => $weatherData['weather']['main'],
            'description' => $weatherData['weather']['description'],
            'temp' => $weatherData['main']['temp'],
            'pressure' => $weatherData['main']['pressure'],
            'humidity' => $weatherData['main']['humidity']
        ];

        Cache::put($cacheKey, $newWeatherData, 120 * 60); // Cache for 2 hours

        return $newWeatherData;
    }
}