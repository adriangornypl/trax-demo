<?php

namespace App\Listeners;

use App\Events\TripWasRemoved;
use App\Repository\CarRepository;

class TripWasRemovedListener
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
     * @param  \App\Events\TripWasRemoved  $event
     * @return void
     */
    public function handle(TripWasRemoved $event)
    {
        $car = $this->carRepository->findOne($event->car_id);

        $car->trip_count--;
        $car->trip_miles -= $event->miles;

        $this->carRepository->save($car);
    }
}
