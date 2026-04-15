<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('usagers', function (Blueprint $table) {
            $table->dropColumn('api_token');
        });
    }

    public function down()
    {
        Schema::table('usagers', function (Blueprint $table) {
            $table->char('api_token', 60)->nullable()->after('motdepasse');
        });
    }
};
