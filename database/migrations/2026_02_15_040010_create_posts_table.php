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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('file_path')->nullable(); //this one can't be null, but for testing purpose we can make it nullable
            $table->string('type');
            // $table->string('department'); //moved to another migration

            $table->string('slug')->nullable();
            //$table->string('short_url')->nullable(); //default
            
            $table->string('short_url', 8)->unique();
            $table->text('excerpt')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->integer('views_count')->nullable()->default(0);
            $table->integer('downloads_count')->nullable()->default(0);
            $table->boolean('is_featured')->nullable()->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
