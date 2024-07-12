<?php

namespace App\Services;

use App\Models\Plant;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WateringService
{
    public function calculateNextWatering(Plant $plant, array $weather)
    {
        $sky = $weather['weather']['main'];
        $humidity = $weather['main']['humidity'];
        $benchmarkValue = $plant->water_general_benchmark->value;
        $benchmarkUnit = $plant->water_general_benchmark->unit;
        $seconds = null;

        if ($benchmarkUnit == "weeks") {
            $seconds = 168 * 60 * 60;
        } else {
            $seconds = 24 * 60 * 60;
        }

        if (preg_match('/^\d+$/', $benchmarkValue, $matches)) {
            $number = (int)$matches[0];
            $seconds = $number * $seconds;
        } elseif (preg_match('/^\d+-\d+$/', $benchmarkValue, $matches)) {
            $number = (int)$matches[1];
            $seconds = $number * $seconds;
        } else {
            $seconds = 1 * $seconds;
        }

        if ($sky == "Clear") {
            $seconds = $seconds * 0.8;
        } elseif ($sky == "Cloudy") {
            $seconds = $seconds * 1.2;
        } else {
            $seconds = $seconds * 1;
        }

        if ($humidity > 80) {
            $seconds = $seconds * 1.2;
        } elseif ($humidity < 60) {
            $seconds = $seconds * 0.8;
        } else {
            $seconds = $seconds * 1;
        }

        $nextWateringTimestamp = now()->addSeconds($seconds);
        return $nextWateringTimestamp;
    }
}