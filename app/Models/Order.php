<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'grand_total',
        'shipping_cost',
        'discount',
        'user_id',
    ];

    // One-to-Many relationship with OrderDetail
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    // Each order belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
