<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('homepage_slides', function (Blueprint $table) {
            $table->text('title')->change();
            $table->text('image_url')->change();
            $table->text('trailer_url')->change();
        });
    }

    public function down()
    {
        Schema::table('homepage_slides', function (Blueprint $table) {
            $table->string('title', 255)->change();
            $table->string('image_url', 255)->change();
            $table->string('trailer_url', 255)->change();
        });
    }
};
