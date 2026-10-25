<?php

namespace Modules\Products\Repositories;

use Illuminate\Http\Request;

interface ProductRepositoryInterface
{
    public function getAll(Request $request);
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}