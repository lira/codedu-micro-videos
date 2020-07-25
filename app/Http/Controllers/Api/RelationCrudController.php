<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

abstract class RelationCrudController extends BasicCrudController
{
    abstract protected function handleRelations($model, Request $request);

    public function store(Request $request)
    {
        $validatedData = $this->validate($request, $this->rulesStore());
        $self = $this;
        $object = \DB::transaction(function () use ($request, $validatedData, $self) {
            $object = $self->model()::create($validatedData);
            $self->handleRelations($object, $request);
            return $object;
        });
        $object->refresh();
        return $object;
    }

    public function update(Request $request, $id)
    {
        $validatedData = $this->validate($request, $this->rulesUpdate());
        $self = $this;
        $object = $this->findOrFail($id);
        $object = \DB::transaction(function () use ($request, $validatedData, $self, $object) {
            $object->update($validatedData);
            $self->handleRelations($object, $request);
            return $object;
        });
        $object->refresh();
        return $object;
    }
}
