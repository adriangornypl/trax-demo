<?php

namespace App\Http\Controllers;

use App\Events\TripWasAdded;
use App\Events\TripWasRemoved;
use App\Http\Requests\CreateTripRequest;
use App\Http\Resources\Trip as TripResource;
use App\Models\Trip;
use App\Models\User;
use App\Repository\TripRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TripsController extends Controller
{
    public function __construct(
        private readonly TripRepository $tripRepository
    ) {
        $this->middleware('auth:api');
    }

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Trip::class);

        /** @var User $user */
        $user = auth()->user();

        return TripResource::collection(
            $this->tripRepository->findTripsForUser($user)
        );
    }

    public function create(CreateTripRequest $request): TripResource
    {
        $this->authorize('create', Trip::class);

        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validated();

        $model = new Trip($validated);
        $model->user_id = $user->id;
        $model->total = $this->tripRepository->getTotalMilesForUser($user->id) + (float) $model->miles;

        $this->tripRepository->save($model);

        TripWasAdded::dispatch($model->miles, $user->id);

        return TripResource::make($model->fresh());
    }

    public function remove(Trip $trip): JsonResponse
    {
        $this->authorize('delete', $trip);

        $this->tripRepository->remove($trip);

        TripWasRemoved::dispatch($trip->miles, $trip->user_id);

        return response()->json();
    }
}
