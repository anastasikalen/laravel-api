<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
/**
 * @OA\Info(
 *     title="Your API Title",
 *     version="1.0.0",
 *     description="Description of your API",
 *     @OA\Contact(
 *         name="Your Name",
 *         email="your-email@example.com"
 *     ),
 * )
 */
/**
 * @OA\Tag(name="Places", description="Place management")
 */
/**
 * @OA\Schema(
 *     schema="PlaceRequest",
 *     type="object",
 *     required={"name", "description"},
 *     @OA\Property(property="name", type="string", example="Place 1"),
 *     @OA\Property(property="description", type="string", example="Description of the place"),
 *     @OA\Property(property="latitude", type="number", format="float", example="12.345678"),
 *     @OA\Property(property="longitude", type="number", format="float", example="98.765432"),
 * )
 */
/**
 * @OA\Schema(
 *     schema="WeatherResponse",
 *     type="object",
 *     @OA\Property(property="temperature", type="number", example=23.5),
 *     @OA\Property(property="humidity", type="number", example=60),
 *     @OA\Property(property="weather", type="string", example="Clear sky"),
 *     @OA\Property(property="wind_speed", type="number", example=5.2),
 *     @OA\Property(property="city_name", type="string", example="New York")
 * )
 */
/**
 * @OA\Schema(
 *     schema="HotelResponse",
 *     type="array",
 *     @OA\Items(type="object",
 *         @OA\Property(property="name", type="string", example="Hotel Sunshine"),
 *         @OA\Property(property="address", type="string", example="123 Main St, Springfield"),
 *         @OA\Property(property="price", type="string", example="100 USD"),
 *         @OA\Property(property="rating", type="number", example=4.5),
 *         @OA\Property(property="reviewWord", type="string", example="Excellent"),
 *         @OA\Property(property="checkin", type="string", example="14:00"),
 *         @OA\Property(property="checkout", type="string", example="12:00"),
 *         @OA\Property(property="photo", type="string", example="https://example.com/photo.jpg"),
 *         @OA\Property(property="coordinates", type="object",
 *             @OA\Property(property="latitude", type="number", example=40.7128),
 *             @OA\Property(property="longitude", type="number", example=-74.0060)
 *         ),
 *         @OA\Property(property="badges", type="array", @OA\Items(type="string")),
 *         @OA\Property(property="class", type="string", example="5 star")
 *     )
 * )
 */

class PlaceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('getWeather');
    }

    /**
     * @OA\Post(
     *     path="/api/routes/{routeId}/places",
     *     tags={"Places"},
     *     summary="Create a new place for a specific route",
     *     @OA\Parameter(
     *         name="routeId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="/schemas/PlaceRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Place created successfully.",
     *         @OA\JsonContent(ref="/schemas/Place")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated."
     *     )
     * )
     */

    public function store(Request $request, $routeId)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'latitude'=> 'required|numeric',
            'longitude'=> 'required|numeric',
        ]);

        $place = Place::create(array_merge($request->all(), ['route_id' => $routeId]));
        return response()->json($place, 201);
    }

      /**
     * @OA\Get(
     *     path="/api/places",
     *     tags={"Places"},
     *     summary="Get a list of places",
     *     @OA\Response(
     *         response=200,
     *         description="List of places.",
     *         @OA\JsonContent(type="array", @OA\Items(ref="/schemas/Place"))
     *     )
     * )
     */

    public function index(Request $request)
    {
        $sort = $request->query('sort');

        $query = Place::with('route');
    
        if ($sort && in_array($sort, ['created_at'])) {
            $query->orderBy($sort, 'desc');
        }
    
        $places = $query->get();
    
        return response()->json($places);
    }

     /**
     * @OA\Get(
     *     path="/api/routes/{routeId}/places/{placeId}",
     *     tags={"Places"},
     *     summary="Get a specific place",
     *     @OA\Parameter(
     *         name="routeId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="placeId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Place retrieved successfully.",
     *         @OA\JsonContent(ref="/schemas/Place")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Place does not belong to this route."
     *     )
     * )
     */

    public function show(Route $route, Place $place)
    {
        if ($place->route_id !== $route->id) {
            return response()->json(['message' => 'Place does not belong to this route'], 403);
        }
    
        $place->load(['route']);
        return response()->json($place, 200);
    }

     /**
     * @OA\Put(
     *     path="/api/routes/{routeId}/places/{placeId}",
     *     tags={"Places"},
     *     summary="Update a specific place",
     *     @OA\Parameter(
     *         name="routeId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="placeId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="/schemas/PlaceRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Place updated successfully.",
     *         @OA\JsonContent(ref="/schemas/Place")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="You are not authorized to update this place."
     *     )
     * )
     */

    public function update(Request $request, Route $route, Place $place)
    {
        $user = auth('sanctum')->user();
        if ($route->user_id !== $user->id) {
            return response()->json(['message' => 'You are not authorized to update this place.'], 403);
        }

        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'latitude'=> 'required|numeric',
            'longitude'=> 'required|numeric',
        ]);

        $place->update([
            'name' => $request->name,
            'description' => $request->description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json($place, 200);
    }

       /**
     * @OA\Delete(
     *     path="/api/routes/{routeId}/places/{placeId}",
     *     tags={"Places"},
     *     summary="Delete a specific place",
     *     @OA\Parameter(
     *         name="routeId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="placeId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Place deleted successfully."
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="You are not authorized to delete this place."
     *     )
     * )
     */

    public function destroy(Route $route, Place $place)
    {
        $user = auth('sanctum')->user();
        if ($route->user_id !== $user->id) {
        return response()->json(['message' => 'You are not authorized to delete this place.'], 403);
        }

        $place->delete();

        return response()->json(['message' => 'Route deleted successfully.'], 200);
    }

    /**
 * @OA\Get(
 *     path="/weather/{route}/{place}",
 *     summary="Get weather data for a specific place",
 *     tags={"Weather"},
 *     @OA\Parameter(
 *         name="route",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="place",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful response with weather data",
 *         @OA\JsonContent(ref="/schemas/WeatherResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Place does not belong to this route",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Place does not belong to this route")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to fetch weather data",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Failed to fetch weather data")
 *         )
 *     )
 * )
 */

    public function getWeather(Route $route, Place $place)
    {
        $apiKey = config('services.openweather.api_key');

        $cacheKey = "weather_route_{$route->id}_place_{$place->id}";

        if ($place->route_id !== $route->id) {
            return response()->json(['message' => 'Place does not belong to this route'], 403);
        }

        $weatherData = Cache::remember($cacheKey, 60 * 60, function() use($place, $apiKey){
            $response = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                'lat' => $place->latitude,
                'lon' => $place->longitude,
                'appid' => $apiKey,
                'units' => 'metric',
            ]);
            if ($response->successful()) {
                $weatherData = $response->json();
                return [
                    'temperature' => $weatherData['main']['temp'],
                    'humidity' => $weatherData['main']['humidity'],
                    'weather' => $weatherData['weather'][0]['description'],
                    'wind_speed' => $weatherData['wind']['speed'],
                    'city_name' => $weatherData['name'],
                ];
            }
            return null;
        });
        if ($weatherData) {
            return response()->json($weatherData, 200);
        }

        return response()->json(['message' => 'Failed to fetch weather data'], 500);
    }

    /**
 * @OA\Get(
 *     path="/hotel/{route}/{place}",
 *     summary="Get hotel data for a specific place",
 *     tags={"Hotels"},
 *     @OA\Parameter(
 *         name="route",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="place",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful response with hotel data",
 *         @OA\JsonContent(ref="/schemas/HotelResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Place does not belong to this route",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Place does not belong to this route")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to fetch hotels data",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Failed to fetch hotels data")
 *         )
 *     )
 * )
 */

    public function getHotel(Route $route, Place $place)
    {
        $apiKey = config('services.booking.key');

        $cacheKey = "hotel_route_{$route->id}_place_{$place->id}";
        if ($place->route_id !== $route->id) {
            return response()->json(['message' => 'Place does not belong to this route'], 403);
        }

        $arrivalDate = now()->addDays(1)->format('Y-m-d');
        $departureDate = now()->addDays(2)->format('Y-m-d');

        $hotelsData = Cache::remember($cacheKey, 60 * 60, function() use ($place, $apiKey, $arrivalDate, $departureDate){
            $response = Http::withHeaders([
                'x-rapidapi-key' => $apiKey,
                'x-rapidapi-host' => 'booking-com.p.rapidapi.com'
            ])->get('https://booking-com.p.rapidapi.com/v1/hotels/search-by-coordinates', [
                'latitude' => $place->latitude,
                'longitude' => $place->longitude,
                'locale' => 'en-us',
                'filter_by_currency' => 'USD',
                'order_by' => 'popularity',
                'units' => 'metric',
                'checkin_date' => $arrivalDate,
                'checkout_date' => $departureDate,
                'room_number' => 1,
                'adults_number' => 1,
                'children_number' => 1,
            ]);

            if ($response->successful()) {
                $hotelsData = $response->json();
                
                return collect($hotelsData['result'])->map(function ($hotel) {
                    return [
                        'name' => $hotel['hotel_name'],
                        'address' => $hotel['city'] . ', ' . $hotel['countrycode'],
                        'price' => $hotel['min_total_price'] . ' ' . $hotel['currencycode'],
                        'rating' => $hotel['review_score'],
                        'reviewWord' => $hotel['review_score_word'],
                        'checkin' => $hotel['checkin']['from'],
                        'checkout' => $hotel['checkout']['until'],
                        'photo' => $hotel['main_photo_url'],
                        'coordinates' => [
                            'latitude' => $hotel['latitude'],
                            'longitude' => $hotel['longitude'],
                        ],
                        // 'hasFreeParking' => $hotel['has_free_parking'] == 1,
                        // 'hasSwimmingPool' => $hotel['has_swimming_pool'] == 1 ?? 0,
                        'badges' => $hotel['badges'],
                        'class' => $hotel['class'] . ' star',
                    ];
                })->toArray();
            }
            return null;
        });

        if($hotelsData)
        {
            return response()->json($hotelsData, 200);
        }

        return response()->json(['message' => 'Failed to fetch hotels data'], 500);
    }
}
