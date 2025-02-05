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
        if ($draftProduct) {
            $draftProduct->update($data);
            return $draftProduct;
        }
        return null;
    }

    public function delete($id)
    {
        // dump($id);

        $draftProduct = draft_products::find($id);

        if ($draftProduct) {
            $draftProduct->is_active = false;
            $draftProduct->save();
            return $draftProduct;
        }
        return null;
    }
}
