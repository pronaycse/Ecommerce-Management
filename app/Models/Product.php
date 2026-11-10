<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'price', 'thumbnail', 'created_by'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2'];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function shops(): BelongsToMany
    {
        return $this->belongsToMany(Shop::class, 'shop_product')
            ->withPivot('price', 'details', 'thumbnail')
            ->withTimestamps();
    }

    /** default thumbnail URL (request host ব্যবহার করে, তাই APP_URL ভুল থাকলেও ছবি আসবে) */
    public function thumbnailUrl(): ?string
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    }

    // ---- নিচের helper গুলো কাজ করে যখন product টা shop->products() দিয়ে load করা হয় (pivot থাকে) ----

    public function shopPrice()
    {
        $pivot = $this->pivot;
        return ($pivot && $pivot->price !== null) ? $pivot->price : $this->price;
    }

    public function shopDetails(): ?string
    {
        $pivot = $this->pivot;
        return ($pivot && filled($pivot->details)) ? $pivot->details : $this->description;
    }

    public function shopThumbnailUrl(): ?string
    {
        $pivot = $this->pivot;
        $path = ($pivot && $pivot->thumbnail) ? $pivot->thumbnail : $this->thumbnail;
        return $path ? asset('storage/' . $path) : null;
    }
}
