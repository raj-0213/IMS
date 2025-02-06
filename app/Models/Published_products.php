<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Published_products extends Model
{
    use HasFactory, SoftDeletes,Searchable;

    protected $table = 'published_products';

    protected $fillable = [
        'ws_code',
        'name',
        'sales_price',
        'mrp',
        'manufacturer_name',
        'is_banned',
        'is_discontinued',
        'is_assured',
        'is_active',
        'is_refrigerated',
        'created_by',
        'updated_by',
        'deleted_by',
        'category_id',
        'combination',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function SearchableAs()
    {
        return 'published_products_index';
    }

    public function toSearchableArray(): array
    {
        $array = [
            // "name" => $this->name,
            // "ws_code" => $this->ws_code
        ];
        return $array;
    }

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
}