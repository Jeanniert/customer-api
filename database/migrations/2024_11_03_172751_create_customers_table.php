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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('dni', 45);
            $table->foreignId('id_reg')->constrained('regions')->onDelete('cascade');
            $table->foreignId('id_com')->constrained('communes')->onDelete('cascade');
            $table->string('email', 120);
            $table->string('name', 45);
            $table->string('last_name', 45);
            $table->string('address', 255)->nullable();
            $table->date('date_reg');
            $table->enum('status', ['A', 'I', 'trash'])->default('A');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
