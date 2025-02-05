<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class draft_products extends Model
{
    use HasFactory;

    protected $table = 'draft_products';

    protected $fillable = [
        'name',
        'sales_price',
        'mrp',
        'manufacturer_name',
        'is_active',
        'is_banned',
        'is_discontinued',
        'is_assured',
        'is_refrigerated',
        'created_by',
        'updated_by',
        'deleted_by',
        'category_id',
        'product_status',
        'ws_code',
        'combination',
        'published_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'published_at',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function publishedBy()
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}