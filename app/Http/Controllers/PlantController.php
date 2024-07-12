<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Services\PlantService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlantController extends Controller
{
    protected $plantService;

    public function __construct(PlantService $plantService)
    {
        $this->plantService = $plantService;
    }

    /**
     * @SWG\Get(
     *     path="/plants",
     *     summary="Get a list of plants",
     *     tags={"Plant"},
     *     @SWG\Response(response=200, description="Successful operation"),
     *     @SWG\Response(response=400, description="Invalid request")
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json(Plant::all());
    }

    /**
     * @SWG\Post(
     *     path="/plants",
     *     summary="Create a new plant",
     *     tags={"Plant"},
     *     @SWG\Response(response=200, description="Successful operation"),
     *     @SWG\Response(response=400, description="Invalid request")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $plant = Plant::create($request->all());
        return response()->json($plant, 201);
    }

    /**
     * @SWG\Get(
     *     path="/plants/{name}",
     *     summary="Get a plant",
     *     tags={"Plant"},
     *     @SWG\Response(response=200, description="Successful operation"),
     *     @SWG\Response(response=400, description="Invalid request")
     * )
     */
    public function show($name): JsonResponse
    {
        try {
            $plant = $this->plantService->getPlantByName($name);
            return $plant;

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * @SWG\Delete(
     *     path="/plants/{id}",
     *     summary="Delete a plant",
     *     tags={"Plant"},
     *     @SWG\Response(response=200, description="Successful operation"),
     *     @SWG\Response(response=400, description="Invalid request")
     * )
     */
    public function destroy($id): JsonResponse
    {
        $plant = Plant::find($id);

        if (!$plant) {
            return response()->json(['message' => 'Plant not found'], 404);
        }

        $plant->delete();
        return response()->json(['message' => 'Plant deleted successfully']);
    }

    /**
     * @SWG\Update(
     *     path="/plants/{id}",
     *     summary="Update a plant",
     *     tags={"Plant"},
     *     @SWG\Response(response=200, description="Successful operation"),
     *     @SWG\Response(response=400, description="Invalid request")
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $plant = Plant::find($id);

        if (!$plant) {
            return response()->json(['message' => 'Plant not found'], 404);
        }

        $request->validate([
            'common_name' => 'sometimes|string|max:255',
            // 'scientific_name' => 'sometimes|string|max:255',
            'image' => 'sometimes|string',
            'thumbnail' => 'sometimes|string',
            'watering_general_benchmark' => 'sometimes|json'
        ]);

        $plant->common_name = $request->get('common_name', $plant->common_name);
        // $plant->scientific_name = $request->get('scientific_name', $plant->scientific_name);
        $plant->image = $request->get('image', $plant->image);
        $plant->thumbnail = $request->get('thumbnail', $plant->thumbnail);
        $plant->watering_general_benchmark = $request->get('watering_general_benchmark', $plant->watering_general_benchmark);

        $plant->save();

        return response()->json(['message' => 'Plant deleted successfully']);
    }

    /**
     * @SWG\Update(
     *     path="/plants/refresh",
     *     summary="Refresh all datas of plants API",
     *     tags={"Plant"},
     *     @SWG\Response(response=200, description="Successful operation"),
     *     @SWG\Response(response=400, description="Invalid request")
     * )
     */
    public function refresh()
    {
        try {
            $this->plantService->fetchAndStoreAllPlants();
            return response()->json(['message' => 'Plants fetched and stored successfully'], 200);

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
