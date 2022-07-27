<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Model\Trip.
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Trip newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Trip newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Trip query()
 * @mixin \Eloquent
 * @property int $id
 * @property DateTimeInterface $date
 * @property float $total
 * @property float $miles
 * @property int $car_id
 * @property int $user_id
 * @method static \Illuminate\Database\Eloquent\Builder|Trip whereCarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trip whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trip whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trip whereMiles($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trip whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trip whereUserId($value)
 */
class Trip extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'trips';

    protected $fillable = ['date', 'miles', 'car_id'];

    protected $casts = [
        'total' => 'decimal:1',
        'miles' => 'decimal:1',
        'date' => 'date',
    ];

    public function toArray()
    {
        return [
            'id' => $this->id,
            'date' => $this->date->format('Y-m-d'),
            'miles' => $this->miles,
            'total' => $this->total,
            'car' => $this->car()->first() ? $this->car()->first()->toArray() : [],
        ];
    }

    public function isOwnedBy(User $user): bool
    {
        return $user->id === $this->user_id;
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
