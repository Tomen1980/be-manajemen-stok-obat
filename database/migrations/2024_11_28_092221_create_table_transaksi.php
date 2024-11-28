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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe', ['pembelian', 'penjualan'])->default('pembelian');
            $table->enum('status', ['proses', 'selesai'])->default('proses');
            $table->text('deskripsi');
            $table->integer('total_harga');
            $table->date('tanggal');
            $table->bigInteger('id_pasien')->unsigned()->nullable();
            $table->foreign('id_pasien')->references('id')->on('pasien')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('id_user')->unsigned()->nullable(); // Pastikan tipe data sesuai
            $table->foreign('id_user')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_transaksi');
    }
};
