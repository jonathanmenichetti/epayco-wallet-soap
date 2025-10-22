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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('document');
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->decimal('balance', 12, 0)->default(0);
            $table->timestamps();

            // indexes
            $table->index(['document', 'phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
