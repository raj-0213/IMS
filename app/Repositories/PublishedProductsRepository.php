<?php

namespace App\Repositories;

use App\Interfaces\PublishedProductsRepositoryInterface;
use App\Models\published_products as PublishedProduct;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class PublishedProductsRepository implements PublishedProductsRepositoryInterface
{
    public function all()
    {
        try {
            // return PublishedProduct::where('is_active', true)->get();
            return PublishedProduct::all();
        } catch (\Exception $e) {
            Log::error('Error fetching all draft products: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching all Publish products'], 500);
        }
    }

    public function find($id)
    {
        try {
            // $draftProduct = PublishedProduct::where('is_active', true)->where('id', $id)->first();
            $draftProduct = PublishedProduct::find($id);
            if (!$draftProduct) {
                return response()->json(['error' => 'Draft product not found'], 404);
            }
            return $draftProduct;
        } catch (ModelNotFoundException $e) {
            Log::warning('Publish Product not found: ' . $e->getMessage());
            return response()->json(['error' => 'Draft product not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching Publish product by ID: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching draft product by ID'], 500);
        }
    }

    // public function create(array $data)
    // {
    //     try {
    //         return PublishedProduct::create($data);
    //     } catch (\Exception $e) {
    //         Log::error('Error creating draft product: ' . $e->getMessage());
    //         return response()->json(['error' => 'Error creating draft product'], 500);
    //     }
    // }

    // public function update($id, array $data)
    // {
    //     try {
    //         $draftProduct = PublishedProduct::where('is_active', true)->where('id', $id)->first();
    //         if (!$draftProduct) {
    //             return response()->json(['error' => 'Draft product not found'], 404);
    //         }
    //         $draftProduct->update($data);
    //         return $draftProduct;
    //     } catch (ModelNotFoundException $e) {
    //         Log::warning('Draft product not found: ' . $e->getMessage());
    //         return response()->json(['error' => 'Draft product not found'], 404);
    //     } catch (\Exception $e) {
    //         Log::error('Error updating draft product: ' . $e->getMessage());
    //         return response()->json(['error' => 'Error updating draft product'], 500);
    //     }
    // }

    // public function delete($id)
    // {
    //     try {
    //         $draftProduct = PublishedProduct::where('is_active', true)->where('id', $id)->first();
    //         if (!$draftProduct) {
    //             return response()->json(['error' => 'Draft product not found'], 404);
    //         }
    //         $draftProduct->delete();
    //         return response()->json(['success' => 'Draft product deleted'], 200);
    //     } catch (ModelNotFoundException $e) {
    //         Log::warning('Draft product not found: ' . $e->getMessage());
    //         return response()->json(['error' => 'Draft product not found'], 404);
    //     } catch (\Exception $e) {
    //         Log::error('Error deleting draft product: ' . $e->getMessage());
    //         return response()->json(['error' => 'Error deleting draft product'], 500);
    //     }
    // }
}