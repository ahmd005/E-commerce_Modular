<?php

namespace Modules\Products\Repositories;

use Modules\Products\Models\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all()
    {
        return Category::orderBy('id', 'desc')->get();
    }


    public function findById($uuid)
    {
        return Category::where('uuid', $uuid)->firstOrFail();
    }

    public function create(array $data)
    {
        return Category::create($data);
    }

   
    public function update($uuid, array $data)
    {
        $category = Category::where('uuid', $uuid)->firstOrFail();
        $category->update($data);
        return $category;
    }

    
    public function delete($uuid)
    {
        $category = Category::where('uuid', $uuid)->firstOrFail();
        return $category->delete();
    }
}