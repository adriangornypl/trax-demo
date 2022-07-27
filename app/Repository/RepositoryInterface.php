<?php

declare(strict_types=1);

namespace App\Repository;

interface RepositoryInterface
{
    public function findOne(int $id);

    public function findBy(array $criteria = []);

    public function save($model): void;

    public function remove($model): void;
}
