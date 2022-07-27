<?php

namespace App\Listeners;

use App\Events\TripWasAdded;
use App\Repository\CarRepository;

class TripWasAddedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        private readonly CarRepository $carRepository
    ) {
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\TripWasAdded  $event
     * @return void
     */
    public function handle(TripWasAdded $event)
    {
        $car = $this->carRepository->findOne($event->car_id);

        $car->trip_count++;
        $car->trip_miles += $event->miles;

        $this->carRepository->save($car);
    }
}
