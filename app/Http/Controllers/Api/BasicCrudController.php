<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

abstract class BasicCrudController extends Controller
{
    protected abstract function model();

    protected abstract function rulesStore();

    private $rules = [
        'name' => 'required|max:255',
        'is_active' => 'boolean',
    ];

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

    public function show(Category $category)
    {
        return $category;
    }

    public function update(Request $request, Category $category)
    {
        $this->validate($request, $this->rules);
        $category->update($request->all());
        $category->refresh();
        return $category;
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->noContent(); // 204 - no content
    }

    protected function findOrFail($id)
    {
        $model = $this->model();
        $key = (new $model)->getRouteKeyName();
        return $this->model()::where($key, $id)->firstOrFail();
    }
}
