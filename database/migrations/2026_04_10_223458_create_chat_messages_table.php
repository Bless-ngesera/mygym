<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();

            // User relationship - who sent/received the message
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade')
                  ->comment('The user who owns this chat message');

            // Message role - who is the sender
            $table->enum('role', ['user', 'assistant'])
                  ->default('user')
                  ->comment('user = member/instructor/admin, assistant = AI response');

            // The actual message content
            $table->text('message')
                  ->comment('The chat message content');

            // Context type for better organization
            $table->string('context_type')
                  ->nullable()
                  ->comment('general, workout, nutrition, schedule, stats');

            // Additional metadata (JSON)
            $table->json('metadata')
                  ->nullable()
                  ->comment('Additional data like tokens, model used, etc.');

            // Timestamps
            $table->timestamps();

            // Indexes for better performance
            $table->index(['user_id', 'created_at']);
            $table->index('role');
            $table->index('context_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
}
