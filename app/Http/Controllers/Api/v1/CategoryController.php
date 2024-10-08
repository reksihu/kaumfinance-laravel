<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Category;
use App\Http\Requests\v1\StoreCategoryRequest;
use App\Http\Requests\v1\UpdateCategoryRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\CategoryResource;
use App\Http\Resources\v1\CategoryCollection;
use App\Filters\v1\CategoriesFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new CategoriesFilter();
        $queryItems = $filter->transform($request);

        $category = Category::where($queryItems)->get();
        return new CategoryCollection($category);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}
