<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('external_id');
            $table->foreignId('source_id')->constrained('sources')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('url', 500)->unique();
            $table->string('image_url', 500)->nullable();
            $table->string('author')->nullable();
            $table->timestamp('published_at');
            $table->timestamps();

            // Indexes
            $table->unique(['external_id', 'source_id']);
            $table->index('published_at');
            $table->index('source_id');
            $table->index('author');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
