<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_Molecule extends Model
{
    use HasFactory;

    protected $table = 'product_molecules';

    protected $fillable = [
        'product_id',
        'molecule_id',
    ];

    public function product()
    {
        return $this->belongsTo(draft_products::class, 'product_id');
    }

    public function molecule()
    {
        return $this->belongsTo(molecules::class, 'molecule_id');
    }
}