<?php

namespace Modules\Products\Repositories;

use Illuminate\Http\Request;

interface ProductRepositoryInterface
{
    public function getAll(Request $request);
    public function findById($uuid);
    public function create(array $data);
    public function update($uuid, array $data);
    public function delete($uuid);
}