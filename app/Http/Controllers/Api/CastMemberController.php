<?php

namespace App\Http\Controllers\Api;

use App\Models\CastMember;
use Illuminate\Http\Request;

class CastMemberController extends BasicCrudController
{
    private $rules = [];

    public function __construct()
    {
        $this->rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|numeric|in:' . implode(',', [CastMember::TYPE_DIRECTOR, CastMember::TYPE_ACTOR]),
        ];
    }

    protected function model()
    {
        return CastMember::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }
}
