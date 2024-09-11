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

    // Define the relationship to the Product model
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Define the relationship to the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
