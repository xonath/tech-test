<?php

namespace App\Models;

use App\Models\Enums\StoreTypesEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Store extends Model
{
    use HasFactory;

    protected const HAVENSINE = "(? * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'is_open',
        'store_type',
        'max_delivery_distance',
        'postcode_id',
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'store_type' => StoreTypesEnum::class,
        'max_delivery_distance' => 'float',
    ];

    public function postcode(): BelongsTo
    {
        return $this->belongsTo(Postcode::class);
    }

    public function scopeWithinRadius(Builder $query, float $latitude, float $longitude, float $radius, int $unitOfMeasurement): Builder
    {
        // The (? + 0.0) resolves an issue where sqlite changes floats to strings
        return $query->select('*')
            ->selectRaw(self::HAVENSINE . " as distance", [$unitOfMeasurement, $latitude, $longitude, $latitude])
            ->whereRaw(self::HAVENSINE . " < (? + 0.0)", [$unitOfMeasurement, $latitude, $longitude, $latitude, $radius]);
    }

    public function scopeCanDeliverTo(Builder $query, float $latitude, float $longitude, int $unitOfMeasurement): Builder
    {
        return $query->select('*')
            ->selectRaw(self::HAVENSINE . " as distance", [$unitOfMeasurement, $latitude, $longitude, $latitude])
            ->having('distance', '<=', DB::raw('max_delivery_distance'));
    }
}
