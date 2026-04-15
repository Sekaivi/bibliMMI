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
        Schema::create('ouvrages', function (Blueprint $table) {
            $table->string('titre') ;
            $table->string('auteur') ;
            $table->string('editeur') ;
            $table->string('couverture')->nullable() ;
            $table->string('serie')->nullable() ;
            $table->unsignedSmallInteger('pages');
            $table->date('date_publication') ;
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ouvrages');
    }
};
