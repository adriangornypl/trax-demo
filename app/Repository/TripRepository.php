<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class TripRepository implements RepositoryInterface
{
    /**
     * @param User $user
     * @param string|null $sortBy
     * @param string $sortWay
     * @return Collection|Trip[]
     */
    public function findTripsForUser(User $user, ?string $sortBy = 'id', string $sortWay = 'DESC'): Collection
    {
        $criteria = [
            'user_id' => $user->id,
        ];

        $query = Trip::where($criteria);

        if ($sortBy) {
            $query->orderBy($sortBy, $sortWay);
        }

        return $query->get();
    }

    public function findOne(int $id): ?Trip
    {
        return Trip::find($id);
    }

    /**
     * @return Collection|Trip[]
     */
    public function findBy(array $criteria = []): Collection
    {
        return Trip::where($criteria)->get();
    }

    /**
     * @param Trip $model
     */
    public function save($model): void
    {
        $model->push();
    }

    /**
     * @param Trip $model
     */
    public function remove($model, bool $force = false): void
    {
        if ($force) {
            $model->forceDelete();
        } else {
            $model->delete();
        }
    }

    public function getTotalMilesForUser(int $id): float
    {
        return (float) Trip::whereUserId($id)->orderBy('id', 'DESC')->first()?->total ?: 0.0;
    }
}
