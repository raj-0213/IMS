<?php

namespace App\Http\Controllers;


use App\Interfaces\DraftProductsRepositoryInterface;
use App\Jobs\PublishProductJob;
use App\Models\molecules;
use App\Models\Product_Molecule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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
    public function index()
    {
        try {
            $draftProducts = Cache::remember('draft_products', 60, function () {
                return $this->draftProductsRepository->all();
            });
            return response()->json(['status' => 'success', 'data' => $draftProducts], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        dd('Reaching here');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->merge(['created_by' => Auth::id()]);

            // // Fetch molecules and concatenate their names
            // $moleculeIds = $request->input('combination', []);
            // if (!is_array($moleculeIds)) {
            //     $moleculeIds = explode(',', $moleculeIds);
            // }

            // // dump($moleculeIds);

            // $molecules = molecules::whereIn('id', $moleculeIds)
            // ->where('is_active', 'true')
            // ->pluck('molecule_name')
            // ->toArray();

            // // dump($molecules);

            // $combination = implode(' + ', $molecules);

            // // Merge combination into request data
            // $request->merge(['combination' => $combination]);

            $moleculeIds = $request->input('combination', []);
            if (!is_array($moleculeIds)) {
                $moleculeIds = explode(',', $moleculeIds);
            }

            // Fetch molecules and check if they are active
            try {
                $molecules = molecules::whereIn('id', $moleculeIds)
                    ->where('is_active', 'true')
                    ->pluck('molecule_name', 'id')
                    ->toArray();

                // Check if all requested molecules are active and present in the database
                if (count($molecules) !== count($moleculeIds)) {
                    $inactiveOrMissingIds = array_diff($moleculeIds, array_keys($molecules));
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Some molecules are either inactive or not present in the database',
                        'inactive_or_missing_ids' => $inactiveOrMissingIds
                    ], 400);
                }

                $combination = implode(' + ', $molecules);

                // Merge combination into request data
                $request->merge(['combination' => $combination]);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }

            // Create draft product
            $draftProduct = $this->draftProductsRepository->create($request->all());

            // Create entries in product_molecules table
            foreach ($moleculeIds as $moleculeId) {
                Product_Molecule::create([
                    'product_id' => $draftProduct->id,
                    'molecule_id' => $moleculeId,
                ]);
            }

            Cache::put('draft_product_' . $draftProduct->id, $draftProduct, 60);

            return response()->json(['status' => 'success', 'data' => $draftProduct], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $cacheKey = "draft_product_{$id}";

            if (Cache::has($cacheKey)) {
                Log::info("Cache hit for {$cacheKey}");
            } else {
                Log::info("Cache miss for {$cacheKey}");
            }

            $draftProduct = Cache::remember($cacheKey, 6000, function () use ($id) {
                Log::info("Fetching draft product from repository for ID: {$id}");
                $product = $this->draftProductsRepository->find($id);
                Log::info("Fetched draft product: " . json_encode($product));
                // return $this->draftProductsRepository->find($id);
                return $product;
            });

            if (!$draftProduct) {
                return response()->json(['status' => 'error', 'message' => 'Draft product not found'], 404);
            }

            return response()->json(['status' => 'success', 'data' => $draftProduct], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
        // try {
        //         $cacheKey = "draft_product_{$id}";

        //         $draftProduct = Cache::remember($cacheKey, 60, function () use ($id) {
        //             return $this->draftProductsRepository->find($id);
        //         });

        //         if (!$draftProduct) {
        //             return response()->json(['status' => 'error', 'message' => 'Draft product not found'], 404);
        //         }

        //     // $draftProduct = $this->draftProductsRepository->find($id);
        //     // if (!$draftProduct) {
        //     //     return response()->json(['status' => 'error', 'message' => 'Draft product not found'], 404);
        //     // }
        //     return response()->json(['status' => 'success', 'data' => $draftProduct], 200);
        // } catch (\Exception $e) {
        //     return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        // }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Not needed for API
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $draftProduct = $this->draftProductsRepository->find($id);

            if (!$draftProduct) {
                return response()->json(['status' => 'error', 'message' => 'Draft product not found'], 404);
            }

            // Fetch molecule names
            $moleculeIds = explode(',', $request->combination);
            $molecules = molecules::whereIn('id', $moleculeIds)->pluck('molecule_name')->toArray();
            // dump($molecules);
            $combination = implode(' + ', $molecules);

            // Update combination
            $draftProduct->combination = $combination;

            // Check if product status is changed to 'Published'
            if ($draftProduct->product_status === 'Draft' && $request->product_status === 'Published') {
                // Generate Unique WS Code
                $wsCode = rand(100000, 999999);
                $draftProduct->ws_code = $wsCode;
                $draftProduct->product_status = 'Published';
                $createdBy = Auth::id();
                $draftProduct->published_at = now();
                $draftProduct->published_by = Auth::id();
                $draftProduct->save();

                // Dispatch job and pass molecule_name
                PublishProductJob::dispatch($draftProduct, $createdBy, $combination);


                return response()->json(['status' => 'success', 'message' => 'Product publishing is in progress'], 200);
            }

            // If status is not changing to 'Published', just update the product
            $draftProduct->update($request->all());

            return response()->json(['status' => 'success', 'data' => $draftProduct], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // public function update(Request $request, $id)
    // {
    //     try {
    //         $draftProduct = $this->draftProductsRepository->find($id);

    //         if (!$draftProduct) {
    //             return response()->json(['status' => 'error', 'message' => 'Draft product not found'], 404);
    //         }

    //         // Check if product status is changed to 'Published'
    //         if ($draftProduct->product_status === 'Draft' && $request->product_status === 'Published') {
    //             // Generate Unique WS Code
    //             $wsCode = rand(100000, 999999);
    //             $draftProduct->ws_code = $wsCode;
    //             $draftProduct->save();

    //             // Dispatch job to queue
    //             PublishProductJob::dispatch($draftProduct, auth()->id(), $request->molecule_names);
    //             Log::info('Product publishing');

    //             return response()->json(['status' => 'success', 'message' => 'Product publishing is in progress'], 200);
    //         }

    //         // If status is not changing from 'Draft' to 'Published', just update the draft product
    //         $draftProduct->update($request->all());

    //         // Create entries in product_molecules table
    //         $moleculeIds = array_map('intval', explode(',', $request->molecule_ids));
    //         foreach ($moleculeIds as $moleculeId) {
    //             Product_Molecule::create([
    //                 'product_id' => $draftProduct->id,
    //                 'molecule_id' => $moleculeId,
    //             ]);
    //         }

    //         return response()->json(['status' => 'success', 'data' => $draftProduct], 201);
    //     } catch (\Exception $e) {
    //         return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    //     }
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {

            // dump($id);

            $draftProduct = $this->draftProductsRepository->delete($id);

            // dump($draftProduct);

            if (!$draftProduct) {
                return response()->json(['status' => 'error', 'message' => 'Draft product not found'], 404);
            }
            return response()->json(['status' => 'success', 'message' => 'Draft product deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
