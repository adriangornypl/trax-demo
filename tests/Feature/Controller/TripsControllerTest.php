<?php

namespace Tests\Feature\Controller;

use App\Models\Car;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $defaultHeaders = [
        'Accept' => 'application/json',
    ];

    public function testItFailsToListTripsAsUnauthorizedUser(): void
    {
        $response = $this->get('/api/trips', $this->defaultHeaders);

        $response->assertStatus(401);
    }

    public function testItSuccessfullyListTrips(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create([
            'user_id' => $user->id,
        ]);
        Trip::factory(3)->create([
            'user_id' => $user->id,
            'car_id' => $car->id,
        ]);

        $response = $this->actingAs($user, 'api')
            ->get('/api/trips', $this->defaultHeaders);

        $response->assertStatus(200);

        $responseContent = json_decode($response->getContent(), true);
        $this->assertEquals(3, count($responseContent['data']));

        // fetch last trip and compare
        $lastTrip = Trip::where([])->orderBy('id', 'DESC')->first()->toArray();

        $this->assertEquals($lastTrip, $responseContent['data'][0]);
    }

    public function testItFailsToCreateTripAsUnauthorizedUser(): void
    {
        $data = [
            'year' => 2020,
            'make' => 'Tesla',
            'model' => 'Model S',
        ];

        $response = $this->postJson('/api/trips', $data, $this->defaultHeaders);

        $response->assertStatus(401);
    }

    /**
     * @dataProvider createTripDataProvider
     */
    public function testItCreatesTripAsAuthenticatedUser(int $responseCode, array $data, array $expectedData): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create([
            'user_id' => $user->id,
        ]);

        $data = array_merge($data, [
            'car_id' => $car->id,
        ]);

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/trips', $data, $this->defaultHeaders);

        $response->assertStatus($responseCode);
        $response->assertJson($expectedData);

        if ($responseCode === 200) {
            $car = Car::find($car->id);
            $this->assertEquals(1, $car->trip_count);
            $this->assertEquals(100.1, $car->trip_miles);
        }
    }

    public function testItFailsToRemoveTripAsUnauthorizedUser(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create([
            'user_id' => $user->id,
        ]);
        Trip::factory(3)->create([
            'user_id' => $user->id,
            'car_id' => $car->id,
        ]);

        $response = $this->deleteJson('/api/trips/1', [], $this->defaultHeaders);

        $response->assertStatus(401);
    }

    public function testItRemovesTripAsAuthorizedUser(): void
    {
        $user = User::factory()->create();
        $car = Car::factory()->create([
            'user_id' => $user->id,
            'trip_count' => 2,
            'trip_miles' => 200.1,
        ]);
        Trip::factory(3)->create([
            'user_id' => $user->id,
            'car_id' => $car->id,
            'miles' => 90,
        ]);

        $firstTrip = Trip::first();

        $response = $this->actingAs($user, 'api')
            ->deleteJson('/api/trips/'.$firstTrip->id, [], $this->defaultHeaders);

        $response->assertStatus(200);
        $deletedTrip = Trip::find($firstTrip->id);
        $this->assertNull($deletedTrip);

        if ($response->getStatusCode() === 200) {
            $car = Car::find($car->id);
            $this->assertEquals(1, $car->trip_count);
            $this->assertEquals(110.1, $car->trip_miles);
        }
    }

    public function createTripDataProvider(): array
    {
        return [
            'it successfully creates trip' => [
                'expectedCode' => 200,
                'data' => [
                    'date' => '2022-01-01',
                    'miles' => '100.1',
                ],
                'expectedData' => ['data' => [
                    'date' => '2022-01-01',
                    'miles' => '100.1',
                ]],
            ],
            'it fails to create trip with invalid data' => [
                'expectedCode' => 422,
                'data' => [
                    'date' => '2022-01-01',
                    'miles' => 'somethingwrong',
                ],
                'expectedData' => [
                    'message' => 'The miles must be a number.',
                    'errors' => [
                        'miles' => ['The miles must be a number.'],
                    ],
                ],
            ],
        ];
    }
}
