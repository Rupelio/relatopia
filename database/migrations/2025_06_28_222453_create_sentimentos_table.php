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
        Schema::create('sentimentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('usuarios')->onDelete('cascade');
            $table->timestamp('horario');
            $table->enum('tipo_sentimento', [
                'feliz', 'empolgado', 'grato', 'calmo', 'confiante', 'amoroso', 'esperancoso',
                'triste', 'ansioso', 'raiva', 'frustrado', 'preocupado', 'sozinho', 'estressado',
                'confuso', 'cansado', 'nostalgico', 'entediado'
            ]);
            $table->integer('nivel_intensidade')->unsigned()->between(1, 10);
            $table->text('descricao');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sentimentos');
    }
};
