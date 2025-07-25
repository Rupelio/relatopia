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
        Schema::create('lista_desejos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('link_compra')->nullable();
            $table->decimal('preco_estimado', 10, 2)->nullable();
            $table->enum('prioridade', ['baixa', 'media', 'alta'])->default('media');
            $table->boolean('comprado')->default(false);
            $table->foreignId('comprado_por')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamp('data_compra')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lista_desejos');
    }
};
