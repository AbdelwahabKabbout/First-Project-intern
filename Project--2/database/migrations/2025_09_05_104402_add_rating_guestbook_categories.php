<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guestbook_entries', function (Blueprint $table) {
            $table->enum('rate', ['happy', 'smile', 'neutral', 'sad', 'angry'])
                ->default('happy')
                ->after('name')
                ->comment('Rating from comments');
        });
    }

    public function down(): void
    {
        Schema::table('guestbook_entries', function (Blueprint $table) {
            $table->dropColumn('rate');
        });
    }
};
