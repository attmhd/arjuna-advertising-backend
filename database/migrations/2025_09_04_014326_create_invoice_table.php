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
        Schema::create("invoice", function (Blueprint $table) {
            $table->id();
            $table->string("invoice_number")->unique();
            $table->string("customer_name");
            $table->foreignId("source_id")->constrained("sumber_pelanggans");
            $table->dateTime("issue_date")->default(now());
            $table->date("due_date");
            $table->decimal("discount")->default(0);
            $table->boolean("tax_enabled")->default(false);
            $table->decimal("grand_total")->default(0);
            $table->foreignId("status_id")->constrained("invoice_statuses");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("invoice");
    }
};
