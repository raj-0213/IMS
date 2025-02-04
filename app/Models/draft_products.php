<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class draft_products extends Model
{
    use HasFactory;

    protected $table = 'draft_products';

    protected $fillable = [
        'product_name',
        'product_code',
        'manufacture_name',
        'category',
        'mrp',
        'is_banned',
        'is_discontinued',
        'is_active',
        'is_assured',
        'is_refrigerated',
        'is_published',
        'molecules',
        'is_activated_by',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
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
}