<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Tests\Traits\TestValidations;
use Tests\Traits\TestSaves;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    /**
     * @var Category
     */
    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = factory(Category::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route('api.categories.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$this->category->toArray()])
        ;
    }

    public function testShow()
    {
        $response = $this->get(route('api.categories.show', ['category' => $this->category->id]));

        $response
            ->assertStatus(200)
            ->assertJson($this->category->toArray())
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
        $response = $this->assertStore($data, $data + ['description' => null, 'is_active' => true, 'deleted_at' => null]);
        $response->assertJsonStructure(
            [
                'created_at', 'updated_at'
            ]
        );

        $data = [
            'name' => 'test',
            'description' => 'description',
            'is_active' => false
        ];
        $this->assertStore($data, $data + ['description' => 'description', 'is_active' => false]);
    }

    public function testUpdate()
    {
        $this->category = factory(Category::class)->create(
            [
                'description' => 'description',
                'is_active' => false
            ]
        );
        $data = [
            'name' => 'test',
            'description' => 'test',
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
            'name' => 'test',
            'description' => '',
        ];
        $this->assertUpdate(
            $data, array_merge($data, ['description' => null])
        );

        $data['description'] = 'test';
        $this->assertUpdate(
            $data, array_merge($data, ['description' => 'test'])
        );

        $data['description'] = null;
        $this->assertUpdate(
            $data, array_merge($data, ['description' => null])
        );
    }

    public function testDelete()
    {
        $id = $this->category->id;

        $response = $this->json(
            'DELETE',
            route('api.categories.destroy', ['category' => $id])
        );

        $response->assertStatus(204);

        $response = $this->json(
            'GET',
            route('api.categories.show', ['category' => $id])
        );

        $response->assertStatus(404);

        $this->assertNull(Category::find($id));
        $this->assertNotNull(Category::withTrashed()->find($id));
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
        return route('api.categories.store');
    }

    protected function routeUpdate()
    {
        return route('api.categories.update', ['category' => $this->category->id]);
    }

    protected function model()
    {
        return Category::class;
    }
}
