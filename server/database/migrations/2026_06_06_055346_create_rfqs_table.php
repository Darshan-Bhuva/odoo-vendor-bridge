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
        Schema::create('rfqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users');
            $table->string('rfq_number', 50)->unique();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->dateTime('deadline')->index();
            $table->string('status', 30)->index();
            $table->unsignedBigInteger('selected_quotation_id')->nullable();
            $table->timestamps();

            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfqs');
    }
};
