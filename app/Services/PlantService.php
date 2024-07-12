<?php

namespace App\Services;

use App\Interfaces\PlantServiceInterface;
use App\Models\Plant;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class PlantService implements PlantServiceInterface
{
    public function fetchAndStoreAllPlants(): void
    {
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $response = $client->request('GET', env('PERENUAL_URL') . 'species-list?key=' . env('PERENUAL_API_KEY'));

        $plants = json_decode($response->getBody()->getContents(), true);

        // $response = Http::get(env('PERENUAL_URL') . '/species-list?key=' . env('PERENUAL_API_KEY') . '&page=1&per_page=30');

        // if (!$response->successful()) {
        //     throw new Exception('Failed fetching plants', 500);
        // }

        // $plants = $response->json();

        foreach ($plants['data'] as $plant) {

            $plantID = $plant['id'];
            $client = new \GuzzleHttp\Client(['verify' => false]);
            $response = $client->request('GET', env('PERENUAL_URL') . 'species/details/' . $plantID . '?key=' . env('PERENUAL_API_KEY'));

            $plantDetail = json_decode($response->getBody()->getContents(), true);

            // $response = Http::get(env('PERENUAL_URL') . '/species/details/' . $plantID . '?key=' . env('PERENUAL_API_KEY'));

            // if (!$response->successful()) {
            //     throw new Exception('Failed fetching plant datails', 500);
            // }

            // $plantDetail = $response->json();

            $newPlant = [
                'common_name' => $plantDetail['common_name'],
                // 'scientific_name' => $plantDetail['scientific_name'][0],
                'image' => $plantDetail['default_image']['regular_url'],
                'thumbnail' => $plantDetail['default_image']['thumbnail'],
                'watering_general_benchmark' => $plantDetail['watering_general_benchmark']
            ];

            Plant::firstOrCreate($newPlant);
        }
    }

    public function getPlantByName(string $name)
    {
        $plant = Plant::where('common_name', $name)->first();

        if (!$plant) {
            return response()->json(['message' => 'Plant not found'], 404);
        }

        // return response()->json($plant);
        return $plant;
    }
}