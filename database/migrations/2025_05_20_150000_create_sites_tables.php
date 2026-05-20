<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('slug', 80)->unique();
            $table->string('name');
            $table->string('domain', 191);
            $table->string('default_locale', 5)->default('bg');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('site_tokens', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('site_id')->index();
            $table->string('name');
            $table->string('token_hash', 64)->unique();
            $table->text('token_encrypted')->nullable();
            $table->string('prefix', 8);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('site_settings', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('site_id')->index();
            $table->string('key', 80);
            $table->json('value')->nullable();
            $table->unique(['site_id', 'key']);
        });

        Schema::create('site_contents', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('site_id')->index();
            $table->string('locale', 5);
            $table->string('section', 40);
            $table->string('field', 60);
            $table->text('value')->nullable();
            $table->unique(['site_id', 'locale', 'section', 'field']);
        });

        Schema::create('site_media', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('site_id')->index();
            $table->string('kind', 10);
            $table->string('purpose', 20);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('original_name');
            $table->string('stored_name', 80);
            $table->string('mime_type', 191)->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('alt_text_bg', 255)->nullable();
            $table->string('alt_text_en', 255)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamps();
            $table->index(['site_id', 'purpose', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_media');
        Schema::dropIfExists('site_contents');
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('site_tokens');
        Schema::dropIfExists('sites');
    }
};
