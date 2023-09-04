<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\State;
class City extends Model
{
    use HasFactory;
    protected $table = "cities";
    protected $primaryKey = "cityID";

    protected $fillable = [
        'cityName',
    ];

    function getState(): HasOne
    {
        return $this->hasOne(State::class);
    }
}
