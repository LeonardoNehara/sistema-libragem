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
        Schema::create('checklist_pneus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_id')
                ->constrained('checklists_calibragem')
                ->onDelete('cascade');

            $table->foreignId('veiculo_id')
                ->constrained('veiculos')
                ->onDelete('cascade');

            $table->integer('posicao'); // Ex: 1, 2, 3, 4...
            $table->integer('libragem'); // Ex: 110, 120, 125

            $table->enum('status', [
                'calibrado',
                'baixo',
                'alto',
                'critico'
            ])->nullable();

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
        Schema::dropIfExists('checklist_pneus');
    }
};
