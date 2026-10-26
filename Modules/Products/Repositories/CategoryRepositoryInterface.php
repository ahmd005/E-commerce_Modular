<?php

namespace Modules\Products\Repositories;

interface CategoryRepositoryInterface
{
    public function all();
    public function findById($uuid);
    public function create(array $data);
    public function update($uuid, array $data);
    public function delete($uuid);
}