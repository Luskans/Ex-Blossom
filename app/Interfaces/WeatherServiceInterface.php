<?php

namespace App\Interfaces;

interface WeatherServiceInterface
{
    public function getWeatherByCity(string $city): array;
}
