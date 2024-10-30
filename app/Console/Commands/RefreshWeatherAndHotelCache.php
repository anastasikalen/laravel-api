<?php

namespace App\Console\Commands;

use App\Models\Route;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RefreshWeatherAndHotelCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:refresh-weather-hotel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshes weather and hotel data cache for routes';

    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $routes = Route::all();

        foreach ($routes as $route) {
            foreach ($route->places as $place) {
                Cache::forget("weather_route_{$route->id}_place_{$place->id}");
                $weatherData = app(\App\Http\Controllers\PlaceController::class)->getWeather($route, $place);
                Cache::put("weather_route_{$route->id}_place_{$place->id}", $weatherData, now()->addHours(3));

                Cache::forget("hotel_route_{$route->id}_place_{$place->id}");
                $hotelData = app(\App\Http\Controllers\PlaceController::class)->getHotel($route, $place);
                Cache::put("hotel_route_{$route->id}_place_{$place->id}", $hotelData, now()->addHours(3));
            }
        }

        $this->info('Weather and hotel cache refreshed for all routes.');
    }
}
