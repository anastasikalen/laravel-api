<?php

namespace Database\Seeders;

use App\Models\Place;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Place::insert([
            ['route_id' => 1, 'name' => 'Париж', 'description' => 'Столица Франции', 'latitude' => 48.8566, 'longitude' => 2.3522, 'created_at' => '2024-10-19 12:00:00', 'updated_at' => '2024-10-19 12:00:00'],
            ['route_id' => 1, 'name' => 'Берлин', 'description' => 'Столица Германии', 'latitude' => 52.5200, 'longitude' => 13.4050, 'created_at' => '2024-10-19 12:00:00', 'updated_at' => '2024-10-19 12:00:00'],
            ['route_id' => 1, 'name' => 'Амстердам', 'description' => 'Столица Нидерландов', 'latitude' => 52.3676, 'longitude' => 4.9041, 'created_at' => '2024-10-19 12:00:00', 'updated_at' => '2024-10-19 12:00:00'],

            ['route_id' => 2, 'name' => 'Вашингтон', 'description' => 'Столица США', 'latitude' => 38.9072, 'longitude' => -77.0369, 'created_at' => '2024-10-19 12:00:00', 'updated_at' => '2024-10-19 12:00:00'],
            ['route_id' => 2, 'name' => 'Токио', 'description' => 'Столица Японии', 'latitude' => 35.6762, 'longitude' => 139.6503, 'created_at' => '2024-10-19 12:00:00', 'updated_at' => '2024-10-19 12:00:00'],
            ['route_id' => 2, 'name' => 'Канберра', 'description' => 'Столица Австралии', 'latitude' => -35.2809, 'longitude' => 149.1300, 'created_at' => '2024-10-19 12:00:00', 'updated_at' => '2024-10-19 12:00:00'],

            ['route_id' => 3, 'name' => 'Москва', 'description' => 'Столица России', 'latitude' => 55.7558, 'longitude' => 37.6173, 'created_at' => '2024-10-19 12:00:00', 'updated_at' => '2024-10-19 12:00:00'],
            ['route_id' => 3, 'name' => 'Санкт-Петербург', 'description' => 'Культурная столица России', 'latitude' => 59.9343, 'longitude' => 30.3351, 'created_at' => '2024-10-19 12:00:00', 'updated_at' => '2024-10-19 12:00:00'],
            ['route_id' => 3, 'name' => 'Казань', 'description' => 'Один из крупнейших городов России', 'latitude' => 55.8304, 'longitude' => 49.0661, 'created_at' => '2024-10-19 12:00:00', 'updated_at' => '2024-10-19 12:00:00'],
        ]);
    }
}
