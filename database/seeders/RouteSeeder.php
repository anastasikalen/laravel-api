<?php

namespace Database\Seeders;

use App\Models\Route;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Route::insert([
            [
                'user_id' => 1,
                'title' => 'Путешествие по Европе',
                'description' => 'Маршрут через главные города Европы',
                'created_at' => '2024-10-19 12:00:00',
                'updated_at' => '2024-10-19 12:00:00',
            ],
            [
                'user_id' => 1,
                'title' => 'Маршрут по столицам мира',
                'description' => 'Путешествие по столицам различных стран мира',
                'created_at' => '2024-10-19 12:00:00',
                'updated_at' => '2024-10-19 12:00:00',
            ],
            [
                'user_id' => 1,
                'title' => 'Прогулка по России',
                'description' => 'Маршрут через главные города России',
                'created_at' => '2024-10-19 12:00:00',
                'updated_at' => '2024-10-19 12:00:00',
            ]
        ]);
    }
}
