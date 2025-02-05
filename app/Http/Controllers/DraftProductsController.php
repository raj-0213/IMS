<?php

namespace App\Http\Controllers;


use App\Interfaces\DraftProductsRepositoryInterface;
use App\Models\published_products as PublishedProduct;
use App\Jobs\PublishProductJob;
use App\Models\molecules;
use App\Models\Product_Molecule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Storedraft_productsRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

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
            $draftProducts = Cache::remember('draft_products', 6000, function () {
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
        // dd('Reaching here');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Storedraft_productsRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['created_by'] = Auth::id();

            // Process molecule combination
            $moleculeIds = explode(',', $validatedData['combination'] ?? '');
            $molecules = Molecules::whereIn('id', $moleculeIds)
                ->where('is_active', true)
                ->pluck('molecule_name', 'id')
                ->toArray();

            if (count($molecules) !== count($moleculeIds)) {
                $inactiveOrMissingIds = array_diff($moleculeIds, array_keys($molecules));
                return response()->json([
                    'status' => 'error',
                    'message' => 'Some molecules are either inactive or missing in the database',
                    'inactive_or_missing_ids' => $inactiveOrMissingIds
                ], 400);
            }

            $validatedData['combination'] = implode(' + ', $molecules);

            // Create draft product
            $draftProduct = $this->draftProductsRepository->create($validatedData);

            // Insert product-molecule relationships
            foreach ($moleculeIds as $moleculeId) {
                Product_Molecule::create([
                    'product_id' => $draftProduct->id,
                    'molecule_id' => $moleculeId,
                ]);
            }

            // Cache the product for faster access
            Cache::put('draft_product_' . $draftProduct->id, $draftProduct, 6000);

            return response()->json(['status' => 'success', 'data' => $draftProduct], 201);
        } catch (ValidationException $e) {
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
 
             // $validated = $request->validate([
                 // 'name' => 'required|string|max:255',
                 // 'sales_price' => 'required|numeric|min:0',
                 // 'mrp' => 'required|numeric|min:0',
                 // 'manufacturer_name' => 'required|string|max:255',
                 // 'is_banned' => 'required|boolean',
                 // 'is_active' => 'required|boolean',
                 // 'is_discontinued' => 'required|boolean',
                 // 'is_assured' => 'required|boolean',
                 // 'is_refridged' => 'required|boolean',
                 // 'category_id' => 'required|integer|exists:categories,id', 
                 // 'product_status' => 'nullable|string|in:Draft,Unpublished,Published',
                 // 'combination' => 'nullable|string', 
             // ]);
 
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
             if ($request->has('product_status') && ($draftProduct->product_status === 'Unpublished') && $request->product_status === 'Published') {
                $updatedBy = Auth::id();
                // dd($draftProduct,$createdBy,$combination);
                // Log::info('Processing Sent');
                $draftProduct->product_status = $request->product_status;
                $draftProduct->fill([
                    'name' => $request['name'],
                    'sales_price' => $request['sales_price'],
                    'mrp' => $request['mrp'],
                    'manufacturer_name' => $request['manufacturer_name'],
                    'is_banned' => $request['is_banned'],
                    'is_active' => $request['is_active'],
                    'is_discontinued' => $request['is_discontinued'],
                    'is_assured' => $request['is_assured'],
                    'is_refridged' => $request['is_refridged'],
                    'category_id' => $request['category_id'],
                ]);
                $draftProduct->save();
                PublishProductJob::dispatch($draftProduct, $updatedBy, $combination);
                return response()->json(['status' => 'success', 'message' => 'Updated Product Publish is in progress'], 200);
            } else if ($request->has('product_status') && ($draftProduct->product_status === 'Draft' || $draftProduct->product_status === 'Unpublished') && $request->product_status === 'Published') {
                $wsCode = rand(100000, 999999);
                $createdBy = Auth::id();
                // dd($draftProduct,$createdBy,$combination);
                // Log::info('Processing Sent');
                $draftProduct->ws_code = $wsCode;
                $draftProduct->product_status = $request->product_status;
                $draftProduct->fill([
                    'name' => $request['name'],
                    'sales_price' => $request['sales_price'],
                    'mrp' => $request['mrp'],
                    'manufacturer_name' => $request['manufacturer_name'],
                    'is_banned' => $request['is_banned'],
                    'is_active' => $request['is_active'],
                    'is_discontinued' => $request['is_discontinued'],
                    'is_assured' => $request['is_assured'],
                    'is_refridged' => $request['is_refridged'],
                    'category_id' => $request['category_id'],
                ]);
                $draftProduct->save();
                PublishProductJob::dispatch($draftProduct, $createdBy, $combination);
                return response()->json(['status' => 'success', 'message' => 'Product publishing is in progress'], 200);
            } else if ($draftProduct->product_status === 'Published' || $draftProduct->product_status === 'Unpublished') {
                $draftProduct->product_status = "Unpublished";
                $draftProduct->fill([
                    'name' => $request['name'],
                    'sales_price' => $request['sales_price'],
                    'mrp' => $request['mrp'],
                    'manufacturer_name' => $request['manufacturer_name'],
                    'is_banned' => $request['is_banned'],
                    'is_active' => $request['is_active'],
                    'is_discontinued' => $request['is_discontinued'],
                    'is_assured' => $request['is_assured'],
                    'is_refridged' => $request['is_refridged'],
                    'category_id' => $request['category_id'],
                ]);
                $draftProduct->save();
            }
 
             // If status is not changing to 'Published', just update the product
             $draftProduct->update($request->all());
            //  Cache::forget('draft_products');
            //  Cache::forget('draft_product_' . $id);   
            return response()->json(['status' => 'success', 'data' => $draftProduct], 200);
         } catch (\Exception $e) {
             return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
         }
     }

    // public function update(Request $request, $id)
    // {
    //     try {

    //         // $validated = $request->validate([
    //             // 'name' => 'required|string|max:255',
    //             // 'sales_price' => 'required|numeric|min:0',
    //             // 'mrp' => 'required|numeric|min:0',
    //             // 'manufacturer_name' => 'required|string|max:255',
    //             // 'is_banned' => 'required|boolean',
    //             // 'is_active' => 'required|boolean',
    //             // 'is_discontinued' => 'required|boolean',
    //             // 'is_assured' => 'required|boolean',
    //             // 'is_refridged' => 'required|boolean',
    //             // 'category_id' => 'required|integer|exists:categories,id', 
    //             // 'product_status' => 'nullable|string|in:Draft,Unpublished,Published',
    //             // 'combination' => 'nullable|string', 
    //         // ]);

    //         $draftProduct = $this->draftProductsRepository->find($id);

    //         if (!$draftProduct) {
    //             return response()->json(['status' => 'error', 'message' => 'Draft product not found'], 404);
    //         }

    //         // Fetch molecule names
    //         $moleculeIds = explode(',', $request->combination);
    //         $molecules = molecules::whereIn('id', $moleculeIds)->pluck('molecule_name')->toArray();
    //         // dump($molecules);
    //         $combination = implode(' + ', $molecules);

    //         // Update combination
    //         $draftProduct->combination = $combination;

    //         // Check if product status is changed to 'Published'
    //         if ($request->has('product_status')&&($draftProduct->product_status === 'Draft'|| $draftProduct->product_status === 'Unpublished') && $request->product_status === 'Published' ) {
    //             // Generate Unique WS Code
    //             $wsCode = rand(100000, 999999);
    //             $draftProduct->ws_code = $wsCode;
    //             $draftProduct->product_status = $request->product_status;
    //             $createdBy = Auth::id();
    //             $draftProduct->published_at = now();
    //             $draftProduct->published_by = Auth::id();
    //             $draftProduct->fill([
    //                 'name' => $request['name'],
    //                 'sales_price' => $request['sales_price'],
    //                 'mrp' => $request['mrp'],
    //                 'manufacturer_name' => $request['manufacturer_name'],
    //                 'is_banned' => $request['is_banned'],
    //                 'is_active' => $request['is_active'],
    //                 'is_discontinued' => $request['is_discontinued'],
    //                 'is_assured' => $request['is_assured'],
    //                 'is_refridged' => $request['is_refridged'],
    //                 'category_id' => $request['category_id'],
    //             ]);
    //             $draftProduct->save();

    //             // Dispatch job and pass molecule_name
    //             PublishProductJob::dispatch($draftProduct, $createdBy, $combination);

    //             return response()->json(['status' => 'success', 'message' => 'Product publishing is in progress'], 200);
    //         }

    //         else if($draftProduct->product_status === 'Published' ){
    //             $draftProduct->product_status = "Unpublished";
    //             $draftProduct->fill([
    //                 'name' => $request['name'],
    //                 'sales_price' => $request['sales_price'],
    //                 'mrp' => $request['mrp'],
    //                 'manufacturer_name' => $request['manufacturer_name'],
    //                 'is_banned' => $request['is_banned'],
    //                 'is_active' => $request['is_active'],
    //                 'is_discontinued' => $request['is_discontinued'],
    //                 'is_assured' => $request['is_assured'],
    //                 'is_refridged' => $request['is_refridged'],
    //                 'category_id' => $request['category_id'],
    //             ]);
    //             $draftProduct->save();
    //             return response()->json(['status' => 'success', 'message' => 'Product unpublished successfully'], 200);
    //         }

    //         // If status is not changing to 'Published', just update the product
    //         $draftProduct->update($request->all());

    //         return response()->json(['status' => 'success', 'data' => $draftProduct], 200);
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
