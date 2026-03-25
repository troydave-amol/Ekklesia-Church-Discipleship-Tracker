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
        Schema::create('church_membership', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('church_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['active','inactive','visitor','new_member']);
            $table->date('joined_at')->nullable();
            $table->date('baptized_at')->nullable();
            $table->foreignId('leader_id')->nullable()->constrained('users');
            $table->string('satellite')->nullable();
            $table->enum('destiny_training_level',['life_class','sol1','sol2','sol3','graduate'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('church_membership');
    }
};
