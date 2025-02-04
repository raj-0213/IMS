<?php

namespace App\Http\Controllers;

use App\Models\draft_products;
use App\Http\Requests\Storedraft_productsRequest;
use App\Http\Requests\Updatedraft_productsRequest;
use App\Interfaces\DraftProductsRepositoryInterface;
use Illuminate\Http\JsonResponse;

class DraftProductsController extends Controller
{
    protected $draftProductsRepository;

    public function __construct(DraftProductsRepositoryInterface $draftProductsRepository)
    {
        $this->draftProductsRepository = $draftProductsRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $draftProducts = $this->draftProductsRepository->all();
            return response()->json(['data' => $draftProducts], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch draft products'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('draft_products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Storedraft_productsRequest $request): JsonResponse
    {
        try {
            $draftProduct = $this->draftProductsRepository->create($request->all());
            return response()->json(['data' => $draftProduct], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create draft product'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $draftProduct = $this->draftProductsRepository->find($id);
            return response()->json(['data' => $draftProduct], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Draft product not found'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('draft_products.edit', compact('draftProduct'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Updatedraft_productsRequest $request, $id): JsonResponse
    {
        try {
            $draftProduct = $this->draftProductsRepository->update($request->all(), $id);
            return response()->json(['data' => $draftProduct], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update draft product'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->draftProductsRepository->delete($id);
            return response()->json(['message' => 'Draft product deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete draft product'], 500);
        }
    }
}