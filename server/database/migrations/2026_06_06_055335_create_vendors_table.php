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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('company_name', 255);
            $table->string('vendor_code', 50)->unique();
            $table->string('category', 100)->nullable();
            $table->string('gst_number', 50)->nullable();
            $table->string('contact_person', 150);
            $table->string('email', 190)->index();
            $table->text('address')->nullable();
            $table->string('status', 20)->index();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
