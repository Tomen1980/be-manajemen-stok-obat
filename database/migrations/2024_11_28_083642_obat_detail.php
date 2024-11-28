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
        Schema::create('obat_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('stok')->default(0);
            $table->integer('harga_beli_unit');
            $table->date('tgl_kadaluwarsa');
            $table->date('tgl_masuk');
            $table->bigInteger('id_obat')->unsigned();
            $table->foreign('id_obat')->references('id')->on('obat')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
