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
        Schema::create('relacionamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id_1')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('user_id_2')->constrained('usuarios')->onDelete('cascade');
            $table->enum('status', ['pendente', 'ativo', 'recusado'])->default('pendente');
            $table->string('token', 60)->unique()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relacionamentos');
    }
};
