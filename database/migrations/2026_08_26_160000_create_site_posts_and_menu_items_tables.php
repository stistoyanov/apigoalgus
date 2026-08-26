<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_posts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('site_id')->index();
            $table->string('source', 20)->default('manual'); // manual | instagram
            $table->string('external_id', 80)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamp('posted_at')->nullable();
            $table->string('title_bg', 255)->nullable();
            $table->string('title_en', 255)->nullable();
            $table->text('excerpt_bg')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->string('permalink', 500)->nullable();
            $table->unsignedBigInteger('media_id')->nullable()->index();
            $table->string('image_url', 500)->nullable();
            $table->timestamps();
            $table->unique(['site_id', 'external_id']);
            $table->index(['site_id', 'is_published', 'sort_order']);
        });

        Schema::create('site_menu_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('site_id')->index();
            $table->string('category', 40)->default('mains'); // brunch | mains
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->string('title_bg', 255);
            $table->string('title_en', 255);
            $table->text('desc_bg')->nullable();
            $table->text('desc_en')->nullable();
            $table->unsignedBigInteger('media_id')->nullable()->index();
            $table->timestamps();
            $table->index(['site_id', 'category', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_menu_items');
        Schema::dropIfExists('site_posts');
    }
};
