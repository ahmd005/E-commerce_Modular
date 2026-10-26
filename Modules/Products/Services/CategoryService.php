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

    public function getCategoryById($uuid)
    {
        return $this->categoryRepository->findById($uuid);
    }

    public function createCategory(array $data)
    {
        $data['slug'] = Str::slug($data['name']);
        return $this->categoryRepository->create($data);
    }

    public function updateCategory($uuid, array $data)
    {
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        return $this->categoryRepository->update($uuid, $data);
    }

    public function deleteCategory($uuid)
    {
        return $this->categoryRepository->delete($uuid);
    }
}