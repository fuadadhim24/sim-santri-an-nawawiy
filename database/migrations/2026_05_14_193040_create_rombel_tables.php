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
        Schema::create('class_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('level_order')->default(0);
            $table->timestamps();
        });

        Schema::create('study_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_level_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('max_capacity')->default(40);
            $table->timestamps();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('class_level_id')->nullable()->after('status')->constrained()->nullOnDelete();
            $table->foreignId('study_group_id')->nullable()->after('class_level_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['class_level_id']);
            $table->dropForeign(['study_group_id']);
            $table->dropColumn(['class_level_id', 'study_group_id']);
        });

        Schema::dropIfExists('study_groups');
        Schema::dropIfExists('class_levels');
    }
};
