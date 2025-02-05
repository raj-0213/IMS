<?php

namespace App\Jobs;

use App\Models\draft_products as DraftProduct;
use App\Models\Published_products as PublishProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PublishProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $draftProduct;
    protected $userId;
    protected $moleculeNames;
    public function __construct($draftProduct, $userId, $moleculeNames)
    {
        $this->draftProduct = $draftProduct;
        $this->userId = $userId;
        $this->moleculeNames = $moleculeNames;
    }
    public function handle()
    {
        try {
            Log::info('Processing PublishProductJob', ['draft_product_id' => $this->draftProduct]);
            // Check if product with ws_code already exists
            $publishedProduct = PublishProduct::where('ws_code', $this->draftProduct->ws_code)->first();
            $productData = [
                'ws_code' => $this->draftProduct->ws_code,
                'name' => $this->draftProduct->name,
                'sales_price' => $this->draftProduct->sales_price,
                'mrp' => $this->draftProduct->mrp,
                'manufacturer_name' => $this->draftProduct->manufacturer_name,
                'is_banned' => $this->draftProduct->is_banned,
                'is_active' => $this->draftProduct->is_active,
                'is_discontinued' => $this->draftProduct->is_discontinued,
                'is_assured' => $this->draftProduct->is_assured,
                'is_refridged' => $this->draftProduct->is_refridged,
                'category_id' => $this->draftProduct->category_id,
                'combination' => json_encode($this->moleculeNames),
            ];
            if ($publishedProduct) {
                // Update existing product and set updated_by
                $productData['updated_by'] = $this->userId;
                $publishedProduct->update($productData);
                Log::info('PublishProduct updated successfully', ['ws_code' => $this->draftProduct->ws_code]);
            } else {
                // Create new product and set created_by
                $productData['created_by'] = $this->userId;
                PublishProduct::create($productData);
                Log::info('New PublishProduct created successfully', ['ws_code' => $this->draftProduct->ws_code]);
            }
        } catch (\Exception $e) {
            Log::error('Error in PublishProductJob', [
                'error' => $e->getMessage(),
                'ws_code' => $this->draftProduct->ws_code ?? null
            ]);
            throw $e;
        }
    }
}
