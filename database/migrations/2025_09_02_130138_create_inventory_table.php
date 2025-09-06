<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("inventory", function (Blueprint $table) {
            $table->id();
            $table->string("kode_inventory")->unique()->nullable(); // biarkan nullable kalau mau isi setelah dapat ID
            $table->string("product_name");
            $table->string("type");
            $table->string("quality");
            $table->foreignId("unit_id")->constrained("units"); // RESTRICT lebih aman daripada CASCADE
            $table->decimal("stock", 12, 3)->default(0); // ganti float -> decimal
            $table->decimal("price", 12, 3)->default(0);
            $table->timestamps();

            $table->index(["product_name", "type"]); // opsional, buat pencarian cepat
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("inventory");
    }
};
