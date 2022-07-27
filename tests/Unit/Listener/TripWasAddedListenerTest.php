<?php

namespace Tests\Unit\Listener;

use App\Events\TripWasAdded;
use App\Listeners\TripWasAddedListener;
use App\Models\Car;
use App\Repository\CarRepository;
use Mockery\Mock;
use Tests\TestCase;

class TripWasAddedListenerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testItHandlesEventCorrectly()
    {
        $ev = new TripWasAdded(
            miles: 100,
            car_id: 1
        );
        /** @var Mock|Car $car */
        $car = $this->mock(Car::class)->makePartial();

        $repositoryMock = $this->createMock(CarRepository::class);
        $repositoryMock
            ->expects($this->once())
            ->method('findOne')
            ->willReturn($car);

        $repositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($car);

        $listener = new TripWasAddedListener($repositoryMock);
        $listener->handle($ev);

        $this->assertEquals(1, $car->trip_count);
        $this->assertEquals(100, $car->trip_miles);
    }
}
