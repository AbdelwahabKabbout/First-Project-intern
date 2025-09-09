<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('guestbook_entries', function (Blueprint $table) {
            $table->text('tag')->after('message');
        });
    }

    public function down(): void {}
};
