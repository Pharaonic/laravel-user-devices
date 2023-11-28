<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDevicesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->morphs('user');
            $table->string('signature');
            $table->foreignId('agent_id');
            $table->foreignId('token_id')->nullable();
            $table->unique(['user_id', 'user_type', 'signature', 'agent_id', 'token_id'], 'UTSAT');

            $table->string('ip');
            $table->string('fcm')->nullable();
            $table->json('data')->nullable();
            $table->boolean('is_primary')->default(false)->index();
            $table->boolean('logged_out')->default(false);
            $table->timestamp('last_action_at');
            $table->timestamps();

            // Relationships
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');

            if (Schema::hasTable('personal_access_tokens')) {
                $table->foreign('token_id')->references('id')->on('personal_access_tokens')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
}
;
