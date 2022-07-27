<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class CarRepository implements RepositoryInterface
{
    /**
     * @param User $user
     * @param string|null $sortBy
     * @param string $sortWay
     * @return Collection|Car[]
     */
    public function findCarsForUser(User $user, ?string $sortBy = 'id', string $sortWay = 'DESC'): Collection
    {
        $criteria = [
            'user_id' => $user->id,
        ];

        $query = Car::where($criteria);

        if ($sortBy) {
            $query->orderBy($sortBy, $sortWay);
        }

        return $query->get();
    }

    public function findOne(int $id): ?Car
    {
        return Car::find($id);
    }

    /**
     * @return Collection|Car[]
     */
    public function findBy(array $criteria = []): Collection
    {
        return Car::where($criteria)->get();
    }

    /**
     * @param Car $model
     */
    public function save($model): void
    {
        $model->push();
    }

    /**
     * @param Car $model
     */
    public function remove($model, bool $force = false): void
    {
        if ($force) {
            $model->forceDelete();
        } else {
            $model->delete();
        }
    }
}
