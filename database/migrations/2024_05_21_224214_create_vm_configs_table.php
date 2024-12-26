<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVmConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vm_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name');       // Nombre de la máquina virtual
            $table->string('box');        // Nombre de la caja de Vagrant (e.g., 'ubuntu/bionic64')
            $table->integer('memory');    // Cantidad de memoria RAM en MB
            $table->integer('cpus');      // Número de CPUs
            $table->integer('storage');   // Tamaño del almacenamiento en GB
            $table->timestamps();         // Timestamps para created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vm_configs');
    }
}
