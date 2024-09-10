<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'product_categories';
    protected $fillable = [
        'product_id',
        'category_id',
    ];

    // This is a belongsTo relationship because ProductCategory belongs to one Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // This is a belongsTo relationship because ProductCategory belongs to one Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
