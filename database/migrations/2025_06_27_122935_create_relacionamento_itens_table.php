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
        Schema::create('relacionamento_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('usuarios')->onDelete('cascade');
            $table->enum('categoria', [
                'reclamacoes',
                'positivos',
                'meus_desejos',
                'nossos_desejos',
                'melhorar_mim',
                'melhorar_juntos'
            ]);
            $table->text('descricao');
            $table->boolean('resolvido')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relacionamento_itens');
    }
};
