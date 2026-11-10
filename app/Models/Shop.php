<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Shop extends Model
{
    protected $fillable = ['name', 'description', 'owner_id'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /** এই shop-এ assign করা product (pivot-এ shop-ভিত্তিক price/details/thumbnail) */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'shop_product')
            ->withPivot('price', 'details', 'thumbnail')
            ->withTimestamps();
    }
}