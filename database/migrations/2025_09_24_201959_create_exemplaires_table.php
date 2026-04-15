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
        Schema::create('exemplaires', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ouvrage_id');
            $table->enum('etat', ['neuf', 'à réparer', 'à remplacer', 'retiré'])->default('neuf');
            $table->boolean('disponible')->default(true);
            $table->unsignedBigInteger('emprunteur_id')->nullable() ;
            $table->date('date_retour_souhaitee')->nullable() ;
            $table->boolean('reserve')->nullable() ;
            $table->boolean('renouvellement')->nullable() ;
            $table->foreign('ouvrage_id')->references('id')->on('ouvrages')->onDelete('cascade');
            $table->foreign('emprunteur_id')->references('id')->on('usagers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exemplaires');
    }
};
