<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'total_amount',
        'refund_status',
        'customer_name',
        'customer_phone',
        'discount_type',
        'discount_value',
        'payment_method',
        'subtotal',
        'discount',
        'shipping_fees',
        'address'
    ];
    public $timestamps = true;

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    protected $casts = [
        'refund_status' => 'string'
    ];

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function getHasRefundAttribute()
    {
        return in_array($this->refund_status, ['partial_refund', 'full_refund']);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
