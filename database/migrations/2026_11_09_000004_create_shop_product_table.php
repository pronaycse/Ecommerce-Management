<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot: কোন shop-এ কোন product আছে + সেই shop-এর নিজস্ব price/details/thumbnail
        Schema::create('shop_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2)->nullable();   // null হলে product-এর default price
            $table->text('details')->nullable();           // null হলে product-এর description
            $table->string('thumbnail')->nullable();       // null হলে product-এর default thumbnail
            $table->timestamps();

            $table->unique(['shop_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_product');
    }
};
