<?php

namespace App\Http\Controllers;

use App\Models\Published_products as PublishedProduct;
use App\Http\Requests\StorePublished_productsRequest;
use App\Http\Requests\UpdatePublished_productsRequest;
use App\Repositories\PublishedProductsRepository;
use App\Interfaces\PublishedProductsRepositoryInterface;
use Illuminate\Http\Request;

class PublishedProductsController extends Controller
{

    protected $PublishedProductsRepository;

    public function __construct(PublishedProductsRepositoryInterface $PublishedProductsRepository)
    {
        $this->PublishedProductsRepository = $PublishedProductsRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = $this->PublishedProductsRepository->all();
        return response()->json($products);
    }

    public function search(Request $request)
    {
        $search_results = PublishedProduct::search($request->input('query'))->paginateRaw(10);

        return response()->json(['result' => $search_results]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $product = $this->PublishedProductsRepository->create($request->all());
    //     return response()->json($product, 201);
    // }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = $this->PublishedProductsRepository->find($id);
        return response()->json($product);
    }
}
