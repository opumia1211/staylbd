<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('omnichannel_message_attachments')) {
            return;
        }
        Schema::create('omnichannel_message_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_id');
            $table->string('attachment', 255);
            $table->string('original_name', 255)->nullable();
            $table->string('mime_type', 128)->nullable();
            $table->timestamps();
            $table->foreign('message_id')->references('id')->on('omnichannel_messages')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omnichannel_message_attachments');
    }
};
