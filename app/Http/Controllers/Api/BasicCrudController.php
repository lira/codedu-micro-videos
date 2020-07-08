<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

abstract class BasicCrudController extends Controller
{
    protected abstract function model();

    protected abstract function rulesStore();

    protected abstract function rulesUpdate();

    public function index()
    {
        return $this->model()::all();
    }

    public function store(Request $request)
    {
        $validatedData = $this->validate($request, $this->rulesStore());
        $object = $this->model()::create($validatedData);
        $object->refresh();
        return $object;
    }

    public function show($id)
    {
        return $this->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $this->validate($request, $this->rulesUpdate());
        $object = $this->findOrFail($id);
        $object->update($validatedData);
        $object->refresh();
        return $object;
    }

    public function destroy($id)
    {
        $object = $this->findOrFail($id);
        $object->delete();
        return response()->noContent(); // 204 - no content
    }

    protected function findOrFail($id)
    {
        $model = $this->model();
        $key = (new $model)->getRouteKeyName();
        return $this->model()::where($key, $id)->firstOrFail();
    }
}
