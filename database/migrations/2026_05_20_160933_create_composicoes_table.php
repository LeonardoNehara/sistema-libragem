<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('composicoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');

            $table->foreignId('cavalo_id')
                ->constrained('veiculos')
                ->onDelete('cascade');

            $table->foreignId('carreta_1_id')
                ->nullable()
                ->constrained('veiculos')
                ->onDelete('set null');

            $table->foreignId('carreta_2_id')
                ->nullable()
                ->constrained('veiculos')
                ->onDelete('set null');

            $table->date('data_composicao')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('composicoes');
    }
};
