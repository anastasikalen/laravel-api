<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
/**
 * @OA\Info(
 *     title="Your API Title",
 *     version="1.0.0",
 *     description="Description of your API"
 * )
 */
/**
 * @OA\Tag(name="Routes", description="Route management")
 */

/**
 * @OA\Schema(
 *     schema="Route",
 *     type="object",
 *     required={"id", "name", "description"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Route 1"),
 *     @OA\Property(property="description", type="string", example="Description of the route"),
 *     @OA\Property(property="start_location", type="string", example="Start Location"),
 *     @OA\Property(property="end_location", type="string", example="End Location"),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T12:00:00Z"),
 * )
 */

 class RouteController extends Controller
 {
     public function __construct()
     {
         $this->middleware('auth:sanctum');
     }
 
     /**
      * @OA\Post(
      *     path="/api/routes",
      *     tags={"Routes"},
      *     summary="Create a new route",
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\JsonContent(ref="/schemas/RouteRequest")
      *     ),
      *     @OA\Response(
      *         response=201,
      *         description="Route created successfully.",
      *         @OA\JsonContent(ref="/schemas/Route")
      *     ),
      *     @OA\Response(
      *         response=401,
      *         description="Unauthenticated."
      *     )
      * )
      */
     public function store(Request $request)
     {
         $request->validate([
             'name' => 'required|string',
             'description' => 'nullable|string',
             'start_location' => 'nullable|string',
             'end_location' => 'nullable|string',
         ]);
 
         $user = auth('sanctum')->user();
 
         if (!$user) {
             return response()->json(['message' => 'Unauthenticated.'], 401);
         }
 
         $route = Route::create([
             'user_id' => $user->id,
             'name' => $request->name,
             'description' => $request->description,
             'start_location' => $request->start_location,
             'end_location' => $request->end_location,
         ]);
         
         return response()->json($route, 201);
     }
 
     /**
      * @OA\Get(
      *     path="/api/routes",
      *     tags={"Routes"},
      *     summary="Get a list of routes",
      *     @OA\Response(
      *         response=200,
      *         description="List of routes.",
      *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Route"))
      *     )
      * )
      */
     public function index(Request $request)
     {
         $cacheKey = 'cache_routes';
 
         $sort = $request->query('sort');
 
         if ($sort && in_array($sort, ['created_at'])) {
             $cacheKey .= "_sorted_by_{$sort}";
         }
 
         $routes = Cache::remember($cacheKey, now()->addHours(3), function () use ($sort) {
             $query = Route::with('places');
 
             if ($sort && in_array($sort, ['created_at'])) {
                 $query->orderBy($sort, 'desc');
             }
 
             return $query->get();
         });
 
         return response()->json($routes, 200);
     }
 
     /**
      * @OA\Get(
      *     path="/api/routes/{id}",
      *     tags={"Routes"},
      *     summary="Get a specific route",
      *     @OA\Parameter(
      *         name="id",
      *         in="path",
      *         required=true,
      *         @OA\Schema(type="integer")
      *     ),
      *     @OA\Response(
      *         response=200,
      *         description="Route retrieved successfully.",
      *         @OA\JsonContent(ref="/schemas/Route")
      *     ),
      *     @OA\Response(
      *         response=404,
      *         description="Route not found."
      *     )
      * )
      */
     public function show(Route $route)
     {
         $route->load(['places']);
         return response()->json($route, 200);
     }
 
     /**
      * @OA\Put(
      *     path="/api/routes/{id}",
      *     tags={"Routes"},
      *     summary="Update a specific route",
      *     @OA\Parameter(
      *         name="id",
      *         in="path",
      *         required=true,
      *         @OA\Schema(type="integer")
      *     ),
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\JsonContent(ref="/schemas/RouteRequest")
      *     ),
      *     @OA\Response(
      *         response=200,
      *         description="Route updated successfully.",
      *         @OA\JsonContent(ref="/schemas/Route")
      *     ),
      *     @OA\Response(
      *         response=403,
      *         description="You are not authorized to update this route."
      *     )
      * )
      */
     public function update(Request $request, Route $route)
     {
         $user = auth('sanctum')->user();
         if ($route->user_id !== $user->id) {
             return response()->json(['message' => 'You are not authorized to update this route.'], 403);
         }
 
         $request->validate([
             'name' => 'required|string',
             'description' => 'nullable|string',
             'start_location' => 'nullable|string',
             'end_location' => 'nullable|string',
         ]);
 
         $route->update([
             'name' => $request->name,
             'description' => $request->description,
             'start_location' => $request->start_location,
             'end_location' => $request->end_location,
         ]);
 
         return response()->json($route, 200);
     }
 
     /**
      * @OA\Delete(
      *     path="/api/routes/{id}",
      *     tags={"Routes"},
      *     summary="Delete a specific route",
      *     @OA\Parameter(
      *         name="id",
      *         in="path",
      *         required=true,
      *         @OA\Schema(type="integer")
      *     ),
      *     @OA\Response(
      *         response=200,
      *         description="Route deleted successfully."
      *     ),
      *     @OA\Response(
      *         response=403,
      *         description="You are not authorized to delete this route."
      *     )
      * )
      */
     public function destroy(Route $route)
     {
         $user = auth('sanctum')->user();
         if ($route->user_id !== $user->id) {
             return response()->json(['message' => 'You are not authorized to delete this route.'], 403);
         }
 
         $route->delete();
 
         return response()->json(['message' => 'Route deleted successfully.'], 200);
     }
 }