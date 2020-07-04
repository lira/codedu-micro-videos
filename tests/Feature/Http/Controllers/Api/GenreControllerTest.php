<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    /**
     * @var Genre
     */
    private $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = factory(Genre::class)->create();
    }

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
        $data = [
            'name' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [
            'name' => str_repeat('a', 256),
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

        $data = [
            'is_active' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function testStore()
    {
        $data = ['name' => 'test'];
        $response = $this->assertStore($data, $data + ['is_active' => true, 'deleted_at' => null]);
        $response->assertJsonStructure(
            [
                'created_at', 'updated_at'
            ]
        );

        $data = [
            'name' => 'test',
            'is_active' => false
        ];
        $this->assertStore($data, $data + ['is_active' => false]);
    }

    public function testUpdate()
    {
        $this->genre = factory(Genre::class)->create(
            [
                'is_active' => false
            ]
        );
        $data = [
            'name' => 'test',
            'is_active' => true,
        ];
        $response = $this->assertUpdate(
            $data, $data + ['deleted_at' => null]
        );
        $response->assertJsonStructure(
            [
                'created_at', 'updated_at'
            ]
        );

        $data = [
            'name' => 'test updated',
        ];
        $this->assertUpdate(
            $data, $data
        );
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

        $this->assertInvalidationFields(
            $response, ['name'], 'required', []
        );

        $response->assertJsonMissingValidationErrors(['is_active']);
    }

    protected function assertInvalidationMax(TestResponse $response)
    {
        $this->assertInvalidationFields(
            $response, ['name'], 'max.string', ['max' => 255]
        );
    }

    protected function assertInvalidationBoolean(TestResponse $response)
    {
        $this->assertInvalidationFields(
            $response, ['is_active'], 'boolean'
        );
    }

    protected function routeStore()
    {
        return route('api.genres.store');
    }

    protected function routeUpdate()
    {
        return route('api.genres.update', ['genre' => $this->genre->id]);
    }

    protected function model()
    {
        return Genre::class;
    }
}
