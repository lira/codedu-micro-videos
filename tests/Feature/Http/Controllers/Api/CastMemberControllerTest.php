<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CastMemberControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    /**
     * @var CastMember
     */
    private $castMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->castMember = factory(CastMember::class)->create(
            [
                'type' => CastMember::TYPE_DIRECTOR
            ]
        );
    }

    public function testIndex()
    {
        $response = $this->get(route('api.cast_members.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$this->castMember->toArray()])
        ;
    }

    public function testShow()
    {
        $response = $this->get(route('api.cast_members.show', ['cast_member' => $this->castMember->id]));

        $response
            ->assertStatus(200)
            ->assertJson($this->castMember->toArray())
        ;
    }

    public function testInvalidationData()
    {
        $data = [
            'name' => '',
            'type' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [
            'name' => str_repeat('a', 256),
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

        $data = [
            'type' => 's'
        ];
        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');
    }

    public function testStore()
    {
        $data = [
            [
                'name' => 'test director',
                'type' => CastMember::TYPE_DIRECTOR,
            ],
            [
                'name' => 'test actor',
                'type' => CastMember::TYPE_ACTOR
            ],
        ];
        foreach ($data as $member) {
            $response = $this->assertStore($member, $member + ['deleted_at' => null]);
            $response->assertJsonStructure(
                [
                    'created_at', 'updated_at'
                ]
            );
        }
    }

    public function testUpdate()
    {
        $this->castMember = factory(CastMember::class)->create(
            [
                'name' => 'test actor',
                'type' => CastMember::TYPE_DIRECTOR,
            ]
        );
        $data = [
            'name' => 'test actor 2',
            'type' => CastMember::TYPE_ACTOR,
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
            'type' => CastMember::TYPE_ACTOR,
        ];
        $this->assertUpdate(
            $data, $data
        );
    }

    public function testDelete()
    {
        /** @var CastMember $castMember */
        $castMember = factory(CastMember::class)->create();

        $id = $castMember->id;

        $response = $this->json(
            'DELETE',
            route('api.cast_members.destroy', ['cast_member' => $id])
        );

        $response->assertStatus(204);

        $response = $this->json(
            'GET',
            route('api.cast_members.show', ['cast_member' => $id])
        );

        $response->assertStatus(404);


        $this->assertNull(CastMember::find($id));
        $this->assertNotNull(CastMember::withTrashed()->find($id));
    }

    protected function routeStore()
    {
        return route('api.cast_members.store');
    }

    protected function routeUpdate()
    {
        return route('api.cast_members.update', ['cast_member' => $this->castMember->id]);
    }

    protected function model()
    {
        return CastMember::class;
    }
}
