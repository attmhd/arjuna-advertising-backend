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
        Schema::create("invoices", function (Blueprint $table) {
            $table->id();
            $table->string("invoice_number")->unique()->nullable();
            $table->string("customer_name");
            $table->string("source");
            $table->dateTime("issue_date");
            $table->date("due_date");
            $table->decimal("discount", 15, 2)->default(0);
            $table->decimal("down_payment", 15, 2)->default(0);
            $table->boolean("tax_enabled")->default(false);
            $table->decimal("grand_total", 15, 2)->default(0);
            $table->string("status");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("invoices");
    }
};
