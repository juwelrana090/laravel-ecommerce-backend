<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = [
        'name',
        'slug',
        'price',
        'sales_count',
    ];

    // Many-to-Many relationship with Category via ProductCategory
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id');
    }

    // One-to-Many relationship with ProductReview
    public function productReviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }
}
