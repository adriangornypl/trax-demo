<?php

namespace Tests\Feature\Controller;

use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $defaultHeaders = [
        'Accept' => 'application/json',
    ];

    public function testItFailsToListCarsAsUnauthorizedUser(): void
    {
        $response = $this->get('/api/cars', $this->defaultHeaders);

        $response->assertStatus(401);
    }

    public function testItSuccessfullyListCars(): void
    {
        $user = User::factory()->create();
        Car::factory(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'api')
            ->get('/api/cars', $this->defaultHeaders);

        $response->assertStatus(200);

        $responseContent = json_decode($response->getContent(), true);
        $this->assertEquals(3, count($responseContent['data']));

        // fetch last car and compare
        $lastCar = Car::where([])->orderBy('id', 'DESC')->first()->toArray();

        $this->assertEquals($lastCar, $responseContent['data'][0]);
    }

    public function testItFailsToCreateCarAsUnauthorizedUser(): void
    {
        $data = [
            'year' => 2020,
            'make' => 'Tesla',
            'model' => 'Model S',
        ];

        $response = $this->postJson('/api/cars', $data, $this->defaultHeaders);

        $response->assertStatus(401);
    }

    /**
     * @dataProvider createCarDataProvider
     */
    public function testItCreatesCarAsAuthenticatedUser(int $responseCode, array $data, array $expectedData): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/cars', $data, $this->defaultHeaders);

        $response->assertStatus($responseCode);
        $response->assertJson($expectedData);
    }

    public function testItFailsToRemoveCarAsUnauthorizedUser(): void
    {
        $user = User::factory()->create();
        Car::factory(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson('/api/cars/1', [], $this->defaultHeaders);

        $response->assertStatus(401);
    }

    public function testItRemovesCarAsAuthorizedUser(): void
    {
        $user = User::factory()->create();
        Car::factory(3)->create([
            'user_id' => $user->id,
        ]);

        $firstCar = Car::first();

        $response = $this->actingAs($user, 'api')
            ->deleteJson('/api/cars/'.$firstCar->id, [], $this->defaultHeaders);

        $response->assertStatus(200);
        $deletedCar = Car::find($firstCar->id);
        $this->assertNull($deletedCar);
    }

    public function createCarDataProvider(): array
    {
        return [
            'it successfully creates car' => [
                'expectedCode' => 201,
                'data' => [
                    'year' => 2020,
                    'make' => 'Tesla',
                    'model' => 'Model S',
                ],
                'expectedData' => ['data' => [
                    'year' => 2020,
                    'make' => 'Tesla',
                    'model' => 'Model S',
                    'trip_count' => 0,
                    'trip_miles' => '0.0',
                ]],
            ],
            'it fails to create car with invalid data' => [
                'expectedCode' => 422,
                'data' => [
                    'year' => 'test',
                    'make' => 'Tesla',
                    'model' => 'Model S',
                ],
                'expectedData' => [
                    'message' => 'The year must be an integer.',
                    'errors' => [
                        'year' => ['The year must be an integer.'],
                    ],
                ],
            ],
        ];
    }
}
