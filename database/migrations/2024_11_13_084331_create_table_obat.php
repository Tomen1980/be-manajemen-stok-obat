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
        Schema::create('obat', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->integer('stok')->default(0);
            $table->integer('min_stok')->default(0);
            $table->integer('harga_jual')->default(0);
            $table->text('deskripsi');
            $table->string('foto');
            $table->bigInteger('kategori_id')->unsigned();
            $table->foreign('kategori_id')->references('id')->on('kategori')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('id_vendor')->unsigned();
            $table->foreign('id_vendor')->references('id')->on('vendor')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obat');
    }
};
