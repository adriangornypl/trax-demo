<?php

namespace App\Models;

use Database\Factories\CarFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Model\Car.
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Car newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Car newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Car query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $make
 * @property string $model
 * @property int $year
 * @property int $trip_count
 * @property float $trip_miles
 * @property int $user_id
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereMake($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereTripCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereTripMiles($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Car whereYear($value)
 */
class Car extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'cars';

    protected $fillable = ['make', 'model', 'year'];

    protected $casts = [
        'trip_count' => 'integer',
        'trip_miles' => 'decimal:1',
    ];

    protected $attributes = [
        'trip_count' => 0,
        'trip_miles' => 0.0,
    ];

    public function toArray()
    {
        return [
            'id' => $this->id,
            'make' => $this->make,
            'model' => $this->model,
            'year' => $this->year,
            'trip_count' => $this->trip_count,
            'trip_miles' => $this->trip_miles,
        ];
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return CarFactory::new();
    }

    public function isOwnedBy(User $user): bool
    {
        return $user->id === $this->user_id;
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
