<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // student simulator history (was localStorage "emperor-portal:<email>")
        Schema::create('sim_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->unsignedTinyInteger('score');
            $table->unsignedSmallInteger('correct');
            $table->unsignedSmallInteger('total');
            $table->date('taken_on');
            $table->timestamps();
        });

        // imperial workspace datasets (was localStorage "emperor-admin-v1")
        Schema::create('admin_records', function (Blueprint $table) {
            $table->id();
            $table->string('dataset', 32);
            $table->string('key', 64);
            $table->json('data');
            $table->integer('position')->default(0);
            $table->timestamps();
            $table->unique(['dataset', 'key']);
            $table->index(['dataset', 'position']);
        });

        // cms / marketing / seo overrides / rbac overrides
        Schema::create('admin_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->json('value');
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('uid', 16)->unique();
            $table->string('actor');
            $table->string('action');
            $table->string('module', 48);
            $table->text('entity');
            $table->text('detail');
            $table->string('result', 16)->default('success');
            $table->string('at', 16);
            $table->timestamps();
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('subject');
            $table->text('message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('admin_settings');
        Schema::dropIfExists('admin_records');
        Schema::dropIfExists('sim_records');
    }
};
