<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id', 
        'name',
        'description',
        'latitude',
        'longitude'
        ];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
