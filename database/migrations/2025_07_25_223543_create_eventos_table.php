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
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->datetime('data_evento');
            $table->enum('tipo', ['pessoal', 'compartilhado'])->default('pessoal');
            $table->enum('categoria', ['aniversario', 'encontro', 'viagem', 'comemoração', 'compromisso', 'outro'])->default('outro');
            $table->boolean('notificar_email')->default(true);
            $table->integer('notificar_minutos_antes')->default(60); // minutos antes do evento
            $table->boolean('notificacao_enviada')->default(false);
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('relacionamento_id')->nullable()->constrained('relacionamentos')->onDelete('cascade'); // para eventos compartilhados
            $table->timestamps();

            // Índices para performance
            $table->index(['usuario_id', 'data_evento']);
            $table->index(['relacionamento_id', 'data_evento']);
            $table->index(['notificacao_enviada', 'data_evento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
