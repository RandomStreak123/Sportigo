<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->text('avatar')->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'profile_picture')) {
                $table->string('profile_picture')->nullable();
            }
            if (!Schema::hasColumn('users', 'profile_photo')) {
                $table->string('profile_photo')->nullable();
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'hide_phone')) {
                $table->boolean('hide_phone')->default(false);
            }
            if (!Schema::hasColumn('users', 'theme_preference')) {
                $table->string('theme_preference')->default('system');
            }
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable();
            }
            if (!Schema::hasColumn('users', 'primary_sport')) {
                $table->string('primary_sport')->nullable();
            }
            if (!Schema::hasColumn('users', 'skill_tier')) {
                $table->string('skill_tier')->nullable();
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender', 16)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar', 'profile_picture', 'profile_photo',
                'phone', 'phone_number', 'hide_phone', 'theme_preference',
                'bio', 'primary_sport', 'skill_tier', 'gender'
            ]);
        });
    }
};
