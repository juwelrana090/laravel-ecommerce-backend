<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_details';

    protected $fillable = [
        'product_id',
        'order_id',
        'unit_price',
        'quantity',
    ];

    // Each order detail belongs to one order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Each order detail belongs to one product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
