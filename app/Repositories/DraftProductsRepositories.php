<?php

namespace App\Repositories;

use App\Interfaces\DraftProductsRepositoryInterface;
use App\Models\draft_products;

class DraftProductsRepository implements DraftProductsRepositoryInterface
{
    public function all()
    {
        return draft_products::all();
    }

    public function create(array $data)
    {
        return draft_products::create($data);
    }

    public function find($id)
    {
        return draft_products::find($id);
    }

    public function update(array $data, $id)
    {
        $draftProduct = draft_products::find($id);
        $draftProduct->update($data);
        return $draftProduct;
    }

    public function delete($id)
    {
        $draftProduct = draft_products::find($id);
        $draftProduct->delete();
        return $draftProduct;
    }
}