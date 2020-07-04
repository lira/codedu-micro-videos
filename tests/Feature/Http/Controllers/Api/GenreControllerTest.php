<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('api.genres.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$genre->toArray()])
        ;
    }

    public function testShow()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('api.genres.show', ['genre' => $genre->id]));

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray())
        ;
    }

    public function testInvalidationData()
    {
        $response = $this->json('POST', route('api.genres.store'), []);
        $this->assertInvalidationResponse($response);

        $response = $this->json(
            'POST',
            route('api.genres.store'),
            [
                'name' => str_repeat('a', 256),
                'is_active' => 'a'
            ]
        );
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        $genre = factory(Genre::class)->create();
        $response = $this->json('PUT', route('api.genres.update', ['genre' => $genre->id]), []);
        $this->assertInvalidationResponse($response);

        $response = $this->json(
            'PUT',
            route('api.genres.update', ['genre' => $genre->id]),
            [
                'name' => str_repeat('a', 256),
                'is_active' => 'a'
            ]
        );
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);
    }

    public function testStore()
    {
        $response = $this->json('POST', route('api.genres.store'), [
            'name' => 'test'
        ]);

        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($genre->toArray())
        ;
        $this->assertTrue($response->json('is_active'));

        $response = $this->json('POST', route('api.genres.store'), [
            'name' => 'test',
            'is_active' => false
        ]);
        $response->assertJsonFragment(
            [
                'name' => 'test',
                'is_active' => false
            ]
        );
    }

    public function testUpdate()
    {
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create(
            [
                'name' => 'name',
                'is_active' => false
            ]
        );

        $response = $this->json(
            'PUT',
            route('api.genres.update', ['genre' => $genre->id]),
            [
                'name' => 'test',
                'is_active' => true,
            ]
        );

        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray())
            ->assertJsonFragment(
                [
                    'name' => 'test',
                    'is_active' => true,
                ]
            )
        ;
    }

    public function testDelete()
    {
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create();

        $id = $genre->id;

        $response = $this->json(
            'DELETE',
            route('api.genres.destroy', ['genre' => $id])
        );

        $response->assertStatus(204);

        $response = $this->json(
            'GET',
            route('api.genres.show', ['genre' => $id])
        );

        $response->assertStatus(404);


        $this->assertNull(Genre::find($id));
        $this->assertNotNull(Genre::withTrashed()->find($id));
    }

    protected function assertInvalidationResponse(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissing(['is_active'])
            ->assertJsonFragment(
                [\Lang::get('validation.required', ['attribute' => 'name'])]
            )
        ;
    }

    protected function assertInvalidationMax(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment(
                [\Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])]
            )
        ;
    }

    protected function assertInvalidationBoolean(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['is_active'])
            ->assertJsonFragment(
                [\Lang::get('validation.boolean', ['attribute' => 'is active'])]
            )
        ;
    }
}
