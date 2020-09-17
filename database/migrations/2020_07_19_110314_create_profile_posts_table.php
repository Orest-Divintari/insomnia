<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_posts', function (Blueprint $table) {
            $table->id();
            $table->text('body');
            $table->foreignId('profile_owner_id')
                ->references('id')
                ->on('users');
            $table->foreignId('user_id')
                ->references('id')
                ->on('users');;
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
        Schema::dropIfExists('profile_posts');
    }
}
