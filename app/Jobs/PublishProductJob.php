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

    public $draftProduct;
    public $createdBy;
    public $moleculeNames;

    public function __construct(DraftProduct $draftProduct, $createdBy, $moleculeNames)
    {
        $this->draftProduct = $draftProduct;
        $this->createdBy = $createdBy;
        $this->moleculeNames = $moleculeNames;
    }

    public function handle()
    {
        Log::info('Publishing product with ID: ' . $this->moleculeNames);

        PublishProduct::create([
            'name' => $this->draftProduct->name,
            'sales_price' => $this->draftProduct->sales_price,
            'mrp' => $this->draftProduct->mrp,
            'manufacturer_name' => $this->draftProduct->manufacturer_name,
            'is_banned' => $this->draftProduct->is_banned,
            'is_discontinued' => $this->draftProduct->is_discontinued,
            'is_assured' => $this->draftProduct->is_assured,
            'is_refrigerated' => $this->draftProduct->is_refrigerated,
            'category_id' => $this->draftProduct->category_id,
            'ws_code' => $this->draftProduct->ws_code,
            'combination' => $this->moleculeNames, 
            'created_by' => $this->createdBy
        ]);
    }
}
