<?php

namespace App\Http\Controllers;

use App\Services\PlantService;
use App\Services\WeatherService;
use App\Services\WateringService;
use Exception;
use Illuminate\Http\Request;

class UserPlantController extends Controller
{
    protected $weatherService;
    protected $plantService;
    protected $wateringService;

    public function __construct(WeatherService $weatherService, PlantService $plantService, WateringService $wateringService)
    {
        $this->weatherService = $weatherService;
        $this->plantService = $plantService;
        $this->wateringService = $wateringService;
    }

    /**
     * @SWG\Post(
     *     path="/user/plants",
     *     summary="Add a plant to an user",
     *     tags={"UserPlant"},
     *     @SWG\Response(response=200, description="Successful operation"),
     *     @SWG\Response(response=400, description="Invalid request")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|exists:plants,common_name',
            'city' => 'required|string'
        ]);
        $name = $request->input('name');
        $city = $request->input('city');

        try {
            $plant = $this->plantService->getPlantByName($name);
            
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
        
        try {
            $weather = $this->weatherService->getWeatherByCity($city);

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        try {
            $wateringNotification = $this->wateringService->calculateNextWatering($plant, $weather);

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
        

        $user = $request->user();
        $user->plants()->attach($plant->id);

        return response()->json(['message' => 'Plant added to user'], 200);
    }

    /**
     * @SWG\Post(
     *     path="/user/plants/{id}",
     *     summary="Delete a plant of an user",
     *     tags={"UserPlant"},
     *     @SWG\Response(response=200, description="Successful operation"),
     *     @SWG\Response(response=400, description="Invalid request")
     * )
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $user->plants()->detach($id);

        return response()->json(['message' => 'Plant removed from user']);
    }
}
