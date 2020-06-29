<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Genre::class, 1)->create();
        $genre = Genre::all();
        $this->assertCount(1, $genre);
        $genreKeys = array_keys($genre->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            ['id', 'name', 'is_active', 'created_at', 'updated_at', 'deleted_at'],
            $genreKeys
        );
    }

    public function testCreate()
    {
        $uuidPattern = '/[a-f0-9]{8}-?[a-f0-9]{4}-?4[a-f0-9]{3}-?[89ab][a-f0-9]{3}-?[a-f0-9]{12}/i';
        $genre = Genre::create(
            [
                'name' => 'test1'
            ]
        );
        $genre->refresh();

        $this->assertEquals('test1', $genre->name);
        $this->assertTrue((boolean) $genre->is_active);
        $this->assertRegExp($uuidPattern, $genre->id);
        $this->assertEquals(36, mb_strlen($genre->id));

        $genre = Genre::create(
            [
                'name' => 'test1',
                'is_active' => false
            ]
        );
        $genre->refresh();

        $this->assertEquals('test1', $genre->name);
        $this->assertFalse($genre->is_active);
        $this->assertRegExp($uuidPattern, $genre->id);
        $this->assertEquals(36, mb_strlen($genre->id));
    }

    public function testUpdate()
    {
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create(
            [
                'name' => 'name test',
                'is_active' => false,
            ]
        );
        $data = [
            'name' => 'test name test test',
            'is_active' => true,
        ];
        $genre->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function testDelete()
    {
        /** @var Genre $genre */
        $genre = factory(Genre::class, 1)->create(
            [
                'name' => 'name test',
                'is_active' => false,
            ]
        )->first();
        $data = $genre->toArray();
        $genre->delete();
        $this->assertNull(Genre::find($genre->id));
        $this->assertSoftDeleted('genres', $data);
        $genre->restore();
        $this->assertNotNull(Genre::find($genre->id));
    }
}
