<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCarRequest;
use App\Http\Resources\Car as CarResource;
use App\Models\Car;
use App\Models\User;
use App\Repository\CarRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CarsController extends Controller
{
    public function __construct(
        private readonly CarRepository $carRepository
    ) {
        $this->middleware('auth:api');
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Car::class);

        /** @var User $user */
        $user = auth()->user();

        return CarResource::collection(
            $this->carRepository->findCarsForUser($user)
        );
    }

    public function create(CreateCarRequest $request): CarResource
    {
        $this->authorize('create', Car::class);

        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validated();

        $model = new Car($validated);
        $model->user_id = $user->id;

        $this->carRepository->save($model);

        return CarResource::make($model);
    }

    public function remove(Car $car): JsonResponse
    {
        $this->authorize('delete', $car);

        $this->carRepository->remove($car);

        return response()->json();
    }
}
