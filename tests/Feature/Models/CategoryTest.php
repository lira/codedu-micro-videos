<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Category::class, 1)->create();
        $categories = Category::all();
        $this->assertCount(1, $categories);
        $categoryKeys = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            ['id', 'name', 'description', 'is_active', 'created_at', 'updated_at', 'deleted_at'],
            $categoryKeys
        );
    }

    public function testCreate()
    {
        $uuidPattern = '/[a-f0-9]{8}-?[a-f0-9]{4}-?4[a-f0-9]{3}-?[89ab][a-f0-9]{3}-?[a-f0-9]{12}/i';
        $category = Category::create(
            [
                'name' => 'test1'
            ]
        );
        $category->refresh();

        $this->assertEquals('test1', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue((boolean) $category->is_active);
        $this->assertRegExp($uuidPattern, $category->id);
        $this->assertEquals(36, mb_strlen($category->id));

        $category = Category::create(
            [
                'name' => 'test1',
                'description' => null
            ]
        );
        $category->refresh();

        $this->assertEquals('test1', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);
        $this->assertRegExp($uuidPattern, $category->id);
        $this->assertEquals(36, mb_strlen($category->id));

        $category = Category::create(
            [
                'name' => 'test1',
                'description' => 'test description'
            ]
        );
        $category->refresh();

        $this->assertEquals('test1', $category->name);
        $this->assertEquals('test description', $category->description);
        $this->assertTrue($category->is_active);
        $this->assertRegExp($uuidPattern, $category->id);
        $this->assertEquals(36, mb_strlen($category->id));

        $category = Category::create(
            [
                'name' => 'test1',
                'description' => 'test description',
                'is_active' => false
            ]
        );
        $category->refresh();

        $this->assertEquals('test1', $category->name);
        $this->assertEquals('test description', $category->description);
        $this->assertFalse($category->is_active);
        $this->assertRegExp($uuidPattern, $category->id);
        $this->assertEquals(36, mb_strlen($category->id));
    }

    public function testUpdate()
    {
        /** @var Category $category */
        $category = factory(Category::class)->create(
            [
                'description' => 'description test',
                'is_active' => false,
            ]
        );
        $data = [
            'name' => 'test name updated',
            'description' => 'test description updated',
            'is_active' => true,
        ];
        $category->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testDelete()
    {
        /** @var Category $category */
        $category = factory(Category::class, 1)->create(
            [
                'description' => 'description test',
                'is_active' => false,
            ]
        )->first();
        $data = $category->toArray();
        $category->delete();
        $this->assertNull(Category::find($category->id));
        $this->assertSoftDeleted('categories', $data);
        $category->restore();
        $this->assertNotNull(Category::find($category->id));
    }
}
