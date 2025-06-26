<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->decimal("price", 10, 2);
            $table->decimal("ex_price", 10, 2)->nullable();
            $table->text("description")->nullable();
            $table->text("short_des")->nullable();
           
            
            $table->unsignedBigInteger("category_id");
            $table->unsignedBigInteger("brand_id");

            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            $table->foreign("category_id")->references("id")->on("categories")->onDelete("cascade");
            $table->foreign("brand_id")->nullable()->references("id")->on("brands")->onDelete("cascade");

            $table->integer("qty")->nullable();
            $table->string("sku");
            $table->string("barcode")->nullable();
            $table->integer("status")->default(1);
            $table->enum("featured", ["yes", "no"])->default("no");
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
