<?php

namespace App\Repositories;

use App\Interfaces\CategoryRepositoryInterface;
use App\Models\categories;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all()
    {
        return categories::all();
    }

    public function create(array $data)
    {
        return categories::create($data);
    }

    public function find($id)
    {
        return categories::find($id);
    }

}