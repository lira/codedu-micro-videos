<?php

namespace Tests\Feature\Models;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CastMemberTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(CastMember::class, 1)->create();
        $castMember = CastMember::all();
        $this->assertCount(1, $castMember);
        $genreKeys = array_keys($castMember->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            ['id', 'name', 'type', 'created_at', 'updated_at', 'deleted_at'],
            $genreKeys
        );
    }

    public function testCreate()
    {
        $uuidPattern = '/[a-f0-9]{8}-?[a-f0-9]{4}-?4[a-f0-9]{3}-?[89ab][a-f0-9]{3}-?[a-f0-9]{12}/i';
        $castMember = CastMember::create(
            [
                'name' => 'test1',
                'type' => 1
            ]
        );
        $castMember->refresh();

        $this->assertEquals('test1', $castMember->name);
        $this->assertEquals(1, $castMember->type);
        $this->assertRegExp($uuidPattern, $castMember->id);
        $this->assertEquals(36, mb_strlen($castMember->id));

        $castMember = CastMember::create(
            [
                'name' => 'test1',
                'type' => 2
            ]
        );
        $castMember->refresh();

        $this->assertEquals('test1', $castMember->name);
        $this->assertEquals(2, $castMember->type);
        $this->assertRegExp($uuidPattern, $castMember->id);
        $this->assertEquals(36, mb_strlen($castMember->id));
    }

    public function testUpdate()
    {
        /** @var CastMember $castMember */
        $castMember = factory(CastMember::class)->create(
            [
                'name' => 'name test',
                'type' => 2,
            ]
        );
        $data = [
            'name' => 'test name test test',
            'type' => 1,
        ];
        $castMember->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $castMember->{$key});
        }
    }

    public function testDelete()
    {
        /** @var CastMember $castMember */
        $castMember = factory(CastMember::class, 1)->create(
            [
                'name' => 'name test',
                'type' => 1,
            ]
        )->first();
        $data = $castMember->toArray();
        $castMember->delete();
        $this->assertNull(CastMember::find($castMember->id));
        $this->assertSoftDeleted('cast_members', $data);
        $castMember->restore();
        $this->assertNotNull(CastMember::find($castMember->id));
    }
}
