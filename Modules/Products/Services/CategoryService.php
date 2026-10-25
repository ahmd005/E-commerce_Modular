<?php

namespace Modules\Products\Services;

use Modules\Products\Repositories\CategoryRepositoryInterface;
use Illuminate\Support\Str;

class CategoryService
{
    protected CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories()
    {
        return $this->categoryRepository->all();
    }

    public function getCategoryById($id)
    {
        return $this->categoryRepository->findById($id);
    }

    public function createCategory(array $data)
    {
        $data['slug'] = Str::slug($data['name']);
        return $this->categoryRepository->create($data);
    }

    public function updateCategory($id, array $data)
    {
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        return $this->categoryRepository->update($id, $data);
    }

    public function deleteCategory($id)
    {
        return $this->categoryRepository->delete($id);
    }
}