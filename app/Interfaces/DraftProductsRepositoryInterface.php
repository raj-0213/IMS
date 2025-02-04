<?php

namespace App\Interfaces;

use App\Models\draft_products;

interface DraftProductsRepositoryInterface
{
    public function all();
    public function create(array $data);
    public function find($id);
    public function update(array $data, $id);
    public function delete($id);
}