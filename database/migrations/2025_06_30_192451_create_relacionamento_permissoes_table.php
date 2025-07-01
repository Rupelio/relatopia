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
        Schema::create('relacionamento_permissoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relacionamento_id')->constrained('relacionamentos')->onDelete('cascade');
            $table->string('permissao');
            $table->boolean('valor')->default('false');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relacionamento_permissoes');
    }
};
