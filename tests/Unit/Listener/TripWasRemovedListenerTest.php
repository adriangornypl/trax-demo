<?php

namespace Tests\Unit\Listener;

use App\Events\TripWasRemoved;
use App\Listeners\TripWasRemovedListener;
use App\Models\Car;
use App\Repository\CarRepository;
use Mockery\Mock;
use Tests\TestCase;

class TripWasRemovedListenerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testItHandlesEventCorrectly()
    {
        $ev = new TripWasRemoved(
            miles: 100,
            car_id: 1
        );
        /** @var Mock|Car $car */
        $car = $this->mock(Car::class)->makePartial();
        $car->trip_miles = 100;
        $car->trip_count = 1;

        $repositoryMock = $this->createMock(CarRepository::class);
        $repositoryMock
            ->expects($this->once())
            ->method('findOne')
            ->willReturn($car);

        $repositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($car);

        $listener = new TripWasRemovedListener($repositoryMock);
        $listener->handle($ev);

        $this->assertEquals(0, $car->trip_count);
        $this->assertEquals(0, $car->trip_miles);
    }
}
