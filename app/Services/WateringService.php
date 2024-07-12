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
        //
    }
}