<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('body');
            $table->foreignId('user_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->unsignedBigInteger('replies_count')->default(0);
            $table->unsignedBigInteger('views')->default(0);
            $table->boolean('pinned')->default(false);
            $table->boolean('locked')->default(false);
            $table->timestamps();
            $table->index(['category_id', 'replies_count']);
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('threads');
    }
}