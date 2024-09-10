<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;

    protected $table = 'product_reviews';

    protected $fillable = [
        'product_id',
        'user_id',
        'comment',
        'rating',
    ];

    // Each review belongs to a single product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Each review belongs to a single user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
