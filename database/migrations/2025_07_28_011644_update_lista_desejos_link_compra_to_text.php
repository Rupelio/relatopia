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
        Schema::table('lista_desejos', function (Blueprint $table) {
            $table->text('link_compra')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lista_desejos', function (Blueprint $table) {
            $table->string('link_compra', 255)->nullable()->change();
        });
    }
};
