<?php

namespace Tests\Unit\Models;

use App\Models\CastMember;
use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class CastMemberUnitTest extends TestCase
{
    private $castMember;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->castMember = new CastMember();
    }

    public function testFillable()
    {
        $fillable = ['name', 'type'];
        $this->assertEqualsCanonicalizing($fillable, $this->castMember->getFillable());
    }

    public function testIfuseTraits()
    {
        $traits = [
            SoftDeletes::class,
            Uuid::class
        ];
        $castMemberTraits = array_keys(class_uses(Genre::class));
        $this->assertEquals($traits, $castMemberTraits);
    }

    public function testCastsAttribute()
    {
        $casts = ['id' => 'string', 'type' => 'number'];
        $this->assertEqualsCanonicalizing($casts, $this->castMember->getCasts());
    }

    public function testIncrementingAttribute()
    {
        $this->assertFalse($this->castMember->incrementing);
    }

    public function testDatesAttribute()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        foreach ($dates as $date) {
            $this->assertContains($date, $this->castMember->getDates());
        }
        $this->assertCount(count($dates), $this->castMember->getDates());
    }
}