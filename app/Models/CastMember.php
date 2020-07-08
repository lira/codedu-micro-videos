<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CastMember extends Model
{
    use SoftDeletes, Uuid;

    const DIRECTOR = 1;
    const ACTOR = 2;

    protected $fillable = ['name', 'type'];
    protected $dates = ['deleted_at'];
    protected $casts = ['id' => 'string', 'type' => 'number'];
    public $incrementing = false;
}
